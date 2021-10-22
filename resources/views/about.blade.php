@extends('layout.base')

@section('title', '- About')

@section('content')
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">About</h2>
                <div class="card-text">
                    <p>
                        This site is made by <a href="https://twitter.com/Gory_moon">@Gory_Moon</a> as a hobby project and because a previous site that had this functionality was removed.<br>
                        If you want to donate to help run this site you can do it here <a href="https://paypal.me/GustafJ"><fa style="font-size: x-large" :icon="['fab','cc-paypal']"></fa></a> or <a href="https://www.patreon.com/GoryMoon">Patreon <fa style="font-size: x-large" :icon="['fab','patreon']"></fa></a>, it's not needed as I use this for other personal stuff but it's much appreciated.
                    </p>
                    <p>
                        I will continue to add features and work on this as time goes, if you have suggestions you can post a issue on the github below.
                    </p>
                    <h4>Site info</h4>
                    <h6>Version: @version('simple') (build <a href="https://github.com/GoryMoon/SubscriptionWhitelist/commit/@version('commit')">@version('commit')</a>)</h6>
                    <h6>Source code:</h6>
                    <ul>
                        <li>
                            Main: <a href="https://github.com/GoryMoon/SubscriptionWhitelist"><fa style="font-size: x-large" :icon="['fab','github']"></fa> SubscriberWhitelist</a>
                        </li>
                        <li>
                            API: <a href="https://github.com/GoryMoon/SubscriptionWhitelistApi"><fa style="font-size: x-large" :icon="['fab','github']"></fa> SubscriberWhitelistAPI</a>
                        </li>
                    </ul>
                    <h4>Libraries</h4>
                    <h5>PHP</h5>
                    <ul>
                        <li>
                            <a href="https://laravel.com/">Laravel</a>: Main page framework.
                        </li>
                        <li>
                            <a href="https://lumen.laravel.com/">Lumen</a>: Api page framework.
                        </li>
                        <li>
                            <a href="https://horizon.laravel.com/">laravel/horizon</a>: Dashboard and code-driven configuration for Laravel queues.
                        </li>
                        <li>
                            <a href="https://github.com/laravel/telescope">laravel/telescope</a>: An elegant debug assistant for the Laravel framework.
                        </li>
                        <li>
                            <a href="https://github.com/guzzlehttp/guzzle">guzzlehttp/guzzle</a>: Guzzle is a PHP HTTP client library.
                        </li>
                        <li>
                            <a href="https://github.com/invisnik/laravel-steam-auth">invisnik/laravel-steam-auth</a>: Laravel Steam Auth.
                        </li>
                        <li>
                            <a href="https://github.com/antonioribeiro/version">pragmarx/version</a>: Take control over your Laravel app version.
                        </li>
                        <li>
                            <a href="https://github.com/pusher/pusher-http-php">pusher/pusher-php-server</a>: Library for interacting with the Pusher REST API.
                        </li>
                        <li>
                            <a href="https://github.com/fruitcake/laravel-cors">fruitcake/laravel-cors</a>: Adds CORS (Cross-Origin Resource Sharing) headers support in your Laravel application.
                        </li>
                        <li>
                            <a href="https://github.com/spatie/laravel-csp">spatie/laravel-csp</a>: Set content security policy headers in a Laravel app.
                        </li>
                        <li>
                            <a href="https://github.com/bepsvpt/secure-headers">bepsvpt/secure-headers</a>: Add security related headers to HTTP response. The package includes Service Providers for easy Laravel integration.
                        </li>
                        <li>
                            <a href="https://github.com/romanzipp/Laravel-Twitch">romanzipp/laravel-twitch</a>: Twitch PHP Wrapper for Laravel.
                        </li>
                        <li>
                            <a href="https://github.com/tightenco/ziggy">tightenco/ziggy</a>: Use your Laravel Named Routes inside JavaScript.
                        </li>
                        <li>
                            <a href="https://github.com/Torann/laravel-geoip">torann/geoip</a>: Support for multiple GeoIP services. (Used for cookie displaying)
                        </li>
                        <li>
                            <a href="https://github.com/vinkla/laravel-hashids">vinkla/hashids</a>: A Hashids bridge for Laravel.
                        </li>
                        <li>
                            <a href="https://github.com/laravel/socialite">laravel/socialite</a>: Laravel wrapper around OAuth 1 & OAuth 2 libraries.
                        </li>
                        <li>
                            <a href="https://github.com/SocialiteProviders/Manager">socialiteproviders/manager</a>: Easily add new or override built-in providers in Laravel Socialite.
                        </li>
                        <li>
                            <a href="https://github.com/SocialiteProviders/Twitch">socialiteproviders/twitch</a>: Twitch OAuth2 Provider for Laravel Socialite.
                        </li>
                    </ul>
                    <h5>JavaScript</h5>
                    <ul>
                        <li>
                            <a href="https://github.com/JeffreyWay/laravel-mix">laravel-mix</a>: An elegant wrapper around Webpack for the 80% use case.
                        </li>
                        <li>
                            <a href="https://github.com/sass/dart-sass">sass</a>: The reference implementation of Sass, written in Dart.
                        </li>
                        <li>
                            <a href="https://github.com/vuejs/vue">vue</a>: Reactive, component-oriented view layer for modern web interfaces.
                        </li>
                        <li>
                            <a href="https://github.com/sindresorhus/ky">ky</a>: Tiny and elegant HTTP client based on the browser Fetch API
                        </li>
                        <li>
                            <a href="https://github.com/twbs/bootstrap">bootstrap</a>: Sleek, intuitive, and powerful front-end framework for faster and easier web development.
                        </li>
                        <li>
                            <a href="https://github.com/bootstrap-vue/bootstrap-vue">bootstrap-vue</a>: Bootstrap components for Vue
                        </li>
                        <li>
                            Fontawesome: The iconic font, CSS, and SVG framework
                            <a href="https://github.com/FortAwesome/Font-Awesome">
                                <ul>
                                    <li>
                                        @fortawesome/fontawesome-svg-core
                                    </li>
                                    <li>
                                        @fortawesome/free-brands-svg-icons
                                    </li>
                                    <li>
                                        @fortawesome/free-solid-svg-icons
                                    </li>
                                </ul>
                            </a>
                        </li>
                        <li>
                            <a href="https://github.com/FortAwesome/vue-fontawesome">@fortawesome/vue-fontawesome</a>: Official Vue component for Font Awesome 5
                        </li>
                        <li>
                            <a href="https://github.com/gitbrent/bootstrap-switch-button">bootstrap-switch-button</a>: Bootstrap Switch Button is a bootstrap 4 plugin that converts checkboxes into switch button toggles.
                        </li>
                        <li>
                            <a href="https://github.com/laravel/echo">laravel-echo</a>: Laravel Echo library for beautiful Pusher and Socket.IO integration
                        </li>
                        <li>
                            <a href="https://github.com/lodash/lodash">lodash</a>: Lodash modular utilities.
                        </li>
                        <li>
                            <a href="https://github.com/moment/moment">moment</a>: Parse, validate, manipulate, and display dates
                        </li>
                        <li>
                            <a href="https://github.com/atomiks/tippyjs">tippy.js</a>: The complete tooltip, popover, dropdown, and menu solution for the web
                        </li>
                        <li>
                            <a href="https://github.com/pusher/pusher-js">pusher-js</a>: Pusher JavaScript library for browsers, React Native, NodeJS and web workers
                        </li>
                        <li>
                            <a href="https://github.com/chartjs/Chart.js">chart.js</a>: Simple HTML5 charts using the canvas element.
                        </li>
                        <li>
                            <a href="https://github.com/apertureless/vue-chartjs">vue-chartjs</a>: Vue.js wrapper for chart.js for creating beautiful charts.
                        </li>
                        <li>
                            <a href="https://github.com/Inndy/vue-clipboard2">vue-clipboard2</a>: A Vuejs2 & Vuejs3 binding for clipboard.js
                        </li>
                        <li>
                            <a href="https://github.com/mannyyang/vuetable-3">vuetable-3</a>: Datatable component for Vue 2.x
                        </li>
                    </ul>
                </div>
            </div>
        </div>

@endsection
