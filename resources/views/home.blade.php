@extends('layout.base')


@section('content')
    <section class="jumbotron text-center">
        <div class="container">
            <h1 class="jumbotron-heading">Subscription Whitelist</h1>
            <p class="lead text-muted">Create a list that your twitch subscribers can add their username to and/or<br>Add your name to a list setup by the streamer you watch.</p>
            <p>
                @if($hasUser)
                    <b-button variant="primary" href="{{ route('dashboard') }}">Dashboard</b-button>
                    <p class="small text-muted">Get started by going to the dashboard</p>
                @else
                    <b-button variant="primary" href="{{ route('login') }}"><fa :icon="['fab', 'twitch']"></fa> Login</b-button>
                @endif
            </p>
        </div>
    </section>
    <hr class="featurette-divider">

    <div class="container" style="text-align: center">
        <div class="row">
            <div class="col-lg-4 mb-5">
                <fa style="font-size: xx-large; text-anchor: middle;" :icon="['fab','twitch']"></fa>
                <h2>Subscribers</h2>
                <p>Updates list depending on subscriptions status, can limit list on subscription type: <b-badge variant="primary">Tier 1</b-badge>, <b-badge variant="primary">Tier 2</b-badge> &amp; <b-badge variant="primary">Tier 3</b-badge></p>
                <p>Ability to add custom names to list that always will be present</p>
            </div>
            <div class="col-lg-1 d-lg-none">
                <hr>
            </div>
            <div class="col-lg-4 mb-5">
                <fa style="font-size: xx-large" icon="list"></fa>
                <h2>Formats</h2>
                <p>Supports a bunch of different formats for the list: <b-badge variant="primary">newline</b-badge >, <b-badge variant="primary">csv</b-badge> &amp; <b-badge variant="primary">json array</b-badge></p>
                <p>Minecraft: Verifies name against Mojang and have lists based on the returned username, auto updates on name change &amp; <b-badge variant="primary">minecraft whitelist</b-badge> format</p>
                <p>SteamID: Allows for a list of SteamID64 in the standard formats for use, users need to connect their Steam and then allow the use of the ID on the list.</p>
            </div>
            <div class="col-lg-1 d-lg-none">
                <hr>
            </div>
            <div class="col-lg-4 mb-5">
                <fa style="font-size: xx-large" :icon="['fab','github']"></fa>
                <h2>Open Source</h2>
                <p>This project is open source, the source links can be found on the about page.
                    If you have an issue or want a new feature it's the place for it.</p>
                <p><b-button variant="primary" href="{{ route('about') }}">About Page</b-button></p>
            </div>
        </div>
    </div>
@endsection
