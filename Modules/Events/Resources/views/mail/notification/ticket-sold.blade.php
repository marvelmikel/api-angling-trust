
@component('mail::message')

# Ticket Sold

Event: {{ $event->name }}

Ticket: {{ $ticket->name }} ({{ $ticket->ref }})

@component('mail::panel')
Name: {{ $your_details['first_name'] }} {{ $your_details['last_name'] }}

Email: {{ $your_details['email'] }}

Contact Number: {{ $your_details['contact_number'] }}

Date of Birth: {{ $your_details['date_of_birth'] }}

Disability: {{ $your_details['disability'] }}

Disability: {{ $your_details['disability'] }}

Address: {{ $your_details['address']['line_1'] }} {{ $your_details['address']['line_2'] }} {{ $your_details['address']['town'] }} {{ $your_details['address']['county'] }} {{ $your_details['address']['postcode'] }}

Fishing License Number: {{ $your_details['fishing_licence_number'] }}

Sponsor: {{ $your_details['sponsor'] }}
@endcomponent

@endcomponent
