<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Key/value site settings: contact details, the [XX] statistics and the
 * credential statuses. Read on every homepage render, so the whole table is
 * cached as one map and the cache is dropped whenever a row changes.
 */
class Setting extends Model
{
    public const CACHE_KEY = 'settings.map';

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['key', 'value', 'group', 'label', 'hint'];

    protected static function booted(): void
    {
        $flush = fn () => Cache::forget(self::CACHE_KEY);
        static::saved($flush);
        static::deleted($flush);
    }

    /** @return array<string, string|null> */
    public static function map(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => static::query()->pluck('value', 'key')->all());
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        return static::map()[$key] ?? $default;
    }
}
