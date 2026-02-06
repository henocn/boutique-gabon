<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVideo extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'video_url',
        'texte',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
