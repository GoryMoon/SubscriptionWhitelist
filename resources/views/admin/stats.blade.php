@extends('layout.base')

<?php /**
 * @var string $total
 * @var string $stats
 */ ?>
@section('content')
    <h1>Whitelist Request Statistics</h1>
    <div class="card">
        <div class="card-body">
            <ul>
                <li>
                    Total amount of requests: @bold{{ $total }}@endbold
                </li>
                <li>
                    Total amount of channels: @bold{{ $channels }}@endbold
                </li>
                <li>
                    Total amount on whitelists: @bold{{ $whitelist->total }}@endbold
                </li>
                <li>
                    Total amount of subscriber names: @bold{{ $whitelist->subscribers }}@endbold
                </li>
                <li>
                    Total amount of custom names: @bold{{ $whitelist->custom }}@endbold
                </li>
                <li>
                    Total amount of invalid subscriptions: @bold{{ $whitelist->invalid }}@endbold
                </li>
            </ul>
            <h5>Requests made in the last 48-hours:</h5>
            <request-chart-component
                    data="{{ $stats }}"
            ></request-chart-component>
        </div>
    </div>
@endsection