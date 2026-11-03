<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * A logo in the trust strip's marquee.
 *
 * Read on every page render, so the visible set is cached as one collection
 * and dropped whenever a row changes — the same pattern as Setting.
 */
class Client extends Model
{
    use HasFactory;

    public const CACHE_KEY = 'clients.visible';

    protected $fillable = [
        'name', 'logo_path', 'url', 'industry', 'is_sample', 'is_visible', 'position',
    ];

    protected function casts(): array
    {
        return [
            'is_sample' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        $flush = fn () => Cache::forget(self::CACHE_KEY);
        static::saved($flush);
        static::deleted($flush);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true)->orderBy('position')->orderBy('id');
    }

    /**
     * The visible logos, as plain arrays.
     *
     * Deliberately not an Eloquent collection: the cache store serialises
     * whatever it is given, and a serialised model comes back as an
     * incomplete object when the class is not yet autoloaded — which is
     * exactly what happened the first time this was written. The view needs
     * two strings per row, so two strings per row is what gets cached.
     *
     * @return \Illuminate\Support\Collection<int, array{name: string, logo_path: string}>
     */
    public static function forMarquee(): Collection
    {
        $rows = Cache::rememberForever(
            self::CACHE_KEY,
            fn (): array => static::query()->visible()
                ->whereNotNull('logo_path')
                ->get(['name', 'logo_path'])
                ->map(fn (self $c): array => ['name' => $c->name, 'logo_path' => $c->logo_path])
                ->all(),
        );

        return collect($rows);
    }
}
