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
            <ul>
                <li>
                    Total amount of requests: @bold{{ $total }}@endbold
                </li>
                <li>
                    Total amount on whitelist: @bold{{ $whitelist->total }}@endbold
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