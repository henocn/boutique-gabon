<?php

namespace App\Listeners;

use App\Events\NewOrder;
use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class SendPushNotification
{
    public function handle(NewOrder $event): void
    {
        $order = $event->order;
        $order->loadMissing('product');
        $managerId = $order->product?->manager_id;

        Log::info('SendPushNotification listener called for order #' . $order->id);
        Log::info('Product manager_id: ' . ($managerId ?? 'NULL'));

        // Cibler seulement: tous les admins actifs + l'assistante (manager) du produit commandé
        $subscriptions = PushSubscription::whereHas('user', function ($query) use ($managerId) {
            $query->where('is_active', true)
                ->where(function ($subQuery) use ($managerId) {
                    $subQuery->where('role', User::ROLE_ADMIN);

                    if ($managerId) {
                        $subQuery->orWhere('id', $managerId);
                    }
                });
        })->with('user')->get();

        Log::info('Found ' . $subscriptions->count() . ' push subscriptions');
        
        foreach ($subscriptions as $sub) {
            Log::info('Subscription for user: ' . $sub->user->name . ' (ID: ' . $sub->user->id . ', Role: ' . $sub->user->role . ')');
        }

        if ($subscriptions->isEmpty()) {
            Log::warning('No push subscriptions found for admins/product manager');
            return;
        }

        $auth = [
            'VAPID' => [
                'subject' => config('services.vapid.subject', 'mailto:admin@boutique-gabon.com'),
                'publicKey' => config('services.vapid.public_key'),
                'privateKey' => config('services.vapid.private_key'),
            ],
        ];

        if (empty($auth['VAPID']['publicKey']) || empty($auth['VAPID']['privateKey'])) {
            Log::warning('Push notifications disabled: missing VAPID keys');
            return;
        }

        try {
            $webPush = new WebPush($auth);
        } catch (\ErrorException $e) {
            Log::warning('Push notifications disabled: ' . $e->getMessage());
            return;
        }

        $payload = json_encode([
            'title' => '📦 Nouvelle commande #' . $order->id,
            'body' => "Commande de {$order->client_name} - {$order->quantity}x {$order->product?->name}",
            'icon' => url('/images/logo.png'),
            'badge' => url('/images/badge.png'),
            'tag' => 'new-order-' . $order->id,
            'data' => [
                'url' => route('admin.orders.index'),
                'order_id' => $order->id,
            ],
        ]);

        foreach ($subscriptions as $subscription) {
            try {
                $pushSubscription = Subscription::create([
                    'endpoint' => $subscription->endpoint,
                    'publicKey' => $subscription->public_key,
                    'authToken' => $subscription->auth_token,
                    'contentEncoding' => $subscription->content_encoding ?? 'aesgcm',
                ]);

                $webPush->queueNotification($pushSubscription, $payload);
            } catch (\Exception $e) {
                Log::error('Push notification queue error: ' . $e->getMessage());
            }
        }

        // Envoyer toutes les notifications
        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();

            if (!$report->isSuccess()) {
                // Supprimer la subscription si elle est expirée ou invalide
                if ($report->isSubscriptionExpired()) {
                    PushSubscription::where('endpoint', $endpoint)->delete();
                }

                Log::error('Push notification failed for ' . $endpoint . ': ' . $report->getReason());
            } else {
                Log::info('Push notification sent successfully to ' . substr($endpoint, 0, 50) . '...');
            }
        }
    }
}
