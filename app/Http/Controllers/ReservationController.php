<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\RestaurantTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ReservationController extends Controller
{
    public function index(): Response
    {
        $reservations = Auth::user()
            ->reservations()
            ->with('table')
            ->orderByDesc('reservation_date')
            ->orderByDesc('reservation_time')
            ->get();

        return Inertia::render('Reservations/Index', [
            'reservations' => $reservations,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Reservations/Create', [
            'user'  => Auth::user()->only(['name', 'phone']),
            'zones' => ['terraza', 'interior', 'chiringuito', 'cualquiera'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'contact_name'     => ['required', 'string', 'max:120'],
            'contact_phone'    => ['required', 'string', 'max:30'],
            'reservation_date' => ['required', 'date', 'after_or_equal:today'],
            'reservation_time' => ['required', 'date_format:H:i'],
            'adults'           => ['required', 'integer', 'min:1', 'max:20'],
            'children'         => ['nullable', 'integer', 'min:0', 'max:20'],
            'ages'             => ['nullable', 'array'],
            'ages.*'           => ['integer', 'min:0', 'max:120'],
            'zone_preference'  => ['required', 'in:terraza,interior,chiringuito,cualquiera'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        $totalGuests = (int) $data['adults'] + (int) ($data['children'] ?? 0);

        $table = RestaurantTable::query()
            ->where('is_active', true)
            ->where('capacity', '>=', $totalGuests)
            ->when($data['zone_preference'] !== 'cualquiera', function ($q) use ($data) {
                $q->where('zone', $data['zone_preference']);
            })
            ->orderBy('capacity')
            ->first();

        $reservation = Auth::user()->reservations()->create([
            ...$data,
            'children'            => $data['children'] ?? 0,
            'restaurant_table_id' => $table?->id,
            'status'              => $table ? 'confirmada' : 'pendiente',
        ]);

        return redirect()
            ->route('reservations.index')
            ->with('success', $table
                ? "¡Mesa {$table->code} reservada en {$table->zone}! Te esperamos."
                : 'Hemos recibido tu solicitud. Te llamaremos para confirmar la mesa.');
    }

    public function destroy(Reservation $reservation): RedirectResponse
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

        $reservation->update(['status' => 'cancelada']);

        return redirect()
            ->route('reservations.index')
            ->with('success', 'Reserva cancelada.');
    }
}
