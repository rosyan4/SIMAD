<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminOPD
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isAdminOPD()) {
            abort(403, 'Hanya admin OPD yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}