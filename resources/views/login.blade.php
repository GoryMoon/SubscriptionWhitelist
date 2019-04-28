@extends('layout.base')

@section('title', '- Login')

@section('content')
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger" role="alert">{{ $error }}</div>
        @endforeach
    @endif
    <div class="row">
        <div class="mr-auto ml-auto text-center">
            <h1>Login</h1>
            <span class="small d-block mb-2">
                By authorizing with Twitch you accept that information is stored to<br>
                link your twitch account to one or more provided usernames that you<br>
                want to whitelist to one or more broadcasters.<br>
                You can delete your information at anytime in your profile when logged in,<br>
                doing so will remove your username/usernames from any whitelists.
            </span>
            <a class="btn btn-primary" href="{{ route('login.authorize') }}"><fa :icon="['fab','twitch']"></fa> Authorize with Twitch</a>
        </div>
    </div>
@endsection