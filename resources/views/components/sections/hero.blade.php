{{--
    Hero: chip, headline, lead, CTAs, artwork. Launch copy from config/site.php.
    Ported verbatim from design/v7/index.html lines 557-586.
    Regenerate with scripts/split-prototype-html.mjs — do not retype markup.
--}}
<section class="hero" id="top">
<i class="hsl a" aria-hidden="true"></i><i class="hsl b" aria-hidden="true"></i>
<div class="wrap">
<div class="hero-g">
<div class="hero-l">
<span class="chip">{{ config('site.eyebrow') }}</span>
<h1 class="h-xl hero-h">
@foreach (config('site.headline') as $i => $line)
<span class="ln"><span><span data-hl="{{ $i % 2 ? 1 : -1 }}">{{ $line }}</span></span></span>
@endforeach
</h1>
<p class="lead">{{ config('site.hero_lead') }}</p>
<div class="cta-row">
<a href="#services" class="btn mag">Explore our services <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"></path></svg></a>
<a href="{{ route('contact') }}" class="tl mag"><span class="t">Talk to our team</span><x-ui.arrow-circle /></a>
</div>
</div>
<div class="hero-art" aria-hidden="true"><div data-px="-0.12" style="position: absolute; inset: 0">
<div class="lay" data-depth="14"><x-art.hero-grid /></div><div class="lay" data-depth="30"><x-art.hero-nodes /></div><div class="lay" data-depth="22"><x-shield-emblem class="emb" scan /></div><div class="lay" data-depth="52"><x-art.hero-badges /></div><i class="bsl" style="left: -6%; top: 16%"></i><i class="bsl" style="right: -14%; bottom: 18%; width: 52%"></i><span class="ch" style="left: 8%; top: 12%"></span><span class="ch" style="right: 4%; top: 58%"></span><span class="ch" style="left: 36%; bottom: 4%"></span>
</div></div>
</div>
{{-- Stats row removed for launch: placeholder metrics and the 24/7 SOC claim (brief §8). --}}
</div>
</section>
