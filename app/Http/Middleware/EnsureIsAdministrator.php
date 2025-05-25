<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureIsAdministrator
{
    public function handle(Request $request, Closure $next)
    {
        if (
            ! auth()->check() ||
            strtolower(auth()->user()->role->name) !== 'administrator'
        ) {
            abort(403, 'Anda tidak memiliki izin mengakses halaman ini.');
        }

        return $next($request);
    }
}
