<?php


namespace App\Http\Middleware;

use App\Utils\TwitchUtils;
use Closure;
use Redirect;
use Session;

class Twitch
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!TwitchUtils::hasUser()) {
            $parts = explode('/', $request->path());
            if ($parts[0] !== 'telescope' && $parts[0] !== 'horizon') {
                Session::put('redirect', $request->fullUrl());
            }
            return Redirect::route('login');
        } else if (is_null(TwitchUtils::getDbUser())) {
            TwitchUtils::logout();
            return Redirect::route('login');
        }

        return $next($request);
    }
}