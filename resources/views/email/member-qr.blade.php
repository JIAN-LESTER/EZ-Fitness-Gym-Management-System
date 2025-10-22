@component('mail::message')
# Hello {{ $name }},

Thank you for completing your membership profile! 🎉  
Attached is your **membership QR code**, which contains your basic information.

You can use it for check-ins and verification.

Thanks,  
{{ config('app.name') }}
@endcomponent
