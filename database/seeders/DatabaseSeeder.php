<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            CaseStudySeeder::class,
            InsightSeeder::class,
        ]);

        /*
         * Review scaffolding, kept separate from the content above because
         * none of it is real: placeholder client logos, and sample cover
         * images for the cards. Both are flagged in the admin and listed in
         * docs/placeholders.md. Drop these two lines for a clean install.
         */
        $this->call([
            ClientSeeder::class,
            CoverImageSeeder::class,
        ]);
    }
}
