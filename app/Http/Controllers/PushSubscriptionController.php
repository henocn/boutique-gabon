<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
    /**
     * Subscribe an endpoint to push notifications.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        PushSubscription::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'endpoint' => $validated['endpoint'],
            ],
            [
                'public_key' => $validated['keys']['p256dh'],
                'auth_token' => $validated['keys']['auth'],
                'content_encoding' => $request->input('contentEncoding', 'aesgcm'),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Subscription saved']);
    }

    /**
     * Unsubscribe an endpoint from push notifications.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|string',
        ]);

        PushSubscription::where('user_id', Auth::id())
            ->where('endpoint', $validated['endpoint'])
            ->delete();

        return response()->json(['success' => true, 'message' => 'Subscription removed']);
    }

    /**
     * Get VAPID public key.
     */
    public function getPublicKey(): JsonResponse
    {
        return response()->json([
            'publicKey' => config('services.vapid.public_key'),
        ]);
    }

    /**
     * Get push configuration status for diagnostics.
     */
    public function status(): JsonResponse
    {
        $publicKey = config('services.vapid.public_key');
        $privateKey = config('services.vapid.private_key');

        return response()->json([
            'hasVapidKeys' => !empty($publicKey) && !empty($privateKey),
            'hasGmp' => extension_loaded('gmp'),
            'hasBcMath' => extension_loaded('bcmath'),
        ]);
    }
}
