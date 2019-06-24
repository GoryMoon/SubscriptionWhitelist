@extends('layout.base')

@section('title', '- Subscriber')

<?php /** @var \Illuminate\Database\Eloquent\Collection $whitelists */?>
@section('content')
    <h1 xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">Whitelist username</h1>
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('subscriber.redirect') }}">
                <div class="form-group">
                    <label for="channel">Channel name</label>
                    <input type="text" class="@error('add') is-invalid @enderror form-control mr-sm-2 mb-2" id="channel" name="channel" placeholder="Channel">
                    @error('add')
                    <div class="alert alert-danger" role="alert">{!! $message !!}</div>
                    @enderror
                </div>
                @csrf
                <button type="submit" class="btn btn-primary mb-2"><fa icon="search"></fa> Search</button>
            </form>
        </div>
    </div>

    <h1 class="mt-5">Manage whitelisted usernames</h1>
    @if(count($whitelists) <= 0)
        <div class="alert alert-info" role="alert">
            You don't have any whitelisted usernames
        </div>
    @endif
    @error('edit')
    <div class="alert alert-danger" role="alert">{{ $message }}</div>
    @enderror
    @foreach($whitelists as $whitelist)
        <div class="card mb-2">
            <h4 class="card-header">Channel: <a class="ml-1" href="https://www.twitch.tv/{{ $whitelist->name }}"><fa :icon="['fab','twitch']"></fa>{{ $whitelist->display_name }} ({{ $whitelist->name }})</a></h4>
            <div class="card-body">

                <div class="card-text">
                    @if(!$whitelist->valid)
                    <div class="alert alert-danger" role="alert">
                        Your subscription isn't valid anymore, this will not be included in the whitelist
                    </div>
                    @endif
                    <sub-manage-component
                            route="{{ route('subscriber.save', ['channel' => $whitelist->id]) }}"
                            id="{{ $whitelist->id }}"
                            uid="{{ $whitelist->uid }}"
                            minecraft="{{ $whitelist->minecraft }}"
                            username="{{ $whitelist->username }}"
                            valid="{{ $whitelist->valid }}"
                            index="{{ $loop->index }}"
                            error-classes="@error('username-'.$whitelist->id) is-invalid @enderror @error('edit'.$whitelist->id) is-invalid @enderror"
                    >
                        <template v-slot:csrf>
                            @csrf
                        </template>
                        <template v-slot:error-alert>
                            @error('username-'.$whitelist->id)
                            <div class="alert alert-danger" role="alert">{{ $message }}</div>
                            @enderror
                            @error('edit-'.$whitelist->id)
                            <div class="alert alert-danger" role="alert">{{ $message }}</div>
                            @enderror
                        </template>
                    </sub-manage-component>
                </div>
            </div>
        </div>
    @endforeach
@endsection