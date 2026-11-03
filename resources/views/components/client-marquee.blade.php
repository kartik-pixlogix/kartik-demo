{{--
    The client-logo marquee inside the trust strip.

    The prototype draws eight dashed "Client logo" boxes and then repeats them,
    marking the copies aria-hidden so a screen reader reads the list once
    rather than twice. That structure is kept exactly; only what sits inside a
    box changes.

    With no clients in the database the prototype's placeholders render
    verbatim, so the homepage still matches the design pixel for pixel. Once
    the client adds logos in the admin, those replace them — padded out by
    repeating the set if there are fewer than eight, because the marquee
    animation translates by exactly -50% and needs both halves to be the same
    width.
--}}
@php
    // Design-preview renders the strip as the prototype draws it, so the pixel
    // diff is unaffected by whichever logos the client has uploaded.
    $clients = \App\Support\DesignPreview::active()
        ? collect()
        : \App\Models\Client::forMarquee();

    // Eight boxes is what the prototype draws and what the -50% keyframe
    // assumes. Repeat a short list rather than leaving a gap in the loop.
    $boxes = $clients->isEmpty()
        ? collect()
        : collect(range(1, 8))->map(fn (int $i) => $clients[($i - 1) % $clients->count()]);
@endphp
<div class="lmq">
<div class="lmq-in">
@if ($boxes->isEmpty())
{{-- No clients yet: the prototype's placeholders, exactly as drawn. --}}
@for ($i = 0; $i < 8; $i++)<span class="lg">Client logo</span>@endfor
@for ($i = 0; $i < 8; $i++)<span class="lg" aria-hidden="true">Client logo</span>@endfor
@else
@foreach ($boxes as $client)<span class="lg has-logo"><img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($client['logo_path']) }}" alt="{{ $client['name'] }}" width="400" height="144" loading="lazy" decoding="async"></span>@endforeach
@foreach ($boxes as $client)<span class="lg has-logo" aria-hidden="true"><img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($client['logo_path']) }}" alt="" width="400" height="144" loading="lazy" decoding="async"></span>@endforeach
@endif
</div>
</div>
