<x-mail::message>
# New website enquiry

**{{ $lead->name }}** at **{{ $lead->company }}** has sent an enquiry.

<x-mail::table>
| | |
|:---|:---|
| **Name** | {{ $lead->name }} |
| **Business email** | {{ $lead->email }} |
| **Company / organization** | {{ $lead->company }} |
| **City** | {{ $lead->city ?: '—' }} |
| **Service interested in** | {{ $lead->service }} |
| **Consent to be contacted** | {{ $lead->consented_at ? 'Yes, '.$lead->consented_at->timezone('Asia/Kolkata')->format('d M Y, H:i').' IST' : '—' }} |
| **Source page** | {{ $lead->source_page ?: '—' }} |
| **Campaign** | {{ collect([$lead->utm_source, $lead->utm_medium, $lead->utm_campaign])->filter()->implode(' / ') ?: '—' }} |
| **Received** | {{ $lead->created_at->timezone('Asia/Kolkata')->format('d M Y, H:i') }} IST |
</x-mail::table>

**Brief requirement**

{{ $lead->requirement ?: '—' }}

Reply directly to this message to reach them.

<x-mail::button :url="config('app.url') . '/admin/leads/' . $lead->id">
Open in the admin
</x-mail::button>

<x-slot:subcopy>
The sender's IP address is not stored — only a keyed hash, so a flood from one
source can still be spotted without holding data we do not need.
</x-slot:subcopy>
</x-mail::message>
