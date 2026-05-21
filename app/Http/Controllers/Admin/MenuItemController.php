<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MenuItemController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Menu', [
            'categories' => Category::orderBy('sort_order')->get(['id', 'name', 'type']),
            'items'      => MenuItem::query()
                ->with('category:id,name,type')
                ->orderBy('category_id')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        MenuItem::create($this->validated($request));

        return redirect()
            ->route('admin.menu.index')
            ->with('success', 'Plato añadido a la carta.');
    }

    public function update(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $menuItem->update($this->validated($request));

        return redirect()
            ->route('admin.menu.index')
            ->with('success', "«{$menuItem->name}» actualizado.");
    }

    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        $menuItem->delete();

        return redirect()
            ->route('admin.menu.index')
            ->with('success', "«{$menuItem->name}» eliminado de la carta.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'category_id'      => ['required', 'integer', 'exists:categories,id'],
            'name'             => ['required', 'string', 'max:120'],
            'description'      => ['nullable', 'string', 'max:500'],
            'price'            => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'contains_alcohol' => ['boolean'],
            'is_available'     => ['boolean'],
            'emoji'            => ['nullable', 'string', 'max:8'],
        ]);
    }
}
