<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminUtama
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isAdminUtama()) {
            abort(403, 'Hanya admin utama yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}