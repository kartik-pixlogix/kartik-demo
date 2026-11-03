{{--
    Industries: 6 hover rows.
    Ported verbatim from design/v7/index.html lines 741-760.
    Regenerate with scripts/split-prototype-html.mjs — do not retype markup.
--}}
<section class="sec" id="industries">
<div class="wrap">
<div class="sec-t"><h2 class="dl rm"><span>Industries<i class="dot"></i></span></h2><p class="rv" style="max-width: 420px">The same five practices, shaped to the rules your sector is inspected against.</p></div>
<div class="inds rs">

<a href="{{ route('industries.show', 'banking-finance') }}" class="ind-row" data-v="1"><span class="lab-s">01</span><h3>Banking &amp; finance</h3><p>RBI and SEBI frameworks, payment data and vendor risk.</p><x-ui.arrow-circle /></a>

<a href="{{ route('industries.show', 'healthcare') }}" class="ind-row" data-v="2"><span class="lab-s">02</span><h3>Healthcare</h3><p>Patient data under DPDPA, and HIPAA for US work.</p><x-ui.arrow-circle /></a>

<a href="{{ route('industries.show', 'saas-it') }}" class="ind-row" data-v="3"><span class="lab-s">03</span><h3>SaaS &amp; IT</h3><p>SOC 2 and ISO 27001 so security reviews stop stalling deals.</p><x-ui.arrow-circle /></a>

<a href="{{ route('industries.show', 'manufacturing') }}" class="ind-row" data-v="4"><span class="lab-s">04</span><h3>Manufacturing</h3><p>Plant and office networks kept apart, supplier access controlled.</p><x-ui.arrow-circle /></a>

<a href="{{ route('industries.show', 'education') }}" class="ind-row" data-v="5"><span class="lab-s">05</span><h3>Education</h3><p>Student and parent data, consent records and campus networks.</p><x-ui.arrow-circle /></a>

<a href="{{ route('industries.show', 'e-commerce') }}" class="ind-row" data-v="6"><span class="lab-s">06</span><h3>E-commerce</h3><p>PCI DSS, checkout integrity and account-takeover defence.</p><x-ui.arrow-circle /></a>

</div>
</div>
</section>
