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
            <p>
                To get started adding a name to whitelists or<br>
                setup your own whitelist you need to authorize with twitch.<br>
            </p>
            <a class="btn btn-primary" href="{{ route('login.authorize') }}"><fa :icon="['fab','twitch']"></fa> Authorize with Twitch</a>
            <span class="small d-block mt-2">
                By authorizing with Twitch you accept our <a href="{{ route('privacy') }}">Privacy Policy &amp; Terms of Service</a>
            </span>
        </div>
    </div>
@endsection