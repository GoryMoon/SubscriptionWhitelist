@extends('layout.base')

<?php /**
 * @var \App\Models\Channel $channel
 * @var \App\Models\Whitelist[]|\Illuminate\Pagination\LengthAwarePaginator $whitelists
 */ ?>
@section('content')
    <a class="btn btn-primary mb-2" href="{{ route('admin.channel') }}"><fa icon="angle-left"></fa> Channels</a>
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
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col"><a href="{{ request()->fullUrlWithQuery(\App\Helpers::sortQuery('id')) }}">#</a></th>
                        <th scope="col"><a href="{{ request()->fullUrlWithQuery(\App\Helpers::sortQuery('name')) }}">Name</a></th>
                        <th scope="col"><a href="{{ request()->fullUrlWithQuery(\App\Helpers::sortQuery('type')) }}">Type</a></th>
                        <th scope="col"><a href="{{ request()->fullUrlWithQuery(\App\Helpers::sortQuery('valid')) }}">Valid</a></th>
                        <th scope="col"><a href="{{ request()->fullUrlWithQuery(\App\Helpers::sortQuery('minecraft')) }}">Minecraft</a></th>
                        <th scope="col"><a href="{{ request()->fullUrlWithQuery(\App\Helpers::sortQuery('steam')) }}">Steam</a></th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($whitelists as $whitelist)
                        <tr>
                            <th>
                                {{ $whitelist->id }}
                            </th>
                            <td>
                                {{ $whitelist->username }}
                            </td>
                            <td>
                                @if(is_null($whitelist->user))
                                    Custom
                                @else
                                    Subscription
                                @endif
                            </td>
                            <td>
                                @if($whitelist->valid)
                                    <span data-toggle="tooltip" data-placement="top" title="Valid subscription"><fa icon="check" class="text-success"></fa></span>
                                @else
                                    <span data-toggle="tooltip" data-placement="top" title="Invalid subscription"><fa icon="times" class="text-danger"></fa></span>
                                @endif
                            </td>
                            <td>
                                @if(!is_null($whitelist->minecraft_id))
                                    <span data-toggle="tooltip" data-placement="top" title="Minecraft Name Valid"><fa icon="check" class="text-success"></fa></span>
                                @else
                                    <span data-toggle="tooltip" data-placement="top" title="Minecraft Name Invalid"><fa icon="times" class="text-danger"></fa></span>
                                @endif
                            </td>
                            <td>
                                @if(!is_null($whitelist->steam_id))
                                    <span data-toggle="tooltip" data-placement="top" title="Steam Linked"><fa icon="check" class="text-success"></fa></span>
                                @else
                                    <span data-toggle="tooltip" data-placement="top" title="Steam Not Linked"><fa icon="times" class="text-danger"></fa></span>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.channel.whitelist.delete', ['channel' => $channel->id, 'whitelist' => $whitelist->id]) }}">
                                    @method("delete")
                                    @csrf
                                    <button class="btn btn-danger" type="submit"><fa icon="trash"></fa></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $whitelists->withQueryString()->links() }}
        </div>
    </div>
@endsection
