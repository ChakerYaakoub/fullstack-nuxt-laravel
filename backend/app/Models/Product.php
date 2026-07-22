<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'slug',
        'name',
        'price',
        'short_description',
        'description',
        'material',
        'finish',
        'dimensions',
        'featured',
        'image_tone',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'featured' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function toApiArray(): array
    {
        $this->loadMissing('category');

        return [
            'id' => (string) $this->id,
            'categoryId' => $this->category_id,
            'slug' => $this->slug,
            'name' => $this->name,
            'category' => $this->category?->slug,
            'categoryName' => $this->category?->name,
            'price' => $this->price,
            'shortDescription' => $this->short_description,
            'description' => $this->description,
            'material' => $this->material,
            'finish' => $this->finish,
            'dimensions' => $this->dimensions,
            'featured' => $this->featured,
            'imageTone' => $this->image_tone,
        ];
    }
}
