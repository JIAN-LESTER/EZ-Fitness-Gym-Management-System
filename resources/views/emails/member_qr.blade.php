
<x-mail::message>



Hello {{ $name }}!<br>
Thank you for joining EZ Fitness! <br>
Attached is your membership QR code — please present it at the gym for check-in and verification.<br><br>

Membership Type: {{ $plan }} <br>
Valid for: {{$duration}}<br>

Keep this QR code safe and show it each time you visit.

— EZ Fitness
{{ config('app.name') }}
</x-mail::message>
