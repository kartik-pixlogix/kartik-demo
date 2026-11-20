{{--
    Header: brand, nav, services dropdown, action, progress bar.
    Navigation per the launch brief §7.
    Ported verbatim from design/v7/index.html lines 522-545.
    Regenerate with scripts/split-prototype-html.mjs — do not retype markup.
--}}
<header class="hdr">
<div class="wrap hdr-in">
<a href="{{ route('home') }}" class="brand" aria-label="UB Shield home"><img class="m" src="/images/brand/ub-shield.webp" alt="" width="752" height="775" fetchpriority="high" decoding="async"><img class="w" src="/images/brand/ub-wordmark.webp" alt="UB Shield" width="639" height="59" fetchpriority="high" decoding="async"></a>
<nav class="nav" aria-label="Main">
<a href="{{ route('home') }}">Home</a>
<a href="{{ route('about') }}">About us</a>
<div class="dd">
<a href="{{ home_anchor('services') }}" aria-haspopup="true">Services <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="m5 9 7 7 7-7"></path></svg></a>
<div class="dd-m">
@foreach (\App\Support\Practices::all() as $slug => $service)
<a href="{{ route('services.show', $slug) }}"><span class="k">{{ $service['kicker'] }}</span><b>{{ $service['nav'] }}</b><x-ui.arrow-circle /></a>
@endforeach
</div>
</div>
<a href="{{ home_anchor('industries') }}">Industries</a>
<a href="{{ home_anchor('approach') }}">Our approach</a>
</nav>
<a href="{{ route('contact') }}" class="act mag"><span class="lab-hide">Contact us</span><i aria-hidden="true"><svg class="a1" viewBox="0 0 24 24"><path d="M7 17 17 7M9 7h8v8"></path></svg><svg class="a2" viewBox="0 0 24 24"><path d="M7 17 17 7M9 7h8v8"></path></svg></i></a>
<button class="menu-btn" aria-label="Open menu" aria-expanded="false" aria-controls="mmenu" data-menu-toggle><i></i><i></i></button>
</div>
<i class="prog" aria-hidden="true"></i>
</header>
