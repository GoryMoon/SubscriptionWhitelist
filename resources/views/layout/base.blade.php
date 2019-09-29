<!doctype html>
<html class="h-100" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="Description" content="Twitch subscription restricted whitelists">
    <title>Subscriber Whitelist @yield('title')</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('safari-pinned-tab.svg') }}" color="#4b367c">
    <meta name="msapplication-TileColor" content="#4b367c">
    <meta name="theme-color" content="#4b367c">


    <!-- COMMON TAGS -->
    <meta charset="utf-8">
    <title>Subscription Whitelist</title>
    <!-- Search Engine -->
    <meta name="description" content="Twitch subscription restricted whitelists">
    <meta name="image" content="https://whitelist.gorymoon.se/site.png">
    <!-- Schema.org for Google -->
    <meta itemprop="name" content="Subscriber Whitelist">
    <meta itemprop="description" content="Twitch subscription restricted whitelists">
    <meta itemprop="image" content="https://whitelist.gorymoon.se/site.png">
    <!-- Twitter -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="Subscriber Whitelist">
    <meta name="twitter:description" content="Twitch subscription restricted whitelists">
    <meta name="twitter:site" content="@Gory_Moon">
    <!-- Open Graph general (Facebook, Pinterest & Google+) -->
    <meta name="og:title" content="Subscriber Whitelist">
    <meta name="og:description" content="Twitch subscription restricted whitelists">
    <meta name="og:image" content="https://whitelist.gorymoon.se/site.png">
    <meta name="og:url" content="https://whitelist.gorymoon.se/">
    <meta name="og:site_name" content="Subscriber Whitelist">
    <meta name="og:type" content="website">

    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.css" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.js"></script>
    <script>
        window.addEventListener("load", function(){
            window.cookieconsent.initialise({
                "palette": {
                    "popup": {
                        "background": "#000"
                    },
                    "button": {
                        "background": "#4b367c"
                    }
                },
                law: {
                    countryCode: '{{ geoip()->getLocation(Request::ip())->getAttribute('iso_code') }}',
                    regionalLaw: false
                },
                location: false
            })});
    </script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<body class="h-100">
    <div class="h-100 d-flex flex-column" id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-primary mb-5">
            <div class="container">
                <a class="navbar-brand" href="/">Subscriber Whitelist</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto">
                            @if(\App\Utils\TwitchUtils::hasDBUser())
                                <li class="nav-item">
                                    <a class="nav-link {{ \App\Helpers::isRoute('dashboard', 'active') }}" href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                @if(\App\Utils\TwitchUtils::hasSubscribers())
                                    <li class="nav-item">
                                        <a class="nav-link {{ \App\Helpers::isRouteBase('broadcaster', 'active') }}" href="{{ route('broadcaster') }}">Broadcaster</a>
                                    </li>
                                @endif
                                <li class="nav-item">
                                    <a class="nav-link {{ \App\Helpers::isRoute('subscriber', 'active') }}" href="{{ route('subscriber') }}">Subscriber</a>
                                </li>
                                @if(\App\Utils\TwitchUtils::getDbUser()->uid == config('whitelist.admin_id'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('horizon.index', ['view' => 'dashboard']) }}">Horizon</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('telescope') }}">Telescope</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ \App\Helpers::isRoute('admin.stats', 'active') }}" href="{{ route('admin.stats') }}">Stats</a>
                                </li>
                                @endif
                            @endif
                        </ul>
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item">
                                <a class="nav-link {{ \App\Helpers::isRoute('about', 'active') }}" href="{{ route('about') }}">About</a>
                            </li>
                            @if(\App\Utils\TwitchUtils::hasDBUser())
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ \App\Helpers::isRoute('profile', 'active') }}" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ \App\Utils\TwitchUtils::getDbUser()->display_name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a href="{{ route('profile') }}" class="dropdown-item {{ \App\Helpers::isRoute('profile', 'active') }}">Profile</a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link {{ \App\Helpers::isRoute('login', 'active') }}" href="{{ route('login') }}">Login</a>
                                </li>
                            @endif
                        </ul>
                    </div>
            </div>
        </nav>
        <div class="container">
            @if (Session::has('success'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('success') }}
                </div>
            @endif
            @yield('content')
        </div>
        <footer class="d-flex justify-content-center mt-auto">
            <div class="text-center pt-3 px-2 pb-1">
                <span class="text-nowrap">Copyright &copy; {{ date('Y') }} <a href="{{ route('home') }}">Subscription Whitelist</a></span>
                <fa class="text-muted" icon="grip-lines-vertical"></fa> <a href="{{ route('privacy') }}" class="text-nowrap">Privacy Policy & Terms of Service</a>
            </div>
        </footer>
    </div>
    @routes
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
