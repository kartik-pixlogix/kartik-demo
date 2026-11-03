<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Read a site setting. The whole table is cached as one map, so calling this
     * a dozen times while rendering the homepage costs one query at most.
     */
    function setting(string $key, ?string $default = null): ?string
    {
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
