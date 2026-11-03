<?php

namespace App\Filament\Resources\Insights;

use App\Filament\Resources\Insights\Pages\CreateInsight;
use App\Filament\Resources\Insights\Pages\EditInsight;
use App\Filament\Resources\Insights\Pages\ListInsights;
use App\Models\Insight;
use App\Support\CoverArt;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class InsightResource extends Resource
{
    protected static ?string $model = Insight::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Insights';

    protected static ?string $modelLabel = 'field note';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Article')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $operation, $state, callable $set) {
                            // Only auto-slug while drafting. Changing the slug of a
                            // published article would break any link to it.
                            if ($operation === 'create') {
                                $set('slug', Str::slug($state));
                            }
                        }),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->helperText('Used in the URL. Changing it on a published article breaks existing links.'),

                    TextInput::make('category')
                        ->required()
                        ->maxLength(60)
                        ->datalist(['Advisory', 'Guide', 'Field note'])
                        ->helperText('Shown before the date on the card, e.g. "Advisory / 12 Mar 2026".'),

                    Textarea::make('excerpt')
                        ->rows(3)
                        ->maxLength(500)
                        ->columnSpanFull(),

                    RichEditor::make('body')
                        ->columnSpanFull()
                        ->toolbarButtons(['bold', 'italic', 'link', 'h2', 'h3', 'bulletList', 'orderedList', 'blockquote', 'undo', 'redo']),
                ]),

            Section::make('Card')
                ->columns(2)
                ->schema([
                    Select::make('cover_art_key')
                        ->label('Cover artwork')
                        ->options(CoverArt::forInsights())
                        ->searchable()
                        ->helperText('The hand-drawn SVG that illustrates the card. Used unless a cover image is uploaded below.'),

                    FileUpload::make('cover_image')
                        ->label('Cover image')
                        ->image()
                        ->disk('public')
                        ->directory('covers')
                        ->visibility('public')
                        ->imageEditor()
                        ->imageEditorAspectRatios(['5:4'])
                        ->maxSize(3072)
                        ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                        ->columnSpanFull()
                        ->helperText('Optional. Overrides the artwork above for this field note. The frame is 5:4 and the image is cropped to fill it, so upload it at 1000x800 or larger. Clear this field to go back to the hand-drawn artwork.'),

                    Toggle::make('art_inverted')
                        ->label('Light card frame')
                        ->helperText('The alternating light frame (.art.k) used by every other card in the row.'),

                    TextInput::make('date_label')
                        ->label('Date label')
                        ->maxLength(60)
                        ->helperText('Printed on the card exactly as typed. Currently the placeholder "[DD MMM 2026]".'),

                    TextInput::make('position')
                        ->numeric()
                        ->default(0)
                        ->helperText('Lower numbers come first on the homepage.'),
                ]),

            Section::make('Publishing')
                ->columns(2)
                ->schema([
                    DateTimePicker::make('published_at')
                        ->label('Published at')
                        ->seconds(false)
                        ->helperText('Empty, or in the future, keeps it off the site.'),
                ]),

            Section::make('SEO')
                ->columns(1)
                ->collapsed()
                ->schema([
                    TextInput::make('seo_title')
                        ->maxLength(255)
                        ->helperText('Falls back to the article title.'),
                    Textarea::make('seo_description')
                        ->rows(2)
                        ->maxLength(500)
                        ->helperText('Falls back to the excerpt. Around 155 characters shows in full.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                TextColumn::make('position')->label('#')->sortable()->width(1),
                TextColumn::make('title')->searchable()->sortable()->wrap()->limit(70),
                TextColumn::make('category')->badge()->searchable(),
                TextColumn::make('date_label')->label('Date label')->placeholder('—'),
                IconColumn::make('published')
                    ->label('Live')
                    ->boolean()
                    ->state(fn (Insight $r) => $r->published_at !== null && $r->published_at->isPast()),
                TextColumn::make('cover_art_key')->label('Artwork')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInsights::route('/'),
            'create' => CreateInsight::route('/create'),
            'edit' => EditInsight::route('/{record}/edit'),
        ];
    }
}
