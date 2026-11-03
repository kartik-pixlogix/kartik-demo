<?php

namespace App\Support;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Initials avatars rendered locally as a data: URI.
 *
 * Filament ships with ui-avatars.com as the default provider, which means
 * every admin page load sends the signed-in user's NAME to a third party in a
 * query string, from the staff member's own browser. The CSP caught it.
 *
 * Whitelisting the domain would have silenced the warning without fixing the
 * leak. This renders the same thing locally instead: no outbound request, no
 * name disclosure, and it keeps working if that service is down. It is also
 * the advice UB Shield would give a client about an admin panel.
 */
class LocalAvatarProvider implements AvatarProvider
{
    public function get(Model $record): string
    {
        $name = trim((string) ($record->getAttribute('name') ?? ''));
        $initials = $this->initials($name);

        // Deterministic hue from the name, so each person keeps the same
        // colour, drawn from the brand's blues rather than an arbitrary one.
        $hue = 196 + (crc32($name) % 24);

        $svg = <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 96 96">
              <rect width="96" height="96" fill="hsl({$hue} 72% 34%)"/>
              <text x="48" y="48" fill="#F4F6F8" font-family="Instrument Sans, system-ui, sans-serif"
                    font-size="38" font-weight="600" text-anchor="middle" dominant-baseline="central">{$initials}</text>
            </svg>
            SVG;

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    private function initials(string $name): string
    {
        $parts = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($parts === []) {
            return '?';
        }

        $first = Str::upper(Str::substr($parts[0], 0, 1));
        $last = count($parts) > 1 ? Str::upper(Str::substr(end($parts), 0, 1)) : '';

        return e($first.$last);
    }
}
