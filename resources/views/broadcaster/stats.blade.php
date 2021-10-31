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
                <li>
                    Total amount of valid Minecraft names: @bold{{ $whitelist->minecraft }}@endbold
                </li>
                <li>
                    Total amount of linked Steam IDs: @bold{{ $whitelist->steam }}@endbold
                </li>
                <li>
                    Requests last 24h: @bold{{ $day }}@endbold
                </li>
                <li>
                    Requests last 48h: @bold{{ $twodays }}@endbold
                </li>
            </ul>
            <h5>Graph of request for last 48-hours:</h5>
            <request-chart-component></request-chart-component>
        </div>
    </div>
@endsection
