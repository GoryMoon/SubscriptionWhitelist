<?php

namespace App\Http\Middleware;

use App\Utils\TwitchUtils;
use Closure;
use Redirect;

class Broadcaster
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
        if (!TwitchUtils::hasSubscribers()) {
            return Redirect::route('home');
        }
        return $next($request);
    }
}
