@extends('layout.base')


<?php /** @var boolean $isBroadcaster
 * @var boolean $disabled */?>
@section('content')
    <h1>Welcome</h1>
    <div class="row">
        @if($isBroadcaster)
            <div class="col-sm-6 mt-2">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><fa :icon="['fab', 'twitch']" class="text-primary"></fa> Broadcaster</h5>
                        <p class="card-text">Here you can manage your whitelist settings and get the links to give to your subscribers. You can also manage the username list.</p>
                        @if($disabled)
                            <b-alert show>Get started with your whitelist here!</b-alert>
                        @endif
                        <a href="{{ route('broadcaster') }}" class="btn btn-primary">Broadcaster Dashboard</a>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-sm-6 mt-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><fa icon="star" class="text-primary"></fa> Subscriber</h5>
                    <p class="card-text">Here you can manage your whitelisted usernames based on your subscriptions.</p>
                    <a href="{{ route('subscriber') }}" class="btn btn-primary">Subscription Dashboard</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 mt-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><fa icon="user" class="text-primary"></fa> Profile</h5>
                    <p class="card-text">Here you can manage your profile.</p>
                    <a href="{{ route('profile') }}" class="btn btn-primary">Go to Profile</a>
                </div>
            </div>
        </div>
    </div>
@endsection
