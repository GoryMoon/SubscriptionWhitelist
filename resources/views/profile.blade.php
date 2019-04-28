@extends('layout.base')

@section('title', '- Profile')

<?php
    /** @var string $display_name */
    /** @var string $name */
?>
@section('content')
    <h1><a href="https://twitch.tv/{{ $name }}"><fa :icon="['fab','twitch']"></fa>{{ $display_name }} ({{ $name }})</a></h1>
    <p class="text-muted h6">Not much here, might be more in future if needed</p>
    <div class="row">
        <div class="col-sm-6 mt-2">
            <div class="card text-white border-danger">
                <div class="card-body text-danger">
                    <h5 class="card-title"><fa icon="trash"></fa> Delete Account</h5>
                    <p class="card-text">
                        With this you can delete your account and all the data associated with it.<br>
                        Your username/usernames will be removed from any whitelist you are on.<br>
                        If you are a broadcaster, your whitelist will be removed and all the usernames on it.
                    </p>
                    <label for="login_name">Enter login name to verify removal</label>
                    <remove-account-component
                            route="{{ route('profile.delete') }}"
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