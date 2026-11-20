<x-mail::message>
# Thank you, {{ $lead->name }}

We have received your enquiry. Our team will review it and get in touch to
discuss the next steps.

For reference, here is what you sent us:

<x-mail::table>
| | |
|:---|:---|
| **Company / organization** | {{ $lead->company }} |
| **City** | {{ $lead->city ?: '—' }} |
| **Service interested in** | {{ $lead->service }} |
</x-mail::table>

If anything there is wrong, just reply to this message and we will correct it.

Thanks,<br>
UB Shield<br>
{{ config('site.locations') }}

<x-slot:subcopy>
You are receiving this because someone submitted this address on
{{ config('app.url') }}. If that was not you, please ignore this message —
we will not contact you again.
</x-slot:subcopy>
</x-mail::message>
