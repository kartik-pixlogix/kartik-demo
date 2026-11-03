<x-mail::message>
# Thank you, {{ $lead->name }}

We have your request for a free posture review and a senior consultant will be
in touch **within one business day**.

The session takes about forty-five minutes. You will leave it with your five
biggest risks and the order to fix them in — no obligation, and nothing to
prepare beforehand.

For reference, here is what you sent us:

<x-mail::table>
| | |
|:---|:---|
| **Company** | {{ $lead->company }} |
| **Service interest** | {{ $lead->service }} |
</x-mail::table>

If anything there is wrong, just reply to this message and we will correct it.

Thanks,<br>
UB Shield<br>
Ahmedabad, India

<x-slot:subcopy>
You are receiving this because someone submitted this address on
{{ config('app.url') }}. If that was not you, please ignore this message —
we will not contact you again.
</x-slot:subcopy>
</x-mail::message>
