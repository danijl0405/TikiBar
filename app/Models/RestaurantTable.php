<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'capacity', 'zone', 'is_active'])]
class RestaurantTable extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Active tables with no live (non-cancelled) reservation on the given
     * date and turn. Each table seats one party per 90-minute turn.
     *
     * @param  Builder<RestaurantTable>  $query
     * @return Builder<RestaurantTable>
     */
    public function scopeAvailableFor(Builder $query, string $date, string $time): Builder
    {
        return $query
            ->where('is_active', true)
            ->whereDoesntHave('reservations', function (Builder $reservation) use ($date, $time) {
                $reservation->whereDate('reservation_date', $date)
                    ->where('reservation_time', $time)
                    ->where('status', '!=', 'cancelada');
            });
    }
}
