<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'product_id',
        'pack_id',
        'quantity',
        'unit_price',
        'purchase_price',
        'total_price',
        'client_name',
        'client_country_id',
        'client_phone',
        'client_adress',
        'client_note',
        'manager_id',
        'manager_note',
        'status',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function pack(): BelongsTo
    {
        return $this->belongsTo(ProductPack::class, 'pack_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function clientCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'client_country_id');
    }
}
