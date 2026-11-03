@props([
    'title' => 'UB Shield — Cybersecurity & Compliance, Ahmedabad',
    'description' => 'UB Shield tests, fixes, monitors and certifies Indian enterprises: VAPT, DPDPA privacy, ISO 27001 / SOC 2 / RBI / SEBI compliance, cloud security and a 24/7 managed SOC.',
    // The floating industry artwork belongs to the homepage industries list;
    // every other page would ship six unused SVGs.
    'preview' => false,
])
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<link rel="icon" href="/images/brand/ub-monogram.png">
{{-- the hero headline only fits its column once the display face is loaded, so preload it --}}
<link rel="preload" href="/fonts/sofia-sans-extra-condensed-var.woff2" as="font" type="font/woff2" crossorigin>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="ds6" id="ubs">

<x-svg-defs />

<x-ui.cursor />
@if ($preview)

<x-ui.industry-preview />
@endif

<x-sections.header />

<x-sections.mobile-menu />

{{ $slot }}

<x-sections.footer />

</div>
</body>
</html>
