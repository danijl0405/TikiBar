<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Inertia\Inertia;
use Inertia\Response;

class MenuController extends Controller
{
    public function __invoke(): Response
    {
        $categories = Category::query()
            ->with(['items' => function ($q) {
                $q->where('is_available', true)->orderBy('price');
            }])
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Menu', [
            'categories' => $categories,
            'phone'      => config('tikibar.phone'),
        ]);
    }
}
