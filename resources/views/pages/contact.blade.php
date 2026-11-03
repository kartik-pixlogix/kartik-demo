{{--
    Contact. The closing strip already carries the form and the contact
    details, so this page frames it rather than repeating it.
--}}
<x-layout
    title="Talk to an expert — UB Shield"
    description="Forty-five minutes with a senior consultant. You leave with your five biggest risks and the order to fix them in."
    :schema="$schema"
>

<section class="phero" id="top">
<i class="hsl a" aria-hidden="true"></i>
<div class="wrap">

<nav class="crumbs" aria-label="Breadcrumb">
<a href="{{ route('home') }}">UB Shield</a>
<i aria-hidden="true">/</i>
<span aria-current="page">Talk to an expert</span>
</nav>

<div class="phero-g solo">
<div>
<span class="chip">Contact</span>
<h1>Talk to an expert<i class="dot"></i></h1>
<p class="lead">No gatekeeping and no discovery call about a discovery call. You speak to someone who does the work.</p>
</div>
</div>

</div>
</section>

<section class="sec" style="padding-top: 0">
<div class="wrap split">
<h2 class="dl rm"><span>What happens next<i class="dot"></i></span></h2>
<div>
<ul class="rows rs">
<li><span class="n" aria-hidden="true"></span><div><h3>You send the form</h3><p>Four fields. We only ask for what we need to route it to the right person.</p></div></li>
<li><span class="n" aria-hidden="true"></span><div><h3>We reply within one business day</h3><p>From a named consultant, not a shared inbox. If your question is quick, you get an answer rather than a meeting invitation.</p></div></li>
<li><span class="n" aria-hidden="true"></span><div><h3>Forty-five minutes, scoped to you</h3><p>We go through your estate, what you are inspected against and where the exposure is. Bring whoever owns the systems.</p></div></li>
<li><span class="n" aria-hidden="true"></span><div><h3>You leave with a written summary</h3><p>Your five biggest risks and the order to fix them in. No obligation, and it is yours whether or not you engage us.</p></div></li>
</ul>
<div class="ask">
<p>Reporting a vulnerability in our own systems? Please use our <a href="{{ route('responsible-disclosure') }}" class="lnk">responsible disclosure</a> process instead.</p>
</div>
</div>
</div>
</section>

<x-sections.strip />

</x-layout>
