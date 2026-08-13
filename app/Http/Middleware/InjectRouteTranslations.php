<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class InjectRouteTranslations
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Inertia::share([
            'translations' => function () use ($request) {
                // 1. Fetch the exact route name (e.g., 'factors.index' or 'dashboard')
                $routeName = $request->route() ? $request->route()->getName() : null;

                if (!$routeName) {
                    return [];
                }

                // 2. Fetch the language files you need based on the route name
                // Example logic: if route is "factors.index", load "lang/en/factors.php"
                $group = explode('.', $routeName)[0]; // Extracts "factors"


                // Return an array where the key matches the file/group name
                // Example: ['factors' => ['title' => 'Add Factor', 'buttons' => ['submit' => 'Save']]]
                /*return [
                    $group => trans($group) ?: []
                ];*/

                // __() automatically loads translation files for the current app locale
                return trans($group) ?: [];
            }
        ]);

        return $next($request);
    }
}
