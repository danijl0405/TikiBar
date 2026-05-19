<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'category_id',
    'name',
    'description',
    'price',
    'contains_alcohol',
    'is_available',
    'emoji',
])]
class MenuItem extends Model
{
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'contains_alcohol' => 'boolean',
            'is_available' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
