<?php

namespace App;


use Illuminate\Support\Facades\Route;

class Helpers
{

    /**
     * @param $route string Route to check
     * @param $true string Value to return if true
     * @param $false string Value to return if false
     * @return string Value returned based on current route
     */
    public static function isRoute($route, $true, $false = "") {
        return Route::currentRouteName() == $route ? $true : $false;
    }

}