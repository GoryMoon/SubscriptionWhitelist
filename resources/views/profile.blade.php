@extends('layout.base')

@section('title', '- Profile')

<?php
    /** @var string $display_name */
    /** @var string $name */
?>
@section('content')
    <h1><a href="https://twitch.tv/{{ $name }}"><fa :icon="['fab','twitch']"></fa>{{ $display_name }} ({{ $name }})</a></h1>
    <div class="row">
        <div class="col-md-6 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"><fa icon="link"></fa> Connections</h4>
                    <hr>
                    <ul class="list-unstyled">
                        <li>
                            <div class="row">
                                <div class="col">
                                    <h5><fa :icon="['fab','steam']"></fa> Steam</h5>
                                    @if(!is_null($steam))
                                    <a href="{{ $steam->profile_url }}"><fa icon="external-link-alt"></fa> {{ $steam->name }}</a>
                                    @endif
                                </div>
                                <div class="col-4 col-md-4 text-right">
                                    @if(is_null($steam))
                                        <a href="{{ route('auth.steam.link') }}" class="btn btn-primary">Link</a>
                                    @else
                                        <a href="{{ route('auth.steam.unlink') }}" class="btn btn-primary">Unlink</a>
                                    @endif
                                </div>
                            </div>
                            <hr>
                        </li>
                        @if($isBroadcaster)
                        <li>
                            <div class="row">
                                <div class="col">
                                    <h5><fa :icon="['fab','patreon']"></fa> Patreon</h5>
                                    @if(!is_null($patreon))
                                        <a href="{{ $patreon->url }}"><fa icon="external-link-alt"></fa> {{ $patreon->vanity }}</a>
                                    @endif
                                </div>
                                <div class="col-4 col-md-4 text-right">
                                    @if(is_null($patreon))
                                        <a href="{{ route('auth.patreon.link') }}" class="btn btn-primary">Link</a>
                                    @else
                                        <a href="{{ route('auth.patreon.unlink') }}" class="btn btn-primary">Unlink</a>
                                    @endif
                                </div>
                            </div>
                            <hr>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6 mt-2">
            <div class="card text-white border-danger">
                <div class="card-body text-danger">
                    <h4 class="card-title"><fa icon="trash"></fa> Delete Account</h4>
                    <p class="card-text">
                        With this you can delete your account and all the data associated with it.<br>
                        Your username/usernames will be removed from any whitelist you are on.<br>
                        If you are a broadcaster, your whitelist will be removed and all the usernames on it.
                    </p>
                    <label for="login_name">Enter login name to verify removal</label>
                    <remove-account-component
                        url="{{ route('profile.delete') }}"
                        name="{{ $name }}"
                    >
                        @method('DELETE')
                        @csrf
                    </remove-account-component>
                </div>
            </div>
        </div>
    </div>
@endsection
