
@component('mail::message')

# New Member

@component('mail::panel')
Name: {{ $member->user->first_name }} {{ $member->user->last_name }}

Email: {{ $member->user->email }}
@endcomponent

@endcomponent
