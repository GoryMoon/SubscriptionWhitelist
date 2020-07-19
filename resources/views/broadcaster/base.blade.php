@extends('layout.base')

@section('title', '- Broadcaster Settings')

@section('content')
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ \App\Helpers::isRoute('broadcaster', 'active') }}" href="{{ route('broadcaster') }}">Settings</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ \App\Helpers::isRoute('broadcaster.links', 'active') }}" href="{{ route('broadcaster.links') }}">Links</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ \App\Helpers::isRoute('broadcaster.list', 'active') }}" href="{{ route('broadcaster.list') }}">Userlist</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ \App\Helpers::isRoute('broadcaster.stats', 'active') }}" href="{{ route('broadcaster.stats') }}">Stats</a>
        </li>
    </ul>
    @yield('b_content')
@endsection
