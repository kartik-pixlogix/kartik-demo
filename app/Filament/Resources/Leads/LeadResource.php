<?php

namespace App\Filament\Resources\Leads;

use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Filament\Resources\Leads\Pages\ViewLead;
use App\Models\Lead;
use App\Support\LeadCsv;
use BackedEnum;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * Form submissions. Read-only by design: a lead is a record of what someone
 * actually sent, so nobody should be able to edit or hand-create one. There is
 * no create page and no edit page — only the list, a detail view and export.
 */
class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static ?string $recordTitleAttribute = 'company';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Leads';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Lead::query()->where('created_at', '>=', now()->subDays(7))->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Received in the last 7 days';
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Request')
                ->columns(2)
                ->schema([
                    TextEntry::make('name')->label('Name'),
                    TextEntry::make('email')->label('Business email')->copyable(),
                    TextEntry::make('company')->label('Company / organization'),
                    TextEntry::make('city')->label('City')->placeholder('—'),
                    TextEntry::make('service')->label('Service interest')->badge(),
                    TextEntry::make('requirement')->label('Brief requirement')->placeholder('—')->columnSpanFull(),
                    TextEntry::make('consented_at')
                        ->label('Consent to be contacted')
                        ->dateTime('d M Y, H:i')
                        ->timezone('Asia/Kolkata')
                        ->placeholder('Not recorded'),
                    TextEntry::make('created_at')
                        ->label('Received')
                        ->dateTime('d M Y, H:i')
                        ->timezone('Asia/Kolkata')
                        ->suffix(' IST'),
                    TextEntry::make('notified_at')
                        ->label('Notification queued')
                        ->dateTime('d M Y, H:i')
                        ->timezone('Asia/Kolkata')
                        ->placeholder('Not sent — check the mail queue'),
                ]),

            Section::make('Attribution')
                ->columns(2)
                ->collapsed()
                ->schema([
                    TextEntry::make('source_page')->label('Source page')->placeholder('—'),
                    TextEntry::make('utm_source')->label('UTM source')->placeholder('—'),
                    TextEntry::make('utm_medium')->label('UTM medium')->placeholder('—'),
                    TextEntry::make('utm_campaign')->label('UTM campaign')->placeholder('—'),
                    TextEntry::make('utm_term')->label('UTM term')->placeholder('—'),
                    TextEntry::make('utm_content')->label('UTM content')->placeholder('—'),
                ]),

            Section::make('Technical')
                ->columns(1)
                ->collapsed()
                ->schema([
                    TextEntry::make('ip_hash')
                        ->label('IP hash')
                        ->placeholder('—')
                        ->helperText('The IP address itself is never stored — only this keyed hash, so repeat submissions from one source can be spotted without holding personal data.'),
                    TextEntry::make('user_agent')->label('User agent')->placeholder('—'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('company')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('d M Y, H:i')
                    ->timezone('Asia/Kolkata')
                    ->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('company')->searchable()->sortable(),
                TextColumn::make('email')->label('Work email')->searchable()->copyable(),
                TextColumn::make('service')->badge()->searchable(),
                TextColumn::make('utm_source')
                    ->label('Campaign')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('notified_at')
                    ->label('Notified')
                    ->dateTime('d M Y, H:i')
                    ->timezone('Asia/Kolkata')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('service')
                    ->options(fn () => array_combine(
                        \App\Http\Requests\ReviewRequest::services(),
                        \App\Http\Requests\ReviewRequest::services()
                    )),
                Filter::make('recent')
                    ->label('Last 7 days')
                    ->query(fn (Builder $query) => $query->where('created_at', '>=', now()->subDays(7))),
                Filter::make('not_notified')
                    ->label('Notification not sent')
                    ->query(fn (Builder $query) => $query->whereNull('notified_at')),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('export')
                        ->label('Export selected to CSV')
                        ->icon(Heroicon::OutlinedArrowDownTray)
                        ->action(fn (Collection $records) => LeadCsv::download(
                            $records,
                            'ub-shield-leads-'.Carbon::now()->format('Y-m-d').'.csv'
                        ))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeads::route('/'),
            'view' => ViewLead::route('/{record}'),
        ];
    }
}
