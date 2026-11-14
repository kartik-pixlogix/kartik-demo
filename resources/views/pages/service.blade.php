{{--
    Service page template, shared by all five practices.

    Section order is BUILD.md Phase 3: hero, what's included, the process,
    deliverables, frameworks covered, related case file, FAQ, closing strip.
    Everything but the hero and the FAQ reuses components and classes the
    prototype already established.
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
<span aria-current="page">{{ $practice['nav'] }}</span>
</nav>

<div class="phero-g">
<div>
<span class="chip">{{ $practice['kicker'] }}</span>
<h1>{{ title_dot($practice['title']) }}</h1>
<p class="lead">{{ $practice['lead'] }}</p>
<div class="cta-row">
<a href="#contact" class="btn mag">Book a security assessment <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"></path></svg></a>
<a href="#faq" class="tl mag"><span class="t">Common questions</span><x-ui.arrow-circle /></a>
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
<p class="rv intro">{{ $practice['intro'] }}</p>
<ul class="rows rs" style="margin-top: 48px">
@foreach ($practice['included'] as [$heading, $detail])
<li><span class="n" aria-hidden="true"></span><div><h3>{{ $heading }}</h3><p>{{ $detail }}</p></div></li>
@endforeach
</ul>
</div>
</div>
</section>

<section class="sec">
<div class="wrap">
<div class="sec-t"><h2 class="dl rm"><span>How we <span class="tnw">work<i class="dot"></i></span></span></h2><p class="rv" style="max-width: 420px">Every engagement runs the same four phases. You always know what happens next and who owns it.</p></div>
<div class="fw-notes rs">
@foreach ($practice['process'] as $i => [$phase, $detail])
<div><b>{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }} / {{ $phase }}</b>{{ $detail }}</div>
@endforeach
</div>
</div>
</section>

<section class="sec">
<div class="wrap split">
<h2 class="dl rm"><span>What you <span class="tnw">get<i class="dot"></i></span></span></h2>
<ul class="rows rs">
@foreach ($practice['deliverables'] as [$heading, $detail])
<li><span class="n" aria-hidden="true"></span><div><h3>{{ $heading }}</h3><p>{{ $detail }}</p></div></li>
@endforeach
</ul>
</div>
</section>

<section class="sec">
<div class="wrap">
<div class="sec-t"><h2 class="dl rm"><span>Frameworks <span class="tnw">covered<i class="dot"></i></span></span></h2><p class="rv" style="max-width: 420px">We map each control once and reuse its evidence for every auditor who asks.</p></div>
<ul class="chips rv">
@foreach ($practice['frameworks'] as $framework)
<li>{{ $framework }}</li>
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

<section class="sec" id="faq">
<div class="wrap split">
<h2 class="dl rm"><span>Common <span class="tnw">questions<i class="dot"></i></span></span></h2>
<div>
<div class="faq">
@foreach ($practice['faq'] as $i => [$question, $answer])
<details @if ($i === 0) open @endif>
<summary>{{ $question }}<span class="mk" aria-hidden="true"></span></summary>
<div class="answer"><p>{{ $answer }}</p></div>
</details>
@endforeach
</div>
<div class="ask">
<p>Not the question you had? A scoping call answers it faster than a brochure.</p>
<a href="#contact" class="tl mag"><span class="t">Ask us directly</span><x-ui.arrow-circle /></a>
</div>
</div>
</div>
</section>

<x-sections.strip />

</x-layout>
