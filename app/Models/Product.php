<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description_html
 * @property int|null $price_buy
 * @property int $price_sell
 * @property int|null $price_shipping
 * @property int|null $manager_id
 * @property int $stock
 * @property \App\Enums\ProductStatus|string $status
 * @property array|null $countries
 */
class Product extends Model
{
    protected $fillable = [
        'name',
        'description_html',
        'price_buy',
        'price_sell',
        'price_shipping',
        'manager_id',
        'stock',
        'status',
        'countries',
    ];

    protected $casts = [
        'price_buy' => 'integer',
        'price_sell' => 'integer',
        'price_shipping' => 'integer',
        'status' => ProductStatus::class,
        'countries' => 'array',
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('position');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
