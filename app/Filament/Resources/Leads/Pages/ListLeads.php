<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Support\LeadCsv;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    /** No CreateAction: leads are a record of what visitors sent, not something staff author. */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportAll')
                ->label('Export to CSV')
                ->icon(Heroicon::OutlinedArrowDownTray)
                // Exports what the table currently shows, so a filter or search
                // narrows the file too. Lazily cursored to keep memory flat.
                ->action(fn () => LeadCsv::download(
                    $this->getFilteredSortedTableQuery()->cursor(),
                    'ub-shield-leads-'.Carbon::now()->format('Y-m-d').'.csv'
                )),
        ];
    }
}
