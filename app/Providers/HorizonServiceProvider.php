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
            return app()->environment('local') || TwitchUtils::getDbUser()->uid == config('whitelist.admin_id');
        });

        Horizon::routeMailNotificationsTo('whitelist@gorymoon.se');
        Horizon::night();
    }
}
