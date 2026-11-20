{{--
    Service page template, shared by the six approved services
    (config/practices.php, launch brief §3).

    Sections: hero, what's included, what you get (where the brief names
    outputs), our approach (brief §5), the authorization note where §10
    requires one, the other services, closing strip. The prototype-era
    frameworks, case-file and FAQ sections are gone: the brief supplies no
    content for them and rules out the compliance and case-study claims.
--}}
<x-layout
    :title="$practice['nav'].' — UB Shield'"
    :description="$practice['lead']"
    :schema="$schema"
>

<section class="phero" id="top">
<i class="hsl a" aria-hidden="true"></i>
<div class="wrap">

<nav class="crumbs" aria-label="Breadcrumb">
<a href="{{ route('home') }}">UB Shield</a>
<i aria-hidden="true">/</i>
<a href="{{ route('home') }}#services">Services</a>
<i aria-hidden="true">/</i>
<span aria-current="page">{{ $practice['short'] }}</span>
</nav>

<div class="phero-g">
<div>
<span class="chip">{{ $practice['kicker'] }}</span>
<h1 class="svc-h1">{{ title_dot($practice['nav']) }}</h1>
<p class="lead">{{ $practice['lead'] }}</p>
<div class="cta-row">
<a href="#contact" class="btn mag">Request a consultation <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"></path></svg></a>
<a href="{{ route('home') }}#services" class="tl mag"><span class="t">All services</span><x-ui.arrow-circle /></a>
</div>
</div>
<div class="art{{ $practice['art_inverted'] ? ' k' : '' }} rc" aria-hidden="true"><div class="zoom"><x-dynamic-component :component="'art.'.$practice['art']" /></div></div>
</div>

</div>
</section>

<section class="sec">
<div class="wrap split">
<h2 class="dl rm"><span>What&rsquo;s <span class="tnw">included<i class="dot"></i></span></span></h2>
<div>
<ul class="chips rs">
@foreach ($practice['included'] as $item)
<li>{{ $item }}</li>
@endforeach
</ul>
@isset($practice['note'])
<p class="rv svc-note">{{ $practice['note'] }}</p>
@endisset
</div>
</div>
</section>

@if (! empty($practice['deliverables']))
<section class="sec">
<div class="wrap split">
<h2 class="dl rm"><span>What you <span class="tnw">get<i class="dot"></i></span></span></h2>
<ul class="rows rs">
@foreach ($practice['deliverables'] as $item)
<li><span class="n" aria-hidden="true"></span><div><h3>{{ $item }}</h3></div></li>
@endforeach
</ul>
</div>
</section>
@endif

<section class="sec">
<div class="wrap">
<div class="sec-t"><h2 class="dl rm"><span>Our <span class="tnw">approach<i class="dot"></i></span></span></h2><p class="rv" style="max-width: 420px">{{ config('site.delivery') }}</p></div>
<div class="fw-notes rs">
@foreach (config('site.approach') as $i => [$phase, $detail])
<div><b>{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }} / {{ $phase }}</b>{{ $detail }}</div>
@endforeach
</div>
</div>
</section>

<section class="sec">
<div class="wrap">
<div class="sec-t"><h2 class="dl rm"><span>Other <span class="tnw">services<i class="dot"></i></span></span></h2></div>
<ul class="rows rs">
@foreach ($others as $other)
<li><span class="n" aria-hidden="true"></span><div><h3><a href="{{ route('services.show', $other['slug']) }}" class="lnk">{{ $other['nav'] }}</a></h3><p>{{ $other['kicker'] }}</p></div></li>
@endforeach
</ul>
</div>
</section>

<x-sections.strip :preselect="$practice['nav']" />

</x-layout>
