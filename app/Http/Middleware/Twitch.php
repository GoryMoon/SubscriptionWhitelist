<?php


namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Redirect;
use Session;

class Twitch
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            $parts = explode('/', $request->path());
            if ($parts[0] !== 'telescope' && $parts[0] !== 'horizon') {
                Session::put('redirect', $request->fullUrl());
            }
            return Redirect::route('login');
        } else if (is_null(Auth::user())) {
            Auth::logout();
            return Redirect::route('login');
        }

        return $next($request);
    }
}
