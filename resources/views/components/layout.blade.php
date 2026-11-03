@props([
    'title' => 'UB Shield — Cybersecurity & Compliance, Ahmedabad',
    'description' => 'UB Shield tests, fixes, monitors and certifies Indian enterprises: VAPT, DPDPA privacy, ISO 27001 / SOC 2 / RBI / SEBI compliance, cloud security and a 24/7 managed SOC.',
    // The floating industry artwork belongs to the homepage industries list;
    // every other page would ship six unused SVGs.
    'preview' => false,
    // JSON-LD for this page, already wrapped by App\Support\Seo::graph().
    'schema' => null,
    'ogType' => 'website',
    'noindex' => false,
])
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<link rel="canonical" href="{{ url()->current() }}">
@if ($noindex)
<meta name="robots" content="noindex, nofollow">
@endif

<meta property="og:site_name" content="UB Shield">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:locale" content="en_IN">
<meta property="og:image" content="{{ url('/images/brand/og-default.png') }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="UB Shield — tested, fixed, watched, proven.">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ url('/images/brand/og-default.png') }}">

<link rel="icon" href="/images/brand/ub-monogram.png">
<link rel="apple-touch-icon" href="/images/brand/ub-monogram.png">
<meta name="theme-color" content="#061522">
{{-- the hero headline only fits its column once the display face is loaded, so preload it --}}
<link rel="preload" href="/fonts/sofia-sans-extra-condensed-var.woff2" as="font" type="font/woff2" crossorigin>
@vite(['resources/css/app.css', 'resources/js/app.js'])
@if ($schema)
<script type="application/ld+json" nonce="{{ Vite::cspNonce() }}">{!! $schema !!}</script>
@endif
</head>
<body>
<div class="ds6" id="ubs">

<a href="#main" class="skip">Skip to content</a>

<x-svg-defs />

<x-ui.cursor />
@if ($preview)

<x-ui.industry-preview />
@endif

<x-sections.header />

<x-sections.mobile-menu />

<main id="main" tabindex="-1">
{{ $slot }}
</main>

<x-sections.footer />

</div>
</body>
</html>
