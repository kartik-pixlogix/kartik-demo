{{--
    About us — launch brief §1 and §2.

    Built only from what the brief states: a newly launching company with a
    small core team, technical partners where a project needs them, initial
    service locations Ahmedabad and Rajkot. The prototype-era Credentials
    section is gone: the brief rules out unsubstantiated ISO 27001, SOC 2 and
    CERT-In statuses (§8, §10).
--}}
<x-layout
    title="About us — UB Shield"
    :description="config('site.description')"
    :schema="$schema"
>

<section class="phero" id="top">
<i class="hsl a" aria-hidden="true"></i>
<div class="wrap">

<nav class="crumbs" aria-label="Breadcrumb">
<a href="{{ route('home') }}">UB Shield</a>
<i aria-hidden="true">/</i>
<span aria-current="page">About us</span>
</nav>

<div class="phero-g solo">
<div>
<span class="chip">About us</span>
<h1>Secure today. Stronger <span class="tnw">tomorrow.<i class="dot"></i></span></h1>
<p class="lead">{{ config('site.description') }}</p>
<div class="cta-row">
<a href="#contact" class="btn mag">Talk to our team <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"></path></svg></a>
</div>
</div>
</div>

</div>
</section>

<section class="sec" style="padding-top: 0">
<div class="wrap split">
<h2 class="dl rm"><span>About UB <span class="tnw">Shield<i class="dot"></i></span></span></h2>
<div class="prose rv">
@foreach (config('site.about') as $paragraph)
<p>{{ $paragraph }}</p>
@endforeach
<p>{{ config('site.delivery') }}</p>
</div>
</div>
</section>

<section class="sec">
<div class="wrap split">
<h2 class="dl rm"><span>Why work with UB <span class="tnw">Shield<i class="dot"></i></span></span></h2>
<ul class="rows rs">
@foreach (config('site.why') as $point)
<li><span class="n" aria-hidden="true"></span><div><p>{{ $point }}</p></div></li>
@endforeach
</ul>
</div>
</section>

<section class="sec">
<div class="wrap">
<div class="sec-t"><h2 class="dl rm"><span>Our <span class="tnw">services<i class="dot"></i></span></span></h2></div>
<ul class="rows rs">
@foreach ($practices as $practice)
<li><span class="n" aria-hidden="true"></span><div><h3><a href="{{ route('services.show', $practice['slug']) }}" class="lnk">{{ $practice['nav'] }}</a></h3><p>{{ $practice['lead'] }}</p></div></li>
@endforeach
</ul>
</div>
</section>

<section class="sec">
<div class="wrap">
<div class="sec-t"><h2 class="dl rm"><span>Our <span class="tnw">approach<i class="dot"></i></span></span></h2></div>
<div class="fw-notes rs">
@foreach (config('site.approach') as $i => [$phase, $detail])
<div><b>{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }} / {{ $phase }}</b>{{ $detail }}</div>
@endforeach
</div>
</div>
</section>

<section class="sec">
<div class="wrap">
<div class="sec-t"><h2 class="dl rm"><span>Industries we aim to <span class="tnw">serve<i class="dot"></i></span></span></h2></div>
<ul class="chips rv">
@foreach (config('site.industries') as [$name])
<li>{{ $name }}</li>
@endforeach
</ul>
</div>
</section>

<section class="sec">
<div class="wrap split">
<h2 class="dl rm"><span>Where we <span class="tnw">work<i class="dot"></i></span></span></h2>
<ul class="soc-list rs" style="border-color: var(--ink)">
<li><span>Service locations</span><span>{{ config('site.locations') }}</span></li>
<li><span>Coverage</span><span>{{ config('site.coverage') }}</span></li>
</ul>
</div>
</section>

<x-sections.strip />

</x-layout>
