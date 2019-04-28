@component('mail::message')
# Contact message

From: {{ $display_name }} ({{ $name }})

Email: <a href="mailto:{{ $contact }}">{{ $contact }}</a>

Message:
@component('mail::panel')
{!! $message !!}
@endcomponent

@component('mail::button', ['url' => 'https://twitch.tv/' . $name, 'color' => 'primary'])
    View Channel
@endcomponent

@endcomponent
