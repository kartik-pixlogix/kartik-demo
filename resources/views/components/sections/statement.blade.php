{{--
    About UB Shield. The prototype's statement block (design/v7/index.html
    lines 650-656): word-reveal line with the brush stroke, then body copy.
    Launch brief §2: "About UB Shield" and "Why work with UB Shield?".
    The large line quotes the brief's own description ("secure, reliable and
    resilient IT environments").
--}}
<section class="stm" id="about-ub-shield" aria-labelledby="about-ub-shield-h">
<div class="wrap" style="position: relative">
<span class="ch" style="right: 120px; top: -40px"></span><span class="ch" style="left: 40%; bottom: -60px"></span>
<h2 class="chip" id="about-ub-shield-h" style="margin-bottom: 32px">About UB Shield</h2>
<p class="rw"><span class="w">Secure,</span> <span class="w">reliable</span><br><span class="w">and</span> <span class="w brush">resilient.<x-art.brush-stroke /></span></p>
<div class="rv about-copy" style="max-width: 640px; margin-top: 40px">
@foreach (config('site.about') as $paragraph)
<p>{{ $paragraph }}</p>
@endforeach
</div>
</div>
</section>

<section class="sec" aria-labelledby="why-h">
<div class="wrap split">
<h2 class="dl rm" id="why-h"><span>Why work with UB <span class="tnw">Shield<i class="dot"></i></span></span></h2>
<ul class="rows rs">
@foreach (config('site.why') as $point)
<li><span class="n" aria-hidden="true"></span><div><p class="why-p">{{ $point }}</p></div></li>
@endforeach
</ul>
</div>
</section>
