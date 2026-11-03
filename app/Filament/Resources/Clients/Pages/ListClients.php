<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Resources\Pages\ListRecords;

/**
 * Create and edit happen in a modal rather than on their own pages — a client
 * logo is four fields and a file, and a full page round-trip for that is
 * friction with nothing in return.
 */
class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    public function getSubheading(): ?string
    {
        $samples = \App\Models\Client::query()->where('is_sample', true)->count();

        if ($samples === 0) {
            return null;
        }

        return "{$samples} of these are placeholders seeded for review. "
            .'Replace or delete them before the site goes live — they are not real clients.';
    }
}
