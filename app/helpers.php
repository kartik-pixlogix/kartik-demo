<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Read a site setting. The whole table is cached as one map, so calling this
     * a dozen times while rendering the homepage costs one query at most.
     */
    function setting(string $key, ?string $default = null): ?string
    {
        // The visual harness renders with the design's own placeholder values
        // so the pixel diff measures CSS and markup, not content. Local and
        // testing only — see App\Support\DesignPreview.
        if (App\Support\DesignPreview::active() && App\Support\DesignPreview::knows($key)) {
            return App\Support\DesignPreview::setting($key) ?? $default;
        }

        return Setting::get($key, $default);
    }
}

if (! function_exists('is_placeholder')) {
    /**
     * Placeholders are written in square brackets — "[email address]",
     * "[XX]%", "[status]". Nothing in brackets is real data, so the site must
     * not treat it as such (no mailto: to "[email address]", no JSON-LD claiming
     * an unconfirmed certification). See docs/inventory.md §15.
     */
    function is_placeholder(?string $value): bool
    {
        return $value === null || $value === '' || str_contains($value, '[');
    }
}

if (! function_exists('home_anchor')) {
    /**
     * A link to a section that only exists on the homepage.
     *
     * On the homepage it stays a plain fragment, so the browser scrolls rather
     * than reloading. Anywhere else it becomes a full URL, so the link still
     * works. Fragments for sections that appear on every page — `#contact`,
     * which the closing strip carries — must NOT use this: they should stay
     * local to whatever page the visitor is on.
     */
    function home_anchor(string $fragment): string
    {
        return request()->routeIs('home')
            ? '#'.$fragment
            : route('home').'#'.$fragment;
    }
}

if (! function_exists('contact_href')) {
    /**
     * A real mailto:/tel: link once the client has filled the value in, and the
     * prototype's on-page anchor until then.
     */
    function contact_href(?string $value, string $scheme): string
    {
        if (is_placeholder($value)) {
            return '#contact';
        }

        return $scheme === 'tel'
            ? 'tel:'.preg_replace('/[^+0-9]/', '', $value)
            : 'mailto:'.$value;
    }
}
