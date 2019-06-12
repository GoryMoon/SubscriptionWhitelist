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
    @if ($errors->any())
        @if($errors->has('contact_email') || $errors->has('contact_message'))
            <div class="alert alert-danger">
                There where an issue sending the message, check below
            </div>
        @else
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger" role="alert">{{ $error }}</div>
            @endforeach
        @endif
    @endif
    <h1>Whitelist Settings</h1>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('broadcaster.settings') }}">
                <h5>Whitelist toggle</h5>
                <div class="form-group">
                    <label class="sr-only" for="list_toggle">Enable or disable whitelist</label>
                    <input type="hidden" name="list_toggle" value="0">
                    <input type="checkbox" {{ !$enabled ?: 'checked' }} class="form-control ml-2" value="1" id="list_toggle" name="list_toggle" data-toggle="toggle" data-on="Enabled" data-off="Disabled" data-onstyle="success" data-offstyle="danger">
                </div>
                <hr>
                <h5><fa icon="star" class="text-primary"></fa> Subscriptions</h5>
                <div class="form-group">
                    <label>Select what tier of subs are able to add a username to your whitelist</label>
                    {{--<div class="custom-control custom-checkbox">
                        <input type="hidden" name="plan[prime]" value="0">
                        <input {{ !$plans['prime'] ?: 'checked' }} name="plan[prime]" value="1" type="checkbox" class="custom-control-input" id="prime_check">
                        <label class="custom-control-label" for="prime_check">Prime</label>
                    </div>--}}
                    <div class="custom-control custom-checkbox">
                        <input type="hidden" name="plan[tier1]" value="0">
                        <input {{ !$plans['tier1'] ?: 'checked' }} name="plan[tier1]" value="1" type="checkbox" class="custom-control-input" id="tier1_check">
                        <label class="custom-control-label" for="tier1_check">Tier 1 &amp; Prime</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="hidden" name="plan[tier2]" value="0">
                        <input {{ !$plans['tier2'] ?: 'checked' }} name="plan[tier2]" value="1" type="checkbox" class="custom-control-input" id="tier2_check">
                        <label class="custom-control-label" for="tier2_check">Tier 2</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="hidden" name="plan[tier3]" value="0">
                        <input {{ !$plans['tier3'] ?: 'checked' }} name="plan[tier3]" value="1" type="checkbox" class="custom-control-input" id="tier3_check">
                        <label class="custom-control-label" for="tier3_check">Tier 3</label>
                    </div>
                </div>
                <hr>
                <h5><fa icon="sync" class="text-primary"></fa> Sync</h5>
                <div class="form-group">
                    <p>Enable or disable auto syncing of subscriptions (you can manually sync users in the users tab above)</p>
                    <label class="sr-only" for="sync_toggle">Enable or disable auto syncing of subscriptions</label>
                    <input type="hidden" name="sync_toggle" value="0">
                    <input type="checkbox" {{ !$sync ?: 'checked' }} class="form-control ml-2" id="sync_toggle" value="1" name="sync_toggle" data-toggle="toggle" data-on="Enabled" data-off="Disabled" data-onstyle="success" data-offstyle="secondary">
                </div>
                <div class="form-group">
                    <label for="sync_options">Sync options</label>
                    <select class="custom-select" name="sync_option" id="sync_option">
                        <option {{ $sync_option == "1day" ? 'selected' : '' }} value="1day">Every day</option>
                        <option {{ $sync_option == "2day" ? 'selected' : '' }} value="2day">Every twice a week</option>
                        <option {{ $sync_option == "7day" ? 'selected' : '' }} value="7day">Every week</option>
                    </select>
                </div>
                <hr>
                @csrf

                <button type="submit" class="btn btn-primary"><fa icon="save"></fa> Save</button>
            </form>
        </div>
    </div>

    <h1 class="mt-5">Whitelist Links</h1>
    <div class="card mb-3">
        <div class="card-body">
            <h3>Subscriber link</h3>
            <div class="form-group">
                <label for="sub_link">Link to give to your subscribers</label>
                <input class="form-control selectable" id="sub_link" readonly type="text" value="{{ route('subscriber.add', ['channel' => $name]) }}">
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <h3>List links</h3>
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pills-general-tab" data-toggle="pill" href="#pills-general" role="tab" aria-controls="pills-general" aria-selected="true">General</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-minecraft-tab" data-toggle="pill" href="#pills-minecraft" role="tab" aria-controls="pills-minecraft" aria-selected="false">Minecraft</a>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-general" role="tabpanel" aria-labelledby="pills-general-tab">
                    @include('broadcaster.links.general')
                </div>
                <div class="tab-pane fade" id="pills-minecraft" role="tabpanel" aria-labelledby="pills-minecraft-tab">
                    @include('broadcaster.links.minecraft')
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <h3>Contact</h3>
            <p>
                Not finding the format you want?<br>
                Get in contact with me below and I'll look into adding it.
            </p>
            <form action="{{ route('broadcaster.contact') }}" method="POST">
                <div class="form-group">
                    <label for="contact_email">Email (to contact you back if needed)</label>
                    <input type="email" name="contact_email" class="@error('contact_email') is-invalid @enderror form-control mb-2" id="contact_email" required placeholder="foo.bar@example.com">
                    @error('contact_email')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="contact_message">Describe your request, can include link to an example</label>
                    <textarea name="contact_message" class="form-control mb-2 @error('contact_message') is-invalid @enderror" cols="30" rows="15" required id="contact_message"></textarea>
                    @error('contact_message')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                @csrf
                <button class="btn btn-primary"><fa icon="paper-plane"></fa> Send</button>
            </form>
        </div>
    </div>
@endsection