<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseStudy extends Model
{
    use HasFactory;

    protected $table = 'case_studies';

    protected $fillable = [
        'title', 'slug', 'tags', 'summary', 'body',
        'cover_art_key', 'art_inverted', 'is_published', 'position',
    ];

    protected function casts(): array
    {
        return ['art_inverted' => 'boolean', 'is_published' => 'boolean'];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
