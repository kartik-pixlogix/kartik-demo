<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * The hand-drawn illustrations available to content records.
 *
 * There is no image upload anywhere in this admin, by design: every
 * illustration is inline SVG that shares the page's gradients, clip paths and
 * animation classes (docs/inventory.md §6, §8). A dropdown of existing artwork
 * keeps that intact and makes it impossible to drop a stock photo into a design
 * that has none.
 *
 * Options are read from the components themselves, so adding a new
 * resources/views/components/art/*.blade.php is all it takes to offer it.
 */
class CoverArt
{
    /** @return array<string, string> key => label */
    public static function all(): array
    {
        $dir = resource_path('views/components/art');

        if (! File::isDirectory($dir)) {
            return [];
        }

        return collect(File::files($dir))
            ->map(fn ($file) => Str::before($file->getFilename(), '.blade.php'))
            ->sort()
            ->mapWithKeys(fn (string $key) => [$key => Str::headline($key)])
            ->all();
    }

    /** Artwork drawn for article cards. */
    public static function forInsights(): array
    {
        return self::only('note-');
    }

    /** Artwork drawn for case-file cards. */
    public static function forCaseStudies(): array
    {
        return self::only('case-');
    }

    private static function only(string $prefix): array
    {
        $matching = collect(self::all())->filter(
            fn (string $label, string $key) => str_starts_with($key, $prefix)
        );

        // Anything else is still selectable, just listed after the obvious ones.
        return $matching->union(self::all())->all();
    }
}
