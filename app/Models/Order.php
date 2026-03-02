<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $client_name
 * @property string $client_contact
 * @property string|null $client_comment
 * @property string|null $manager_note
 * @property int $product_id
 * @property int $quantity
 * @property \App\Enums\OrderStatus|string $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Order extends Model
{
    protected $fillable = [
        'client_name',
        'client_contact',
        'client_comment',
        'manager_note',
        'product_id',
        'quantity',
        'status',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'quantity' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
