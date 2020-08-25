@extends('broadcaster.base')

@section('title', '- Broadcaster Userlist')

@section('b_content')
    <h1>Whitelist Userlist</h1>
    <div class="alert alert-info" role="alert">
        <h4 class="alert-heading">Adding yourself to the list</h4>
        <p>
            As broadcaster you add yourself the same way as subscribers do.<br/>
            If you have added yourself manually below as a custom name you need to remove that before
            adding yourself as subscribers do it.<br>
            This is also the only way for you to add your own SteamID to the list.
        </p>
        <a class="btn btn-primary" href="{{ route('subscriber.add', ['channel' => $name]) }}"><fa icon="plus"></fa>  Add yourself</a>
    </div>
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
