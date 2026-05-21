<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'tiki' => [
                'phone'      => config('tikibar.phone'),
                'address'    => config('tikibar.address'),
                'email'      => config('tikibar.email'),
                'heroVideo'  => config('tikibar.hero_video'),
                'heroPoster' => config('tikibar.hero_poster'),
            ],
            'auth' => [
                'user' => $request->user()?->only(['id', 'name', 'email', 'phone', 'is_admin']),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
