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