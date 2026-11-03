{{-- Field notes index. --}}
<x-layout
    title="Field notes — UB Shield"
    description="Advisories, guides and field notes on DPDPA, SEBI CSCRF, cloud security and the rest of what we run into."
    :schema="$schema"
>

<section class="phero" id="top">
<i class="hsl a" aria-hidden="true"></i>
<div class="wrap">

<nav class="crumbs" aria-label="Breadcrumb">
<a href="{{ route('home') }}">UB Shield</a>
<i aria-hidden="true">/</i>
<span aria-current="page">Field notes</span>
</nav>

<div class="phero-g solo">
<div>
<span class="chip">Resources</span>
<h1>Field notes<i class="dot"></i></h1>
<p class="lead">What we run into on engagements, written up while it is still fresh. Advisories when the law moves, guides when a framework needs unpicking.</p>
</div>
</div>

</div>
</section>

<section class="sec" style="padding-top: 0">
<div class="wrap">
@if ($insights->isEmpty())
<p class="lead">No field notes published yet.</p>
@else
<div class="grid4 rs">
@foreach ($insights as $insight)
<a href="{{ route('insights.show', $insight->slug) }}" class="post"><x-cover-art :record="$insight" /><div class="meta"><p class="lab-l">{{ $insight->title }}</p><span class="lab-s">{{ $insight->meta_line }}</span></div></a>
@endforeach
</div>
@endif
</div>
</section>

<x-sections.strip />

</x-layout>
