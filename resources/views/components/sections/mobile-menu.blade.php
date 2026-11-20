{{--
    Mobile circular-reveal menu. Markup from design/v7/index.html lines
    547-555; items per the launch brief §7.
--}}
<nav class="mmenu" id="mmenu" aria-label="Mobile">
<a href="{{ route('home') }}">Home</a>
<a href="{{ route('about') }}">About us</a>
<a href="{{ home_anchor('services') }}">Services</a>
<a href="{{ home_anchor('industries') }}">Industries</a>
<a href="{{ home_anchor('approach') }}">Our approach</a>
<a href="{{ route('contact') }}">Contact us</a>
<p>{{ config('site.eyebrow') }}</p>
</nav>
