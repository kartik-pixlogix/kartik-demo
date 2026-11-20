<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * The five most recent requests, so the dashboard answers "has anything come
 * in?" without a navigation step. Read-only, like the Leads resource itself.
 */
class RecentLeads extends TableWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Latest requests')
            ->description('The five most recent submissions. Times are IST.')
            ->query(fn (): Builder => Lead::query()->latest())
            ->paginated(false)
            ->defaultPaginationPageOption(5)
            ->queryStringIdentifier('recent-leads')
            ->modifyQueryUsing(fn (Builder $query) => $query->limit(5))
            ->emptyStateHeading('No requests yet')
            ->emptyStateDescription('Submissions from the enquiry form on the site appear here.')
            ->emptyStateIcon(Heroicon::OutlinedInbox)
            ->columns([
                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('d M Y, H:i')
                    ->timezone('Asia/Kolkata'),
                TextColumn::make('name'),
                TextColumn::make('company')->placeholder('—'),
                TextColumn::make('email')
                    ->label('Work email')
                    ->copyable()
                    ->copyMessage('Copied'),
                TextColumn::make('service')->badge()->color('info')->placeholder('—'),
                TextColumn::make('notified_at')
                    ->label('Notified')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Sent' : 'Queued')
                    ->color(fn ($state) => $state ? 'gray' : 'info'),
            ])
            ->recordActions([
                Action::make('view')
                    ->icon(Heroicon::OutlinedEye)
                    ->url(fn (Lead $record): string => LeadResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
