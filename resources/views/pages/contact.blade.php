{{--
    Contact us — launch brief §6. The closing strip carries the enquiry form
    and the contact details; this page frames it. No free 45-minute review and
    no response-time promise (brief §6, §8).
--}}
<x-layout
    title="Contact us — UB Shield"
    :description="config('site.contact_lead')"
    :schema="$schema"
>

<section class="phero" id="top">
<i class="hsl a" aria-hidden="true"></i>
<div class="wrap">

<nav class="crumbs" aria-label="Breadcrumb">
<a href="{{ route('home') }}">UB Shield</a>
<i aria-hidden="true">/</i>
<span aria-current="page">Contact us</span>
</nav>

<div class="phero-g solo">
<div>
<span class="chip">Contact us</span>
<h1>Let&rsquo;s discuss your network &amp; security <span class="tnw">requirements<i class="dot"></i></span></h1>
<p class="lead">{{ config('site.contact_lead') }}</p>
</div>
</div>

</div>
</section>

<section class="sec" style="padding-top: 0">
<div class="wrap split">
<h2 class="dl rm"><span>Where we <span class="tnw">work<i class="dot"></i></span></span></h2>
<div>
<ul class="soc-list rs" style="border-color: var(--ink)">
<li><span>Service locations</span><span>{{ config('site.locations') }}</span></li>
<li><span>Coverage</span><span>{{ config('site.coverage') }}</span></li>
</ul>
<div class="ask">
<p>Reporting a vulnerability in our own systems? Please use our <a href="{{ route('responsible-disclosure') }}" class="lnk">responsible disclosure</a> process instead.</p>
</div>
</div>
</div>
</section>

<x-sections.strip />

</x-layout>
