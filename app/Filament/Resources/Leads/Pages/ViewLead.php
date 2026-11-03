<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use Filament\Resources\Pages\ViewRecord;

class ViewLead extends ViewRecord
{
    protected static string $resource = LeadResource::class;

    /** Read-only: no edit or delete action on a submission record. */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
