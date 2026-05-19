<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'restaurant_table_id',
    'contact_name',
    'contact_phone',
    'reservation_date',
    'reservation_time',
    'adults',
    'children',
    'ages',
    'zone_preference',
    'status',
    'notes',
])]
class Reservation extends Model
{
    protected function casts(): array
    {
        return [
            'reservation_date' => 'date',
            'ages' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'restaurant_table_id');
    }

    public function totalGuests(): int
    {
        return (int) $this->adults + (int) $this->children;
    }
}
