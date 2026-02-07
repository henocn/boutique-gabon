<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected static function booted(): void
    {
        static::saving(function (Product $product): void {
            if (empty($product->nom)) {
                $product->nom = $product->name;
            }

            if (empty($product->description) && ! empty($product->description_html)) {
                $product->description = $product->description_html;
            }

            if (empty($product->prix) && ! empty($product->price_sell)) {
                $product->prix = $product->price_sell;
            }

            if (empty($product->categorie_id) && ! empty($product->category_id)) {
                $product->categorie_id = $product->category_id;
            }

            if ($product->statut === null) {
                $product->statut = $product->status === ProductStatus::Active ? 'actif' : 'inactif';
            }
        });
    }
    protected $fillable = [
        'name',
        'description_html',
        'price_buy',
        'price_sell',
        'price_shipping',
        'category_id',
        'manager_id',
        'stock',
        'status',
    ];

    protected $casts = [
        'price_buy' => 'decimal:2',
        'price_sell' => 'decimal:2',
        'price_shipping' => 'decimal:2',
        'status' => ProductStatus::class,
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
