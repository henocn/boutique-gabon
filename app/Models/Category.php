<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected static function booted(): void
    {
        static::saving(function (Category $category): void {
            if (empty($category->nom)) {
                $category->nom = $category->name;
            }

            if (empty($category->image) && ! empty($category->image_path)) {
                $category->image = $category->image_path;
            }

            if ($category->statut === null) {
                $category->statut = $category->is_active ? 'actif' : 'inactif';
            }
        });
    }
    protected $fillable = [
        'name',
        'description',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
