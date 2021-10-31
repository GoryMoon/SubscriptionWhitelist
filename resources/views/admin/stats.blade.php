@extends('layout.base')

@section('content')
    <h1>Whitelist Request Statistics</h1>
    <div class="card">
        <div class="card-body">
            <h5>Misc Stats</h5>
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
