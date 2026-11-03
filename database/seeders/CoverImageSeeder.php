<?php

namespace Database\Seeders;

use App\Models\CaseStudy;
use App\Models\Insight;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Attaches the sample covers from scripts/build-sample-covers.mjs.
 *
 * Review scaffolding, so the cards can be seen carrying images instead of the
 * hand-drawn SVGs. Skips any record whose image file is missing and never
 * overwrites a cover somebody has already set, so re-running is safe and so
 * is running it before the images have been generated.
 *
 * Clearing the Cover image field in the admin restores the SVG.
 */
class CoverImageSeeder extends Seeder
{
    /** slug => file under storage/app/public/covers */
    private const INSIGHTS = [
        'dpdpa-2023-consent-flows' => 'dpdpa-consent.png',
        'sebi-cscrf-control-by-control' => 'sebi-cscrf.png',
        'five-cloud-misconfigurations-aws' => 'cloud-misconfig.png',
    ];

    /** position => file; case studies have no slug to key on */
    private const CASE_STUDIES = [
        'case-exchange.png',
        'case-health.png',
    ];

    public function run(): void
    {
        $disk = Storage::disk('public');

        foreach (self::INSIGHTS as $slug => $file) {
            if (! $disk->exists("covers/{$file}")) {
                $this->command?->warn("  covers/{$file} missing — run scripts/build-sample-covers.mjs");

                continue;
            }

            Insight::query()
                ->where('slug', $slug)
                ->whereNull('cover_image')
                ->update(['cover_image' => "covers/{$file}"]);
        }

        CaseStudy::query()
            ->orderBy('position')
            ->get()
            ->take(count(self::CASE_STUDIES))
            ->each(function (CaseStudy $case, int $i) use ($disk) {
                $file = self::CASE_STUDIES[$i];

                if ($case->cover_image !== null || ! $disk->exists("covers/{$file}")) {
                    return;
                }

                $case->update(['cover_image' => "covers/{$file}"]);
            });
    }
}
