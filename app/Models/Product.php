<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'name',
        'purchase_price',
        'shipping_price',
        'quantity',
        'image',
        'carousel1',
        'carousel2',
        'carousel3',
        'carousel4',
        'carousel5',
        'description',
        'status',
        'manager_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function characteristics(): HasMany
    {
        return $this->hasMany(ProductCharacteristic::class);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(ProductVideo::class);
    }

    public function packs(): HasMany
    {
        return $this->hasMany(ProductPack::class);
    }

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'product_countries')
            ->withPivot('selling_price');
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'product_managers', 'product_id', 'manager_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
