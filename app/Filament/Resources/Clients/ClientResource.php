<?php

namespace App\Filament\Resources\Clients;

use App\Filament\Resources\Clients\Pages\ListClients;
use App\Models\Client;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

/**
 * The logos in the trust strip's marquee.
 *
 * The only place in this admin with an image upload. Everywhere else the
 * artwork is inline SVG from the design system (see App\Support\CoverArt) —
 * but a client's logo is their mark, not ours, so it has to be uploadable.
 *
 * Rows seeded for review carry `is_sample`. The list flags them and refuses
 * to let them blend in with real ones, because a placeholder logo reaching
 * the live site would be a claim the company has not earned.
 */
class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Client logos';

    protected static ?string $modelLabel = 'client logo';

    public static function getNavigationBadge(): ?string
    {
        $samples = Client::query()->where('is_sample', true)->count();

        return $samples > 0 ? (string) $samples : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Placeholder logos still to be replaced';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Logo')
                ->description('Shown in the scrolling strip under the hero. The strip shows eight boxes; fewer than eight clients and the set repeats to fill the loop.')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(120)
                        ->helperText('Used as the image\'s alt text, so write it as the company writes it.'),

                    TextInput::make('industry')
                        ->maxLength(120)
                        ->helperText('Not shown on the site — for your own reference in this list.'),

                    FileUpload::make('logo_path')
                        ->label('Logo file')
                        ->image()
                        ->disk('public')
                        ->directory('clients')
                        ->visibility('public')
                        ->imageEditor()
                        ->maxSize(1024)
                        ->acceptedFileTypes(['image/png', 'image/svg+xml', 'image/webp'])
                        ->columnSpanFull()
                        ->helperText('PNG, WebP or SVG with a transparent background. It sits in a 200x72 box and is scaled to fit, never stretched — so supply it at roughly 400x144 or larger. The strip is on Deep Navy, so a light or white version of the mark reads best.'),

                    TextInput::make('url')
                        ->label('Website')
                        ->url()
                        ->maxLength(255)
                        ->helperText('Stored for your reference. The logos are not links — the strip scrolls continuously, so a moving link is a poor target.'),
                ]),

            Section::make('Placement')
                ->columns(3)
                ->schema([
                    TextInput::make('position')
                        ->numeric()
                        ->default(0)
                        ->helperText('Lower numbers come first.'),

                    Toggle::make('is_visible')
                        ->label('Show on the site')
                        ->default(true),

                    Toggle::make('is_sample')
                        ->label('Placeholder')
                        ->helperText('On for the logos seeded for review. Turn it off once this is a real client, or delete the row.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->emptyStateHeading('No client logos yet')
            ->emptyStateDescription('Until one is added, the strip shows the dashed "Client logo" placeholders the design was approved with.')
            ->emptyStateIcon(Heroicon::OutlinedBuildingOffice2)
            ->columns([
                ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->disk('public')
                    ->height(40)
                    ->extraImgAttributes(['style' => 'object-fit:contain;background:#0C2944;padding:4px']),

                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('industry')->placeholder('—')->searchable(),

                IconColumn::make('is_sample')
                    ->label('Placeholder')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedExclamationTriangle)
                    ->falseIcon(Heroicon::OutlinedCheckCircle)
                    ->trueColor('info')
                    ->falseColor('gray')
                    ->tooltip(fn (Client $record) => $record->is_sample
                        ? 'Seeded for review — replace before launch'
                        : 'A real client'),

                ToggleColumn::make('is_visible')->label('Visible'),
            ])
            ->filters([
                TernaryFilter::make('is_sample')->label('Placeholder'),
                TernaryFilter::make('is_visible')->label('Visible'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
        ];
    }
}
