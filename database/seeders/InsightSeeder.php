<?php

namespace Database\Seeders;

use App\Models\Insight;
use Illuminate\Database\Seeder;

/**
 * The three field notes exactly as the prototype prints them.
 *
 * `date_label` holds the literal "[DD MMM 2026]" the design shows; `published_at`
 * is the sortable timestamp behind it. The client replaces the labels with real
 * dates — at which point date_label can be derived from published_at instead.
 */
class InsightSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'slug' => 'dpdpa-2023-consent-flows',
                'title' => 'DPDPA 2023: what changes in your consent flows once the Rules apply',
                'category' => 'Advisory',
                'date_label' => '[DD MMM 2026]',
                'cover_art_key' => 'note-consent',
                'art_inverted' => false,
                'position' => 1,
            ],
            [
                'slug' => 'sebi-cscrf-control-by-control',
                'title' => 'SEBI CSCRF, control by control: a checklist for regulated entities',
                'category' => 'Guide',
                'date_label' => '[DD MMM 2026]',
                'cover_art_key' => 'note-controls',
                'art_inverted' => true,
                'position' => 2,
            ],
            [
                'slug' => 'five-cloud-misconfigurations-aws',
                'title' => 'Five cloud misconfigurations we find in almost every AWS review',
                'category' => 'Field note',
                'date_label' => '[DD MMM 2026]',
                'cover_art_key' => 'note-cloud',
                'art_inverted' => false,
                'position' => 3,
            ],
        ];

        foreach ($rows as $row) {
            Insight::query()->firstOrCreate(
                ['slug' => $row['slug']],
                $row + ['published_at' => now()]
            );
        }
    }
}
