<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role !== 'superadmin') {
            abort(403, 'Akses ditolak.');
        }
        return $next($request);
    }
}