{{--
    Case files: 2 cards and the second stats row.
    Ported verbatim from design/v7/index.html lines 778-800.
    Regenerate with scripts/split-prototype-html.mjs — do not retype markup.
--}}
<section class="sec" id="about">
<div class="wrap">
<div class="sec-t"><h2 class="dl rm"><span>Case files<i class="dot"></i></span></h2><p class="rv" style="max-width: 420px">Names, figures and outcomes are placeholders until confirmed.</p></div>
<div class="grid4 rs">
@foreach ($caseStudies as $case)
<a href="{{ route('insights.index') }}" class="case">
<div class="art{{ $case->art_inverted ? ' k' : '' }} rc"><div class="zoom"><x-dynamic-component :component="'art.'.$case->cover_art_key" /></div></div>
<div class="meta"><div><p class="lab-l">{{ $case->title }}</p><p class="lab-s" style="margin-top: 4px">{{ $case->tags }}</p></div><x-ui.arrow-circle /></div>
<p class="body">{{ $case->summary }}</p>
</a>
@endforeach
</div>
<div class="stats p rs">
<div><span class="stat">{{ setting('stat_audits_supported') }}</span><span class="lab-s">Audits supported<br>to certification</span></div>
<div><span class="stat">{{ setting('stat_mean_time_to_respond') }}</span><span class="lab-s">Mean time<br>to respond</span></div>
<div><span class="stat">{{ setting('stat_endpoints_monitored') }}</span><span class="lab-s">Endpoints<br>monitored</span></div>
<div><span class="stat"><span data-count="5">5</span></span><span class="lab-s">Practices,<br>one team</span></div>
</div>
</div>
</section>
