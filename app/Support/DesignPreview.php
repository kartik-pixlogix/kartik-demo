<?php

namespace App\Support;

use Database\Seeders\SettingSeeder;

/**
 * Renders the site carrying the *design's* content instead of the client's.
 *
 * The pixel diff compares the build against `design/v7/index.html`, and that
 * file is full of placeholders — `[email address]`, `[XX]%`, dashed
 * "Client logo" boxes. For the first five phases the database held exactly
 * those strings, so the two matched and `visual:diff` could stay at 0 px.
 *
 * The moment real content goes in, it cannot. A real phone number is a
 * different width from `[phone number]`, and six client logos are not eight
 * dashed boxes. Left alone, the guarantee would quietly decay into "the
 * homepage differs for reasons nobody checked" — which is worse than no
 * guarantee, because the next real regression would hide inside it.
 *
 * So the diff stops depending on what is in the database. With this mode on,
 * settings resolve to the seeder's values — which are the prototype's, by
 * construction — and the client marquee falls back to its placeholders. The
 * harness captures with it on, and `visual:diff` goes back to being a pure
 * check on CSS and markup, which is the only thing it was ever meant to catch.
 *
 * Gated to local and testing by DesignPreviewMode. It is a rendering mode for
 * a regression harness, not a feature.
 */
class DesignPreview
{
    private static bool $active = false;

    /** @var array<string, string|null>|null */
    private static ?array $defaults = null;

    public static function activate(): void
    {
        self::$active = true;
    }

    public static function active(): bool
    {
        return self::$active;
    }

    /**
     * The value the prototype prints for a setting, or null if the seeder
     * does not know the key.
     */
    public static function setting(string $key): ?string
    {
        self::$defaults ??= collect(SettingSeeder::rows())
            ->mapWithKeys(fn (array $row) => [$row['key'] => $row['value']])
            ->all();

        return self::$defaults[$key] ?? null;
    }

    public static function knows(string $key): bool
    {
        self::setting($key);

        return array_key_exists($key, self::$defaults);
    }
}
