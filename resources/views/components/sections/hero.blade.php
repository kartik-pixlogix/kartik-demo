{{--
    Hero: chip, headline, lead, CTAs, artwork, stats.
    Ported verbatim from design/v7/index.html lines 557-586.
    Regenerate with scripts/split-prototype-html.mjs — do not retype markup.
--}}
<section class="hero" id="top">
<i class="hsl a" aria-hidden="true"></i><i class="hsl b" aria-hidden="true"></i>
<div class="wrap">
<div class="hero-g">
<div class="hero-l">
<span class="chip">Cybersecurity &amp; compliance</span>
<h1 class="h-xl">
<span class="ln"><span><span data-hl="-1">Tested.</span></span></span>
<span class="ln"><span><span data-hl="1">Fixed.</span></span></span>
<span class="ln"><span><span data-hl="-1">Watched.</span></span></span>
<span class="ln"><span><span data-hl="1" aria-label="Proven.">PR<span class="oring" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 17 17 7M9 7h8v8"></path></svg></span>VEN.</span></span></span>
</h1>
<p class="lead">We are UB Shield. We test your systems, fix what we find, watch them around the clock and prove it to your auditors.</p>
<div class="cta-row">
<a href="#contact" class="btn mag">Book a security assessment <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"></path></svg></a>
<a href="#services" class="tl mag"><span class="t">Explore services</span><x-ui.arrow-circle /></a>
</div>
</div>
<div class="hero-art" aria-hidden="true"><div data-px="-0.12" style="position: absolute; inset: 0">
<div class="lay" data-depth="14"><x-art.hero-grid /></div><div class="lay" data-depth="30"><x-art.hero-nodes /></div><div class="lay" data-depth="22"><x-shield-emblem class="emb" scan /></div><div class="lay" data-depth="52"><x-art.hero-badges /></div><i class="bsl" style="left: -6%; top: 16%"></i><i class="bsl" style="right: -14%; bottom: 18%; width: 52%"></i><span class="ch" style="left: 8%; top: 12%"></span><span class="ch" style="right: 4%; top: 58%"></span><span class="ch" style="left: 36%; bottom: 4%"></span>
</div></div>
</div>
<div class="stats h">
<div><span class="stat">{{ setting('stat_assessments') }}</span><span class="lab-s">Assessments<br>delivered</span></div>
<div><span class="stat"><span data-count="24">24</span>/7</span><span class="lab-s">SOC coverage<br>from Ahmedabad</span></div>
<div><span class="stat">{{ setting('stat_critical_closed') }}</span><span class="lab-s">Critical findings<br>closed in 30 days</span></div>
<div><span class="stat"><span data-count="8">8</span></span><span class="lab-s">Frameworks<br>covered</span></div>
</div>
</div>
</section>
