@extends('layout.base')

@section('title', '- Patreon Campaign Picker')

<?php
/** @var array $campaigns */
?>
@section('content')
    <h1>Select the patreon campaign to link to your account</h1>
    <div class="row">
        @foreach($campaigns as $campaign)
        <div class="col-md-6 mt-2">
            <div class="card">
                <img src="{{ $campaign->image }}" class="card-img-top" alt="Campaign banner">
                <div class="card-body">
                    <h4 class="card-title">{{ $campaign->vanity }} is creating {{ $campaign->creation_name }}</h4>
                    <hr>
                    <div class="row">
                        <div class="col-6 mt-2">
                            <a href="{{ $campaign->url }}">{{ $campaign->vanity }} <fa icon="external-link-alt"></fa></a>
                        </div>
                        <div class="col-6 mt-2 text-right">
                            <p>{{ $campaign->patron_count }} patreons</p>
                        </div>
                    </div>
                    <p>{!! $campaign->summary !!}</p>
                    <form action="{{ route('patreon.campaigns.set') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $campaign->id }}">
                        <input type="hidden" name="vanity" value="{{ $campaign->vanity }}">
                        <input type="hidden" name="url" value="{{ $campaign->url }}">
                        <input type="hidden" name="hash" value="{{ $campaign->hash }}">
                        <button type="submit" class="btn btn-primary">Link</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endsection
