<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Site settings: contact details, the statistics and the credential statuses.
 *
 * A single form over the key/value table rather than a resource, because these
 * are a fixed set of fields the client edits, not records they create. Fields
 * are built from the rows themselves and grouped by their `group` column, so
 * adding a setting to the seeder is enough to surface it here.
 */
class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Site settings';

    protected static ?string $title = 'Site settings';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.manage-settings';

    /** @var array<string, mixed> */
    public array $data = [];

    private const GROUPS = [
        'contact' => ['Contact details', 'Shown in the closing strip and the footer. An email or phone number still in square brackets renders as plain text rather than a mailto: or tel: link.'],
        'credentials' => ['Credentials', 'The trust strip under the hero. These are claims about certification, so leave them in square brackets until each one is confirmed.'],
        'stats' => ['Statistics', 'The figures in the hero and case-file rows. Type them exactly as they should appear, including any + or % sign.'],
        'soc' => ['Managed SOC', 'The facts list in the inverted SOC block.'],
    ];

    public function mount(): void
    {
        $this->form->fill(
            Setting::query()->pluck('value', 'key')
                ->mapWithKeys(fn ($value, $key) => [$key => $value])
                ->all()
        );
    }

    public function form(Schema $schema): Schema
    {
        $settings = Setting::query()->orderBy('group')->orderBy('key')->get()->groupBy('group');

        $sections = [];

        foreach (self::GROUPS as $group => [$heading, $description]) {
            if (! $settings->has($group)) {
                continue;
            }

            $sections[] = Section::make($heading)
                ->description($description)
                ->columns(2)
                ->schema(
                    $settings[$group]->map(fn (Setting $setting) => TextInput::make($setting->key)
                        ->label($setting->label)
                        ->helperText($setting->hint)
                        ->maxLength(500)
                        // A placeholder left in brackets is deliberate, so it is
                        // flagged rather than treated as an error.
                        ->hintIcon(
                            is_placeholder($setting->value) ? Heroicon::OutlinedExclamationTriangle : null,
                            tooltip: 'Still a placeholder — the client needs to confirm this.'
                        )
                    )->all()
                );
        }

        // Anything seeded into a group not listed above still gets an editor.
        foreach ($settings->keys()->diff(array_keys(self::GROUPS)) as $group) {
            $sections[] = Section::make(ucfirst((string) $group))
                ->columns(2)
                ->schema(
                    $settings[$group]->map(fn (Setting $setting) => TextInput::make($setting->key)
                        ->label($setting->label)
                        ->helperText($setting->hint)
                        ->maxLength(500)
                    )->all()
                );
        }

        return $schema->components($sections)->statePath('data');
    }

    /**
     * Filament v5 renders a page through its content schema, so the form and
     * its footer actions are assembled here rather than in the Blade view.
     */
    public function content(Schema $schema): Schema
    {
        return $schema->components([$this->getFormContentComponent()]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make($this->getFormActions())->key('form-actions'),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save settings')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        foreach ($this->form->getState() as $key => $value) {
            Setting::query()->where('key', $key)->update(['value' => $value]);
        }

        // Setting::saved() clears the cache, but update() on the query builder
        // bypasses model events, so the cache is cleared explicitly here.
        \Illuminate\Support\Facades\Cache::forget(Setting::CACHE_KEY);

        Notification::make()
            ->success()
            ->title('Settings saved')
            ->body('The homepage will show the new values immediately.')
            ->send();
    }
}
