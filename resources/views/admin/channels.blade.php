@extends('layout.base')

<?php /**
 * @var \App\Models\Channel[]|\Illuminate\Pagination\LengthAwarePaginator $channels
 */ ?>
@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <h3>Channels</h3>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col"><a href="{{ request()->fullUrlWithQuery(\App\Helpers::sortQuery('id')) }}">#</a></th>
                        <th scope="col"><a href="{{ request()->fullUrlWithQuery(\App\Helpers::sortQuery('name')) }}">Name</a></th>
                        <th scope="col"><a href="{{ request()->fullUrlWithQuery(\App\Helpers::sortQuery('dname')) }}">Display Name</a></th>
                        <th scope="col"><a href="{{ request()->fullUrlWithQuery(\App\Helpers::sortQuery('type')) }}">Type</a></th>
                        <th scope="col"><a href="{{ request()->fullUrlWithQuery(\App\Helpers::sortQuery('enabled')) }}">Enabled</a></th>
                        <th scope="col"><a href="{{ request()->fullUrlWithQuery(\App\Helpers::sortQuery('whitelist')) }}"># Whitelist</a></th>
                        <th scope="col"><a href="{{ request()->fullUrlWithQuery(\App\Helpers::sortQuery('requests')) }}">Requests</a></th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($channels as $channel)
                        <tr>
                            <th>
                                {{ $channel->id }}
                            </th>
                            <td>
                                {{ $channel->owner->name }}
                            </td>
                            <td>
                                {{ $channel->owner->display_name }}
                            </td>
                            <td>
                                {{ \Illuminate\Support\Str::title($channel->owner->broadcaster_type) }}
                            </td>
                            <td>
                                @if($channel->enabled)
                                    <span data-toggle="tooltip" data-placement="top" title="Whitelist enabled"><fa icon="check" class="text-success"></fa></span>
                                @else
                                    <span data-toggle="tooltip" data-placement="top" title="Whitelist disabled"><fa icon="times" class="text-danger"></fa></span>
                                @endif
                            </td>
                            <td>
                                {{ $channel->whitelist->count() }}
                            </td>
                            <td>
                                {{ $channel->requests }}
                            </td>
                            <td>
                                <a class="btn btn-primary" href="{{ route('admin.channel.view', ['channel' => $channel->id]) }}">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $channels->withQueryString()->links() }}
        </div>
    </div>
@endsection
