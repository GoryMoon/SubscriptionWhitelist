@extends('layout.base')
<?php
/** @var string $display_name */
/** @var string $name */
/** @var string $id */
?>

@section('title', '- Channel - ' . $display_name)

@section('content')
    <h1>Channel: <a href="https://twitch.tv/{{ $name }}">{{ $display_name }}</a></h1>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('subscriber.add.save', ['channel' => $id]) }}">
                <div class="form-group">
                    <label for="username">Enter username to whitelist to this channel. This can be edited later.</label>
                    <input type="text" class="@error('username') is-invalid @enderror form-control mr-sm-2 mb-2" id="username" name="username" required placeholder="Username">
                    @error('username')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                @csrf
                <button type="submit" class="btn btn-primary mb-2"><fa icon="plus"></fa> Add</button>
            </form>
        </div>
    </div>
@endsection