<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
        'phone_code',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_countries')
            ->withPivot('selling_price');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'client_country_id');
    }
}
