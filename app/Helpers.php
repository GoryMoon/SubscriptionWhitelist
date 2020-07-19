<?php

namespace App;

use Route;
use Request;

class Helpers
{

    /**
     * @param $route string Route to check
     * @param $true string Value to return if true
     * @param $false string Value to return if false
     * @return string Value returned based on current route
     */
    public static function isRoute($route, $true, $false = '') {
        return Route::currentRouteName() == $route ? $true : $false;
    }

    public static function isRouteBase($route, $true, $false = '') {
        return explode('.', Route::currentRouteName())[0] == $route ? $true: $false;
    }

    /**
     * @param $row
     * @return string[] Query array
     */
    public static function sortQuery($row) {
        $sort = Request::query('sort');
        $order = Request::query('order', 'desc');
        if ($sort == $row) {
            if ($order == 'desc') {
                $order = 'asc';
            } else {
                $order = 'desc';
            }
        }
        return ['sort' => $row, 'order' => $order];
    }
}
