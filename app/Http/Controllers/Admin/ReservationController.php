<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\RestaurantTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReservationController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = [
            'status' => $request->string('status')->toString() ?: null,
            'date'   => $request->string('date')->toString() ?: null,
            'search' => $request->string('search')->toString() ?: null,
        ];

        $reservations = Reservation::query()
            ->with(['table:id,code,zone,capacity', 'user:id,name,email'])
            ->when($filters['status'], fn ($q, $status) => $q->where('status', $status))
            ->when($filters['date'], fn ($q, $date) => $q->whereDate('reservation_date', $date))
            ->when($filters['search'], function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('contact_name', 'like', "%{$search}%")
                        ->orWhere('contact_phone', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('reservation_date')
            ->orderByDesc('reservation_time')
            ->get();

        return Inertia::render('Admin/Reservations', [
            'reservations' => $reservations,
            'tables'       => RestaurantTable::orderBy('code')->get(['id', 'code', 'zone', 'capacity', 'is_active']),
            'filters'      => $filters,
        ]);
    }

    public function update(Request $request, Reservation $reservation): RedirectResponse
    {
        $data = $request->validate([
            'status'              => ['required', 'in:pendiente,confirmada,cancelada'],
            'restaurant_table_id' => ['nullable', 'integer', 'exists:restaurant_tables,id'],
        ]);

        $reservation->update($data);

        return redirect()
            ->back()
            ->with('success', "Reserva de {$reservation->contact_name} actualizada.");
    }
}
