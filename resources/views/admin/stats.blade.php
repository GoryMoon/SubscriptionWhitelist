@extends('layout.base')

<?php /**
 * @var string $total
 * @var string $stats
 */ ?>
@section('content')
    <h1>Whitelist Request Statistics</h1>
    <div class="card">
        <div class="card-body">
            <p>Total amount of requests: <span class="font-weight-bold">{{ $total }}</span></p>
            <h5>Requests made in the last 48-hours:</h5>
            <request-chart-component
                    data="{{ $stats }}"
            ></request-chart-component>
        </div>
    </div>
@endsection