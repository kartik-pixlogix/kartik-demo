<?php

namespace Database\Seeders;

use App\Models\CaseStudy;
use Illuminate\Database\Seeder;

/**
 * The two case files exactly as the prototype prints them. Names, figures and
 * outcomes are placeholders until the client confirms them — the section's own
 * standfirst says so on the page.
 */
class CaseStudySeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'slug' => 'bfsi-rbi-cyber-framework',
                'title' => '[Case study: BFSI client] made inspection-ready for the RBI cyber framework',
                'tags' => 'BFSI / GRC / VAPT / MSS',
                'summary' => 'A [mid-size lender] needed evidence for every control before a fixed inspection date. Outcome: [to confirm].',
                'cover_art_key' => 'case-rbi-csf',
                'art_inverted' => true,
                'position' => 1,
            ],
            [
                'slug' => 'saas-soc-2-readiness',
                'title' => '[Case study: SaaS client] through SOC 2 readiness for enterprise deals',
                'tags' => 'SaaS / GRC / Cloud',
                'summary' => 'A [B2B SaaS company] was losing deals to security questionnaires it could not answer with evidence. Outcome: [to confirm].',
                'cover_art_key' => 'case-soc2',
                'art_inverted' => false,
                'position' => 2,
            ],
        ];

        foreach ($rows as $row) {
            CaseStudy::query()->firstOrCreate(['slug' => $row['slug']], $row);
        }
    }
}
