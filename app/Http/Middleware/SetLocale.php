<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config('app.supported_locales', ['en']);
        if (Session::has('locale') && in_array(Session::get('locale'), $supportedLocales)) {
            $locale = Session::get('locale');
        } else {
            // getPreferredLanguage automatically scans the HTTP_ACCEPT_LANGUAGE string
            // and picks the user's best match that is inside your $supportedLocales array
            $locale = $request->getPreferredLanguage($supportedLocales);
        }

        App::setLocale($locale);

        return $next($request);
    }
}
