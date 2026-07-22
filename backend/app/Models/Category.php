<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'short',
        'description',
        'accent',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'short' => $this->short,
            'description' => $this->description,
            'accent' => $this->accent,
        ];
    }
}
