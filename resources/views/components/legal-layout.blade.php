{{--
    Shared shell for the legal pages. Narrower measure than an article: legal
    text is read in order rather than skimmed.
--}}
@props(['pageTitle', 'lastUpdated', 'description', 'schema' => null])
<x-layout :title="$pageTitle.' — UB Shield'" :description="$description" :schema="$schema">

<section class="phero" id="top">
<i class="hsl a" aria-hidden="true"></i>
<div class="wrap">

<nav class="crumbs" aria-label="Breadcrumb">
<a href="{{ route('home') }}">UB Shield</a>
<i aria-hidden="true">/</i>
<span aria-current="page">{{ $pageTitle }}</span>
</nav>

<div class="phero-g solo">
<div>
<span class="chip">Legal</span>
<h1>{{ $pageTitle }}<i class="dot"></i></h1>
<p class="lead">Last updated {{ $lastUpdated }}.</p>
</div>
</div>

</div>
</section>

<section class="sec" style="padding-top: 0">
<div class="wrap split">
{{-- the sticky column cross-navigates the legal set rather than repeating the title --}}
<nav class="ameta" aria-label="Legal documents">
<div><span class="lab-s">Last updated</span><span class="lab-m">{{ $lastUpdated }}</span></div>
@foreach ([['privacy', 'Privacy policy'], ['terms', 'Terms of use'], ['responsible-disclosure', 'Responsible disclosure']] as [$route, $label])
<div>
@if ($pageTitle === $label)
<span class="lab-m" aria-current="page">{{ $label }}</span>
@else
<a href="{{ route($route) }}" class="lnk lab-m">{{ $label }}</a>
@endif
</div>
@endforeach
</nav>
<div class="legal prose rv">
<p><span class="flag">Legal review required</span></p>
<p>This text is a placeholder. It sets out the structure the final document needs and must be reviewed and replaced by the client&rsquo;s counsel before launch. Every section below is marked <strong>[legal review]</strong>.</p>
<hr>
{{ $slot }}
</div>
</div>
</section>

<x-sections.strip />

</x-layout>
