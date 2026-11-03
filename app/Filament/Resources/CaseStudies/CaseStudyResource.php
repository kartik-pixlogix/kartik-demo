<?php

namespace App\Filament\Resources\CaseStudies;

use App\Filament\Resources\CaseStudies\Pages\CreateCaseStudy;
use App\Filament\Resources\CaseStudies\Pages\EditCaseStudy;
use App\Filament\Resources\CaseStudies\Pages\ListCaseStudies;
use App\Models\CaseStudy;
use App\Support\CoverArt;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
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

class CaseStudyResource extends Resource
{
    protected static ?string $model = CaseStudy::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Case studies';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Case file')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $operation, $state, callable $set) {
                            if ($operation === 'create') {
                                $set('slug', Str::slug($state));
                            }
                        }),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    TextInput::make('tags')
                        ->maxLength(255)
                        ->helperText('The kicker under the title, e.g. "BFSI / GRC / VAPT / MSS".'),

                    Textarea::make('summary')
                        ->rows(3)
                        ->maxLength(600)
                        ->columnSpanFull()
                        ->helperText('The paragraph shown on the homepage card.'),

                    RichEditor::make('body')
                        ->columnSpanFull()
                        ->toolbarButtons(['bold', 'italic', 'link', 'h2', 'h3', 'bulletList', 'orderedList', 'blockquote', 'undo', 'redo']),
                ]),

            Section::make('Card')
                ->columns(2)
                ->schema([
                    Select::make('cover_art_key')
                        ->label('Cover artwork')
                        ->options(CoverArt::forCaseStudies())
                        ->searchable(),

                    Toggle::make('art_inverted')
                        ->label('Light card frame')
                        ->helperText('The alternating light frame (.art.k).'),

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
                        ->helperText('Optional. Overrides the artwork above for this case study. The frame is 5:4 and the image is cropped to fill it, so upload it at 1000x800 or larger. Clear this field to go back to the hand-drawn artwork.'),

                    TextInput::make('position')
                        ->numeric()
                        ->default(0)
                        ->helperText('Lower numbers come first on the homepage.'),

                    Toggle::make('is_published')
                        ->label('Published')
                        ->default(true),
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
                TextColumn::make('tags')->placeholder('—')->searchable(),
                IconColumn::make('is_published')->label('Live')->boolean(),
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
            'index' => ListCaseStudies::route('/'),
            'create' => CreateCaseStudy::route('/create'),
            'edit' => EditCaseStudy::route('/{record}/edit'),
        ];
    }
}
