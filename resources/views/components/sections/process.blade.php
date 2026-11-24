{{--
    Our approach: sticky counter and 4 steps. Markup from design/v7/index.html
    lines 658-705; steps from the launch brief §5 (config/site.php). The
    prototype's per-step "Output" chips are dropped: the brief names none.
--}}
<section class="sec" id="approach">
<div class="wrap proc">
<div class="proc-l">
<h2 class="dl rm"><span>Our <span class="tnw">approach<i class="dot"></i></span></span></h2>
<div class="counter" aria-hidden="true"><span data-stepno>01</span><i></i><span class="tot">{{ str_pad((string) count(config('site.approach')), 2, '0', STR_PAD_LEFT) }}</span></div>
<p class="rv" style="max-width: 420px">{{ config('site.approach_note') }} {{ config('site.delivery') }}</p>
</div>
<div class="steps">
@foreach (config('site.approach') as $i => [$title, $detail])

<div class="step{{ $i === 0 ? ' on' : '' }}" data-step="{{ $i }}">
<span class="n">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
<div>
<h3 class="step-h">{{ title_dot($title) }}</h3>
<p class="b">{{ $detail }}</p>
</div>
</div>
@endforeach

</div>
</div>
</section>
