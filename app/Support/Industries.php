<?php

namespace App\Support;

use Illuminate\Support\Collection;

/** The six sectors, read from config/industries.php. */
class Industries
{
    /** @return Collection<string, array> */
    public static function all(): Collection
    {
        return collect(config('industries'))
            ->map(fn (array $i, string $slug) => $i + ['slug' => $slug]);
    }

    public static function find(string $slug): ?array
    {
        $industry = config("industries.{$slug}");

        return $industry ? $industry + ['slug' => $slug] : null;
    }

    public static function slugs(): array
    {
        return array_keys(config('industries'));
    }
}
