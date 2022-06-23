@component('mail::message')
## Password Reset Request

A request was made to reset your password on {{ $user->domain->name }}. If you 
did not make this request, please ignore this email, or let us know by replying
to this email. Click the link below to confirm this
password change.

@component('mail::button', ['url' => $url])
Confirm Password Reset
@endcomponent
@endcomponent
