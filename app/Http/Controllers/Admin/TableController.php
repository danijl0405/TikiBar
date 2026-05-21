<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TableController extends Controller
{
    public function index(): Response
    {
        $today = Carbon::today()->toDateString();

        $tables = RestaurantTable::query()
            ->withCount(['reservations as upcoming_count' => function ($q) use ($today) {
                $q->whereDate('reservation_date', '>=', $today)
                    ->where('status', '!=', 'cancelada');
            }])
            ->orderBy('code')
            ->get();

        return Inertia::render('Admin/Tables', [
            'tables' => $tables,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        RestaurantTable::create($this->validated($request));

        return redirect()
            ->route('admin.tables.index')
            ->with('success', 'Mesa creada.');
    }

    public function update(Request $request, RestaurantTable $table): RedirectResponse
    {
        $table->update($this->validated($request, $table));

        return redirect()
            ->route('admin.tables.index')
            ->with('success', "Mesa {$table->code} actualizada.");
    }

    public function destroy(RestaurantTable $table): RedirectResponse
    {
        $code = $table->code;
        $table->delete();

        return redirect()
            ->route('admin.tables.index')
            ->with('success', "Mesa {$code} eliminada.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?RestaurantTable $table = null): array
    {
        return $request->validate([
            'code'      => ['required', 'string', 'max:10', Rule::unique('restaurant_tables', 'code')->ignore($table)],
            'capacity'  => ['required', 'integer', 'min:1', 'max:30'],
            'zone'      => ['required', 'in:terraza,interior,chiringuito'],
            'is_active' => ['boolean'],
        ]);
    }
}
