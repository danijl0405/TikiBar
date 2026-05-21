<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\RestaurantTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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

    public function create(Request $request): Response
    {
        $date = $request->input('date');
        $date = $date && ($timestamp = strtotime((string) $date))
            ? date('Y-m-d', $timestamp)
            : now()->toDateString();

        $guests = max(1, min(40, (int) $request->input('guests', 2)));

        return Inertia::render('Reservations/Create', [
            'user'         => Auth::user()->only(['name', 'phone']),
            'zones'        => ['terraza', 'interior', 'chiringuito', 'cualquiera'],
            'turns'        => $this->turns(),
            'availability' => $this->availability($date, $guests),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'contact_name'     => ['required', 'string', 'max:120'],
            'contact_phone'    => ['required', 'string', 'max:30'],
            'reservation_date' => ['required', 'date', 'after_or_equal:today'],
            'reservation_time' => ['required', 'string', Rule::in($this->turns())],
            'adults'           => ['required', 'integer', 'min:1', 'max:20'],
            'children'         => ['nullable', 'integer', 'min:0', 'max:20'],
            'ages'             => ['nullable', 'array'],
            'ages.*'           => ['integer', 'min:0', 'max:120'],
            'zone_preference'  => ['required', 'in:terraza,interior,chiringuito,cualquiera'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        $totalGuests = (int) $data['adults'] + (int) ($data['children'] ?? 0);

        // Pick a free table for that date + turn inside a transaction so two
        // simultaneous requests cannot grab the same one.
        $reservation = DB::transaction(function () use ($data, $totalGuests) {
            $table = RestaurantTable::query()
                ->availableFor($data['reservation_date'], $data['reservation_time'])
                ->where('capacity', '>=', $totalGuests)
                ->when($data['zone_preference'] !== 'cualquiera', function ($query) use ($data) {
                    $query->where('zone', $data['zone_preference']);
                })
                ->orderBy('capacity')
                ->lockForUpdate()
                ->first();

            if (! $table) {
                throw ValidationException::withMessages([
                    'reservation_time' => 'No quedan mesas libres en ese turno para la zona elegida. Prueba con otro turno u otra zona.',
                ]);
            }

            return Auth::user()->reservations()->create([
                ...$data,
                'children'            => $data['children'] ?? 0,
                'restaurant_table_id' => $table->id,
                'status'              => 'confirmada',
            ]);
        });

        $reservation->loadMissing('table');

        return redirect()
            ->route('reservations.index')
            ->with('success', "¡Mesa {$reservation->table->code} reservada en {$reservation->table->zone} para el turno de las {$data['reservation_time']}! Te esperamos.");
    }

    public function destroy(Reservation $reservation): RedirectResponse
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

        // Cancelling frees the table again: availability ignores cancelled rows.
        $reservation->update(['status' => 'cancelada']);

        return redirect()
            ->route('reservations.index')
            ->with('success', 'Reserva cancelada. La mesa vuelve a estar disponible.');
    }

    /**
     * Configured 90-minute reservation turns.
     *
     * @return list<string>
     */
    private function turns(): array
    {
        return array_values((array) config('tikibar.turns', []));
    }

    /**
     * Free tables per turn (and per zone) for a date, for parties of at
     * least $guests.
     *
     * @return array<string, mixed>
     */
    private function availability(string $date, int $guests): array
    {
        $tables = RestaurantTable::query()
            ->where('is_active', true)
            ->where('capacity', '>=', $guests)
            ->get(['id', 'zone']);

        $taken = Reservation::query()
            ->whereDate('reservation_date', $date)
            ->where('status', '!=', 'cancelada')
            ->whereNotNull('restaurant_table_id')
            ->get(['restaurant_table_id', 'reservation_time']);

        $slots = [];

        foreach ($this->turns() as $turn) {
            $takenIds = $taken
                ->filter(fn (Reservation $r) => substr((string) $r->reservation_time, 0, 5) === $turn)
                ->pluck('restaurant_table_id')
                ->all();

            $free = $tables->whereNotIn('id', $takenIds);

            $slots[$turn] = [
                'total' => $free->count(),
                'zones' => [
                    'terraza'     => $free->where('zone', 'terraza')->count(),
                    'interior'    => $free->where('zone', 'interior')->count(),
                    'chiringuito' => $free->where('zone', 'chiringuito')->count(),
                ],
            ];
        }

        return [
            'date'   => $date,
            'guests' => $guests,
            'turns'  => $slots,
        ];
    }
}
