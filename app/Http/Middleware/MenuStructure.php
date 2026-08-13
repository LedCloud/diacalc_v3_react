<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MenuStructure
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $menu = \App\Classes\MenuStructure::getMenuStructure();
        view()->share('menu_structure', $menu);
        return $next($request);
    }
}
