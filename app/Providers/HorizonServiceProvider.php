<?php

namespace App\Providers;

use App\Utils\TwitchUtils;
use Laravel\Horizon\Horizon;
use Illuminate\Support\Facades\Gate;
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
        Horizon::auth(function ($request) {
            $user = TwitchUtils::getDbUser();
            return app()->environment('local') || (!is_null($user) && $user->uid == config('whitelist.admin_id'));
        });

        Horizon::routeMailNotificationsTo('whitelist@gorymoon.se');
        Horizon::night();
    }
}
