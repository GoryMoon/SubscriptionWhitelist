@extends('layout.base')

@section('content')
    <h1>Admin Dashboard</h1>
    <div class="row">
        <div class="col-sm-6 mt-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><fa icon="users" class="text-primary"></fa> Channels</h5>
                    <p class="card-text">View all channels and the users in the lists.</p>
                    <a href="{{ route('admin.channel') }}" class="btn btn-primary">Channel list</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 mt-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><fa icon="chart-area" class="text-primary"></fa> Stats</h5>
                    <p class="card-text">View global usage stats.</p>
                    <a href="{{ route('admin.stats') }}" class="btn btn-primary">Stats</a>
                </div>
            </div>
        </div>
    </div>
@endsection
