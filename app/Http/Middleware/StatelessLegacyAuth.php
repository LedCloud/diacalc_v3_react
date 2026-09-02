<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class StatelessLegacyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('login') && $request->has('pass')) {
            $credentials = [
                'email' => $request->input('login'),
                'password' => $request->input('pass'),
            ];
            if (!empty($credentials['email']) && false === filter_input(INPUT_GET, 'login', FILTER_VALIDATE_EMAIL)) {
                //Try to find the email by name
                $u = User::where('name', $credentials['email'])->first();
                if ($u) {
                    $credentials['email'] = $u->email;
                }
            }
            if (Auth::once($credentials)) {
                return $next($request);
            }
        }

        return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid legacy login or password credentials.'
            ], 401);
    }
}
