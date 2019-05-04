@extends('broadcaster.base')

<?php /**
 * @var string $name
 * @var array $plans
 * @var boolean $enabled
 * @var string $base_url
 * @var boolean $sync
 * @var string $sync_option
 */ ?>
@section('b_content')
    <h1>Whitelist Request Statistics</h1>
    <div class="card">
        <div class="card-body">
            <p>Total amount of request to your links: <span class="font-weight-bold">{{ $total }}</span></p>
            <h5>Requests made in the last 48-hours:</h5>
            <request-chart-component
                    data="{{ $stats }}"
            ></request-chart-component>
        </div>
    </div>
@endsection