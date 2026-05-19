<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        $highlights = Category::query()
            ->whereIn('slug', ['espetos', 'cocteles', 'pescaito'])
            ->with(['items' => function ($q) {
                $q->where('is_available', true)->orderBy('price')->limit(3);
            }])
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Welcome', [
            'phone' => config('tikibar.phone'),
            'highlights' => $highlights,
        ]);
    }
}
