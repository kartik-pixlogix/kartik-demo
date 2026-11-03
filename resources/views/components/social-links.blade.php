{{--
    The footer's "Follow" column.

    The prototype draws two links, LinkedIn and X, both pointing at #top
    because it had nowhere real to send anyone. Real profiles come from Site
    settings; a platform with no URL is left out entirely rather than linked
    to nothing, so the column never offers a dead link.

    Under design-preview the prototype's two placeholder links render verbatim,
    so the pixel diff is unaffected by which profiles exist — see
    App\Support\DesignPreview.
--}}
@php
    $platforms = [
        'social_linkedin' => 'LinkedIn',
        'social_instagram' => 'Instagram',
        'social_x' => 'X',
        'social_facebook' => 'Facebook',
        'social_youtube' => 'YouTube',
    ];

    $links = \App\Support\DesignPreview::active()
        ? collect()
        : collect($platforms)
            ->map(fn (string $label, string $key) => ['label' => $label, 'url' => setting($key)])
            ->reject(fn (array $l) => is_placeholder($l['url']))
            ->values();
@endphp
<div><h3>Follow<i class="dot"></i></h3><ul>
@if ($links->isEmpty())
<li><a href="#top">LinkedIn</a></li><li><a href="#top">X</a></li>
@else
@foreach ($links as $link)<li><a href="{{ $link['url'] }}" target="_blank" rel="noopener me">{{ $link['label'] }}</a></li>@endforeach
@endif
</ul></div>
