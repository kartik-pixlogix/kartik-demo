<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'company', 'service',
        'source_page', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
        'ip_hash', 'user_agent', 'notified_at',
    ];

    protected function casts(): array
    {
        return ['notified_at' => 'datetime'];
    }

    /**
     * Keyed hash of an IP address. Enough to spot a flood from one source
     * without storing the address itself.
     */
    public static function hashIp(?string $ip): ?string
    {
        return $ip === null ? null : hash_hmac('sha256', $ip, config('app.key'));
    }
}
