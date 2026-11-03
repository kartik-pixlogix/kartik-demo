<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

/**
 * Placeholder logos for the trust strip, so the marquee can be reviewed
 * carrying real artwork before the client supplies theirs.
 *
 * Every row is `is_sample`, which the admin flags and the Clients list counts.
 * The names come from the placeholder set on unboundsolutions.in, the
 * reference the client pointed at — nothing here is invented, and none of it
 * claims a real engagement. Delete the lot with one action in the admin.
 */
class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            ['national-exports', 'National Exports', 'Seafood export'],
            ['meridian-health', 'Meridian Health', 'Healthcare'],
            ['verde-realty', 'Verde Realty', 'Real estate'],
            ['nimbus-cart', 'Nimbus Cart', 'D2C e-commerce'],
            ['brightpath-academy', 'BrightPath Academy', 'Education'],
            ['skyline-logistics', 'Skyline Logistics', 'Logistics'],
        ];

        foreach ($samples as $i => [$slug, $name, $industry]) {
            Client::query()->updateOrCreate(
                ['name' => $name],
                [
                    'logo_path' => "clients/{$slug}.png",
                    'industry' => $industry,
                    'is_sample' => true,
                    'is_visible' => true,
                    'position' => $i + 1,
                ],
            );
        }
    }
}
