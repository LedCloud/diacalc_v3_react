<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
                // 1. Fetch the exact route name (e.g., 'factors.index' or 'meal')
                $routeName = $request->route() ? $request->route()->getName() : null;
                Log::info('route', [$routeName]);
                if (!$routeName) {
                    return [];
                }

                // 2. Fetch the language files you need based on the route name
                // Example logic: if route is "factors.index", load "lang/en/factors.php"
                $group = explode('.', $routeName)[0]; // Extracts "factors"
                Log::info('group', [$group]);
                //dd($routeName, $group);
                // Return an array where the key matches the file/group name
                // Example: ['factors' => ['title' => 'Add Factor', 'buttons' => ['submit' => 'Save']]]
                /*return [
                    $group => trans($group) ?: []
                ];*/

                // __() automatically loads translation files for the current app locale
                $res = trans($group);
                Log::info('Translated', [$res]);
                //dd($res);
                return trans($group) ?: [];
            }
        ]);

        return $next($request);
    }
}
