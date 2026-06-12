<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role !== 'customer') {
            abort(403, 'Trang này chỉ dành cho khách hàng');
        }

        return $next($request);
    }
}
