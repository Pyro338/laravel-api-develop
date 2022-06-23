@component('mail::message')

## Confirm Withdrawal Request

{{ $withdrawal->player->name }},

You have requested to send {{ $withdrawal->amount }} {{ $withdrawal->currency }} to {{ $withdrawal->address }}.
If the transaction details below look correct, please click on the confirmation link to send this transaction. If you did 
not make this request, please let us know by replying to this email.

@component('mail::table')
|                                            |                                                                             |
|--------------------------------------------|-----------------------------------------------------------------------------|
| Currency                                   | {{ $withdrawal->currency }}                                                 |
| Amount                                     | {{ number_format($withdrawal->amount, 8) }}                                 |
| Recipient                                  | {{ $withdrawal->address }}                                                  |
@endcomponent

@component('mail::button', ['url' => $url])
Confirm This Withdrawal
@endcomponent

@component('mail::button', ['url' => $cancelUrl])
Cancel This Withdrawal
@endcomponent

@endcomponent
