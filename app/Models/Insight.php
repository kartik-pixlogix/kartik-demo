<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insight extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'category', 'excerpt', 'body',
        'cover_art_key', 'cover_image', 'art_inverted', 'date_label', 'published_at',
        'seo_title', 'seo_description', 'position',
    ];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'art_inverted' => 'boolean'];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /** The kicker under a card: "Advisory / [DD MMM 2026]". */
    public function getMetaLineAttribute(): string
    {
        return trim($this->category.' / '.$this->date_label);
    }
}
