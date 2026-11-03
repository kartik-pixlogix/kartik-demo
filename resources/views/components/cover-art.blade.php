{{--
    The 5:4 artwork frame on a field note or case study card.

    Two sources, in order:

      1. An uploaded cover image, if the record has one.
      2. The hand-drawn SVG named by cover_art_key — the default, and what
         every record shipped with.

    The frame, the `k` inversion and the .zoom hover scale are the prototype's
    and are identical either way, so swapping one record to an image does not
    disturb the row it sits in.

    Under design-preview the SVG always renders, so the pixel diff is
    unaffected by whatever has been uploaded. See App\Support\DesignPreview.

    @param $record   an Insight or CaseStudy
    @param $decorative  true on pages where a heading already names the item,
                        so the image is hidden from assistive tech instead of
                        repeating the title
--}}
@props(['record', 'decorative' => false])
@php
    $image = \App\Support\DesignPreview::active() ? null : $record->cover_image;
@endphp
<div class="art{{ $record->art_inverted ? ' k' : '' }} rc" @if ($decorative) aria-hidden="true" @endif><div class="zoom">@if ($image)<img class="cover" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($image) }}" alt="{{ $decorative ? '' : $record->title }}" width="1000" height="800" loading="lazy" decoding="async">@else<x-dynamic-component :component="'art.'.$record->cover_art_key" />@endif</div></div>
