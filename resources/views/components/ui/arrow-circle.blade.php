{{-- The arrow circle: the only icon in the design. Two stacked glyphs that swap on hover. --}}
@props(['onAcid' => false])
<span {{ $attributes->class(['ac', 'on-acid' => $onAcid]) }}><svg class="a1" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"></path></svg><svg class="a2" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"></path></svg></span>