@extends('layout.base')

<?php /**
 * @var \App\Models\Channel $channel
 * @var \App\Models\Whitelist[]|\Illuminate\Pagination\LengthAwarePaginator $whitelists
 */ ?>
@section('content')
    <a class="btn btn-primary mb-2" href="{{ route('admin.channel.view', ['channel' => $channel->id]) }}"><fa icon="angle-left"></fa> Back to channel</a>
    <div class="card mb-3">
        <div class="card-body">
            <h3>Channel: @bold {{ $channel->owner->display_name }}@endbold</h3>
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
        </div>
    </div>
@endsection
