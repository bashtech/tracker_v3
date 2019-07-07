<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MustBeAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->isRole('admin') ||
            $request->user()->isDeveloper()
        ) {
            return $next($request);
        }

        abort(403);
    }
}
