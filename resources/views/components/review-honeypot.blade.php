{{--
    Spam defences that cost a visitor nothing.

    1. A field a human never sees and never fills. Off-screen rather than
       display:none, because bots commonly skip explicitly hidden inputs.
       tabindex="-1" and autocomplete="off" keep keyboard users and password
       managers away from it; aria-hidden keeps it out of the accessibility tree.
    2. An encrypted render timestamp, so the server can tell how long the form
       was open. Encrypted rather than plain so it cannot be forged or replayed.

    Both are read in App\Http\Requests\ReviewRequest.
--}}
@php
    $honeypot = \App\Http\Requests\ReviewRequest::HONEYPOT;
    $timestamp = \App\Http\Requests\ReviewRequest::TIMESTAMP;
@endphp
<div class="hp" aria-hidden="true">
    <label for="v6-{{ $honeypot }}">Website</label>
    <input id="v6-{{ $honeypot }}" type="text" name="{{ $honeypot }}" value="" tabindex="-1" autocomplete="off">
</div>
<input type="hidden" name="{{ $timestamp }}" value="{{ Crypt::encryptString((string) time()) }}">
