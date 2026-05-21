<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Allow the request through only when the authenticated user is an admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(
            $request->user()?->is_admin,
            403,
            'Acceso restringido al personal del Tiki Bar.',
        );

        return $next($request);
    }
}
