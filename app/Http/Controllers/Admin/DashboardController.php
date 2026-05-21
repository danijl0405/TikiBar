<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Reservation;
use App\Models\RestaurantTable;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $today = Carbon::today()->toDateString();

        $todayReservations = Reservation::query()
            ->whereDate('reservation_date', $today)
            ->where('status', '!=', 'cancelada')
            ->with(['table:id,code,zone,capacity', 'user:id,name'])
            ->orderBy('reservation_time')
            ->get();

        $attention = Reservation::query()
            ->where('status', 'pendiente')
            ->with(['table:id,code,zone', 'user:id,name'])
            ->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->limit(8)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'reservations_today' => $todayReservations->count(),
                'guests_today'       => $todayReservations->sum(fn (Reservation $r) => $r->totalGuests()),
                'pending'            => Reservation::where('status', 'pendiente')->count(),
                'unassigned'         => Reservation::whereNull('restaurant_table_id')
                                            ->where('status', '!=', 'cancelada')
                                            ->count(),
                'reservations_total' => Reservation::count(),
                'tables_active'      => RestaurantTable::where('is_active', true)->count(),
                'tables_total'       => RestaurantTable::count(),
                'menu_unavailable'   => MenuItem::where('is_available', false)->count(),
            ],
            'todayReservations' => $todayReservations,
            'attention'         => $attention,
        ]);
    }
}
