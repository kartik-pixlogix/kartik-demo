{{--
    About.

    Deliberately short on biography. Founding dates, headcount, leadership
    names and credentials are facts about the client that have not been
    confirmed, so this page is built from what the approved design already
    asserts — Ahmedabad, round-the-clock cover, five practices, four phases —
    and brackets everything else. See docs/placeholders.md.
--}}
<x-layout
    title="About — UB Shield"
    description="A cybersecurity and compliance company in Ahmedabad. Five practices, one team, and a method that ends in evidence rather than a report."
>

<section class="phero" id="top">
<i class="hsl a" aria-hidden="true"></i>
<div class="wrap">

<nav class="crumbs" aria-label="Breadcrumb">
<a href="{{ route('home') }}">UB Shield</a>
<i aria-hidden="true">/</i>
<span aria-current="page">About</span>
</nav>

<div class="phero-g solo">
<div>
<span class="chip">About</span>
<h1>We stay until it&rsquo;s fixed<i class="dot"></i></h1>
<p class="lead">UB Shield is a cybersecurity and compliance company in Ahmedabad. We test systems, fix what we find, watch them around the clock and prove it to auditors.</p>
<div class="cta-row">
<a href="#contact" class="btn mag">Talk to an expert <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"></path></svg></a>
</div>
</div>
</div>

</div>
</section>

<section class="sec" style="padding-top: 0">
<div class="wrap split">
<h2 class="dl rm"><span>How we think<i class="dot"></i></span></h2>
<div class="prose rv">
<p>A report nobody acts on is not security. That sentence is the whole argument, and most of how we work follows from it.</p>
<p>Plenty of firms will hand you a finding. Fewer will sit with your engineers while it is fixed, retest it, and file the proof as audit evidence — which is the part that actually changes your risk and the part auditors ask about. So we built the practice around the full loop rather than the first step of it.</p>
<p>The second thing that follows is that the five practices are one team. A penetration test that never reaches the people running your SOC is a wasted finding; a compliance programme built without the people who tested the estate is a filing exercise. Mapping a control once and reusing its evidence everywhere only works if the same organisation holds both ends.</p>
</div>
</div>
</section>

<section class="sec">
<div class="wrap">
<div class="sec-t"><h2 class="dl rm"><span>Five practices<i class="dot"></i></span></h2><p class="rv" style="max-width: 420px">Secure / Privacy / Compliance / Infrastructure / MSS</p></div>
<ul class="rows rs">
@foreach ($practices as $practice)
<li><span class="n" aria-hidden="true"></span><div><h3><a href="{{ route('services.show', $practice['slug']) }}" class="lnk">{{ $practice['nav'] }}</a></h3><p>{{ $practice['lead'] }}</p></div></li>
@endforeach
</ul>
</div>
</section>

<section class="sec">
<div class="wrap">
<div class="sec-t"><h2 class="dl rm"><span>How we work<i class="dot"></i></span></h2><p class="rv" style="max-width: 420px">Every engagement runs the same four phases. You always know what happens next and who owns it.</p></div>
<div class="fw-notes rs">
<div><b>01 / Assess</b>We scope your estate, test it and record a baseline your board can track quarter to quarter.</div>
<div><b>02 / Remediate</b>We fix what matters most first, alongside your engineers. Then we retest to prove it is closed.</div>
<div><b>03 / Monitor</b>Your logs stream to our SOC. Analysts triage around the clock and escalate within agreed SLAs.</div>
<div><b>04 / Certify</b>We hand your auditor an evidence pack mapped control by control, and we sit in the room.</div>
</div>
</div>
</section>

<section class="sec">
<div class="wrap">
<div class="sec-t"><h2 class="dl rm"><span>Who we work with<i class="dot"></i></span></h2><p class="rv" style="max-width: 420px">The same five practices, shaped to the rules your sector is inspected against.</p></div>
<ul class="chips rv">
@foreach ($industries as $industry)
<li><a href="{{ route('industries.show', $industry['slug']) }}" class="lnk">{{ $industry['nav'] }}</a></li>
@endforeach
</ul>
</div>
</section>

<section class="sec">
<div class="wrap split">
<h2 class="dl rm"><span>Credentials<i class="dot"></i></span></h2>
<div>
<ul class="soc-list rs" style="border-color: var(--ink)">
<li><span>ISO/IEC 27001</span><span>{{ setting('cred_iso_27001') }}</span></li>
<li><span>SOC 2</span><span>{{ setting('cred_soc_2') }}</span></li>
<li><span>CERT-In empanelment</span><span>{{ setting('cred_cert_in') }}</span></li>
<li><span>Based in</span><span>Ahmedabad, Gujarat, India</span></li>
</ul>
<p class="rv" style="margin-top: 32px; max-width: 62ch">Statuses are shown exactly as held. We do not describe a certification as in place before it is.</p>
</div>
</div>
</section>

<x-sections.strip />

</x-layout>
