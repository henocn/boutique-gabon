<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class Order extends Model
{
    protected static function booted(): void
    {
        static::saving(function (Order $order): void {
            if (Schema::hasColumn('orders', 'client_nom') && empty($order->client_nom) && ! empty($order->client_name)) {
                $order->client_nom = $order->client_name;
            }

            if (Schema::hasColumn('orders', 'commentaire') && empty($order->commentaire) && ! empty($order->client_comment)) {
                $order->commentaire = $order->client_comment;
            }

            if (Schema::hasColumn('orders', 'statut') && $order->statut === null && $order->status) {
                $order->statut = match ($order->status) {
                    OrderStatus::Delivered => 'livre',
                    OrderStatus::Validated => 'valide',
                    OrderStatus::Cancelled => 'annule',
                    OrderStatus::Processed => 'traite',
                    OrderStatus::Unreachable => 'injoignable',
                    default => 'nouveau',
                };
            }
        });
    }

    protected $fillable = [
        'client_name',
        'client_contact',
        'client_comment',
        'product_id',
        'status',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
