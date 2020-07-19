@extends('broadcaster.base')

@section('title', '- Broadcaster Userlist')

@section('b_content')
    <h1>Whitelist Userlist</h1>
    <div class="card mb-3">
        <div class="card-body">
            <h3>Add custom names</h3>
            <p class="text-muted">
                Here you can add custom usernames, for instance: your, moderators or anyone else. <br>
                These names will always be in the whitelist until you remove them, they are not bound to a subscription
            </p>
            <add-user-component>
                @csrf
            </add-user-component>
            @error('usernames')
            <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
            @error('usernames.*')
            <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <h3>Users</h3>
            <user-list-component channel="{{ $channel_id }}"></user-list-component>
        </div>
    </div>
@endsection
