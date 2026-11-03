{{--
    Field note. Long-form body at Instrument Sans 19/30 as the handoff
    specifies, in a measure capped near 68 characters.
--}}
<x-layout
    :title="($insight->seo_title ?: $insight->title).' — UB Shield'"
    :description="$insight->seo_description ?: $insight->excerpt"
    :schema="$schema"
    ogType="article"
>

<article>

<section class="phero" id="top">
<i class="hsl a" aria-hidden="true"></i>
<div class="wrap">

<nav class="crumbs" aria-label="Breadcrumb">
<a href="{{ route('home') }}">UB Shield</a>
<i aria-hidden="true">/</i>
<a href="{{ route('insights.index') }}">Field notes</a>
<i aria-hidden="true">/</i>
<span aria-current="page">{{ $insight->category }}</span>
</nav>

<div class="phero-g">
<div>
<span class="chip">{{ $insight->category }}</span>
<h1 class="ahead">{{ $insight->title }}</h1>
</div>
<div class="art{{ $insight->art_inverted ? ' k' : '' }} rc" aria-hidden="true"><div class="zoom"><x-dynamic-component :component="'art.'.$insight->cover_art_key" /></div></div>
</div>

</div>
</section>

<section class="sec" style="padding-top: 0">
<div class="wrap split">
<div class="ameta">
<div><span class="lab-s">Published</span><span class="lab-m">{{ $insight->date_label ?: $insight->published_at?->format('d M Y') }}</span></div>
<div><span class="lab-s">Category</span><span class="lab-m">{{ $insight->category }}</span></div>
<div><a href="{{ route('insights.index') }}" class="lnk lab-m">All field notes</a></div>
</div>
<div>
@if ($insight->excerpt)
<p class="lead rv" style="max-width: 62ch; margin-bottom: 40px">{{ $insight->excerpt }}</p>
@endif
<div class="prose rv">
{!! $insight->body !!}
</div>
</div>
</div>
</section>

</article>

@if ($more->isNotEmpty())
<section class="sec">
<div class="wrap">
<div class="sec-t"><h2 class="dl rm"><span>More field notes<i class="dot"></i></span></h2></div>
<div class="grid4 rs">
@foreach ($more as $other)
<a href="{{ route('insights.show', $other->slug) }}" class="post"><div class="art{{ $other->art_inverted ? ' k' : '' }} rc"><div class="zoom"><x-dynamic-component :component="'art.'.$other->cover_art_key" /></div></div><div class="meta"><p class="lab-l">{{ $other->title }}</p><span class="lab-s">{{ $other->meta_line }}</span></div></a>
@endforeach
</div>
</div>
</section>
@endif

<x-sections.strip />

</x-layout>
