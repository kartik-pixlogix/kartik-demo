{{--
    Header: brand, nav, services dropdown, action, progress bar.
    Ported verbatim from design/v7/index.html lines 522-545.
    Regenerate with scripts/split-prototype-html.mjs — do not retype markup.
--}}
<header class="hdr">
<div class="wrap hdr-in">
<a href="{{ route('home') }}" class="brand" aria-label="UB Shield home"><img class="m" src="/images/brand/ub-monogram.png" alt=""><img class="w" src="/images/brand/ub-wordmark.png" alt="UB Shield"></a>
<nav class="nav" aria-label="Main">
<div class="dd">
<a href="{{ home_anchor('services') }}" aria-haspopup="true">Services <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="m5 9 7 7 7-7"></path></svg></a>
<div class="dd-m">
<a href="{{ route('services.show', 'offensive-security') }}"><span class="k">Secure</span><b>Offensive Security / VAPT</b><x-ui.arrow-circle /></a>
<a href="{{ route('services.show', 'data-privacy') }}"><span class="k">Privacy</span><b>Data Privacy</b><x-ui.arrow-circle /></a>
<a href="{{ route('services.show', 'governance-risk-compliance') }}"><span class="k">Compliance</span><b>Governance, Risk &amp; Compliance</b><x-ui.arrow-circle /></a>
<a href="{{ route('services.show', 'infrastructure-cloud') }}"><span class="k">Infra</span><b>Infrastructure &amp; Cloud</b><x-ui.arrow-circle /></a>
<a href="{{ route('services.show', 'managed-security-services') }}"><span class="k">MSS</span><b>Managed Security Services</b><x-ui.arrow-circle /></a>
</div>
</div>
<a href="{{ home_anchor('industries') }}">Industries</a>
<a href="{{ home_anchor('compliance') }}">Compliance</a>
<a href="{{ home_anchor('insights') }}">Resources</a>
<a href="{{ home_anchor('about') }}">About</a>
</nav>
<a href="#contact" class="act mag"><span class="lab-hide">Talk to an expert</span><i><svg class="a1" viewBox="0 0 24 24"><path d="M7 17 17 7M9 7h8v8"></path></svg><svg class="a2" viewBox="0 0 24 24"><path d="M7 17 17 7M9 7h8v8"></path></svg></i></a>
<button class="menu-btn" aria-label="Open menu" aria-expanded="false" aria-controls="mmenu" data-menu-toggle><i></i><i></i></button>
</div>
<i class="prog" aria-hidden="true"></i>
</header>
