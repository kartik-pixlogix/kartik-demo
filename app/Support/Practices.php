<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * The five practices, read from config/practices.php.
 *
 * Code-managed rather than a Filament resource: BUILD.md puts Leads, Insights,
 * Case studies and Settings in the admin, and these pages are structural.
 */
class Practices
{
    /** @return Collection<string, array> */
    public static function all(): Collection
    {
        return collect(config('practices'));
    }

    public static function find(string $slug): ?array
    {
        $practice = config("practices.{$slug}");

        return $practice ? $practice + ['slug' => $slug] : null;
    }

    public static function slugs(): array
    {
        return array_keys(config('practices'));
    }

    /** The other four, for cross-linking at the foot of a service page. */
    public static function except(string $slug): Collection
    {
        return self::all()
            ->except($slug)
            ->map(fn (array $p, string $s) => $p + ['slug' => $s]);
    }
}
