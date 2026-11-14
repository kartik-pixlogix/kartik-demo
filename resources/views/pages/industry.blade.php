{{--
    Industry page template, shared by all six sectors.
    Same vocabulary as the service template — only the content differs.
--}}
<x-layout
    :title="$industry['nav'].' security — UB Shield'"
    :description="$industry['lead']"
    :schema="$schema"
>

<section class="phero" id="top">
<i class="hsl a" aria-hidden="true"></i>
<div class="wrap">

<nav class="crumbs" aria-label="Breadcrumb">
<a href="{{ route('home') }}">UB Shield</a>
<i aria-hidden="true">/</i>
<a href="{{ route('home') }}#industries">Industries</a>
<i aria-hidden="true">/</i>
<span aria-current="page">{{ $industry['nav'] }}</span>
</nav>

<div class="phero-g">
<div>
<span class="chip">Industry {{ $industry['number'] }}</span>
<h1>{{ title_dot($industry['title']) }}</h1>
<p class="lead">{{ $industry['lead'] }}</p>
<div class="cta-row">
<a href="#contact" class="btn mag">Book a security assessment <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"></path></svg></a>
<a href="#practices" class="tl mag"><span class="t">Where we start</span><x-ui.arrow-circle /></a>
</div>
</div>
<div class="art rc" aria-hidden="true"><div class="zoom"><x-dynamic-component :component="'art.'.$industry['art']" /></div></div>
</div>

</div>
</section>

<section class="sec">
<div class="wrap split">
<h2 class="dl rm"><span>What we <span class="tnw">see<i class="dot"></i></span></span></h2>
<div>
<p class="rv intro">{{ $industry['intro'] }}</p>
<ul class="rows rs" style="margin-top: 48px">
@foreach ($industry['pressures'] as [$heading, $detail])
<li><span class="n" aria-hidden="true"></span><div><h3>{!! $heading !!}</h3><p>{{ $detail }}</p></div></li>
@endforeach
</ul>
</div>
</div>
</section>

<section class="sec" id="practices">
<div class="wrap">
<div class="sec-t"><h2 class="dl rm"><span>Where we <span class="tnw">start<i class="dot"></i></span></span></h2><p class="rv" style="max-width: 420px">The same five practices, shaped to the rules this sector is inspected against.</p></div>
<div class="cards3 rs">
@foreach ($practices as $practice)
<a href="{{ route('services.show', $practice['slug']) }}" class="card">
<div class="art{{ $practice['art_inverted'] ? ' k' : '' }} rc"><div class="zoom"><x-dynamic-component :component="'art.'.$practice['art']" /></div></div>
<div class="meta"><div><p class="lab-l">{{ $practice['nav'] }}</p><p class="lab-s" style="margin-top: 4px">{{ $practice['kicker'] }}</p></div><x-ui.arrow-circle /></div>
<p class="body">{{ $practice['lead'] }}</p>
</a>
@endforeach
</div>
</div>
</section>

<section class="sec">
<div class="wrap">
<div class="sec-t"><h2 class="dl rm"><span>What applies <span class="tnw">here<i class="dot"></i></span></span></h2><p class="rv" style="max-width: 420px">We map each control once and reuse its evidence for every auditor who asks.</p></div>
<ul class="chips rv">
@foreach ($industry['regulations'] as $regulation)
<li>{{ $regulation }}</li>
@endforeach
</ul>
</div>
</section>

@if ($caseStudy)
<section class="sec">
<div class="wrap">
<div class="sec-t"><h2 class="dl rm"><span>Related case <span class="tnw">file<i class="dot"></i></span></span></h2><p class="rv" style="max-width: 420px">Names, figures and outcomes are placeholders until confirmed.</p></div>
<div class="grid4 rs">
<a href="{{ route('home') }}#about" class="case">
<x-cover-art :record="$caseStudy" />
<div class="meta"><div><p class="lab-l">{{ $caseStudy->title }}</p><p class="lab-s" style="margin-top: 4px">{{ $caseStudy->tags }}</p></div><x-ui.arrow-circle /></div>
<p class="body">{{ $caseStudy->summary }}</p>
</a>
</div>
</div>
</section>
@endif

<x-sections.strip />

</x-layout>
