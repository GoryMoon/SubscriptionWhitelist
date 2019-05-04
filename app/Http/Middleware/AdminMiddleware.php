<?php

namespace App\Http\Middleware;

use App\Utils\TwitchUtils;
use Closure;

class AdminMiddleware
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
        if (TwitchUtils::getDbUser()->uid !== config('whitelist.admin_id')) {
            return redirect()->route('home');
        }
        return $next($request);
    }
}
