<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Horizon::auth(function (Request $request) {
            $user = $request->user();

            return app()->environment('local') || ( ! is_null($user) && $user->admin);
        });

        Horizon::routeMailNotificationsTo('whitelist@gorymoon.se');
        Horizon::night();
    }
}
