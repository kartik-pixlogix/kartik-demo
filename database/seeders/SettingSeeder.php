<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Reproduces the prototype's placeholder text EXACTLY.
 *
 * Nothing here is real data. Every value is what design/v7/index.html prints
 * today, so that moving this content into the database does not change a single
 * rendered pixel — `npm run visual:diff` is the proof. The client replaces these
 * through the Filament admin; see docs/inventory.md §15 for the full list and
 * the two facts that still need confirming.
 */
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        foreach (self::rows() as $row) {
            // updateOrCreate on the key, but never clobber a value the client has
            // already edited — re-running the seeder must be safe in production.
            Setting::query()->firstOrCreate(
                ['key' => $row['key']],
                collect($row)->except('key')->all()
            );
        }
    }

    /** @return list<array{key:string,value:?string,group:string,label:string,hint:?string}> */
    public static function rows(): array
    {
        return [
            // ---- contact -------------------------------------------------
            ['key' => 'contact_email', 'value' => '[email address]', 'group' => 'contact', 'label' => 'Email address', 'hint' => 'Shown in the closing strip and the footer.'],
            ['key' => 'contact_phone', 'value' => '[phone number]', 'group' => 'contact', 'label' => 'Phone number', 'hint' => 'Shown in the closing strip and the footer.'],
            ['key' => 'contact_address', 'value' => '[Street address]', 'group' => 'contact', 'label' => 'Street address', 'hint' => 'The city line "Ahmedabad, Gujarat, India" is fixed below this.'],
            ['key' => 'contact_response', 'value' => 'Within one business day', 'group' => 'contact', 'label' => 'Response time', 'hint' => null],
            ['key' => 'admin_notification_email', 'value' => null, 'group' => 'contact', 'label' => 'Lead notification inbox', 'hint' => 'Where new posture-review requests are sent. Falls back to MAIL_ADMIN_ADDRESS.'],

            /*
             * ---- social ---------------------------------------------------
             *
             * Read off unboundsolutions.in, the sister site the client pointed
             * at, rather than invented. Only the two profiles that site
             * actually links are seeded; the rest are blank so the footer
             * simply omits them until there is somewhere real to point.
             */
            ['key' => 'social_linkedin', 'value' => 'https://in.linkedin.com/company/unboundsolutions4', 'group' => 'social', 'label' => 'LinkedIn', 'hint' => 'Full URL. Leave empty to drop the link from the footer.'],
            ['key' => 'social_instagram', 'value' => 'https://www.instagram.com/unboundsolutions.in/', 'group' => 'social', 'label' => 'Instagram', 'hint' => 'Full URL. Leave empty to drop the link from the footer.'],
            ['key' => 'social_x', 'value' => null, 'group' => 'social', 'label' => 'X / Twitter', 'hint' => 'Not published on unboundsolutions.in — add one if it exists.'],
            ['key' => 'social_facebook', 'value' => null, 'group' => 'social', 'label' => 'Facebook', 'hint' => 'Not published on unboundsolutions.in — add one if it exists.'],
            ['key' => 'social_youtube', 'value' => null, 'group' => 'social', 'label' => 'YouTube', 'hint' => 'Not published on unboundsolutions.in — add one if it exists.'],

            // ---- credentials ---------------------------------------------
            ['key' => 'cred_iso_27001', 'value' => '[status]', 'group' => 'credentials', 'label' => 'ISO/IEC 27001 status', 'hint' => 'Client must confirm the actual status.'],
            ['key' => 'cred_soc_2', 'value' => '[status]', 'group' => 'credentials', 'label' => 'SOC 2 status', 'hint' => 'Client must confirm the actual status.'],
            ['key' => 'cred_cert_in', 'value' => '[if applicable]', 'group' => 'credentials', 'label' => 'CERT-In empanelment status', 'hint' => 'Client must confirm the actual status.'],

            // ---- hero statistics -----------------------------------------
            ['key' => 'stat_assessments', 'value' => '[XX]+', 'group' => 'stats', 'label' => 'Assessments delivered', 'hint' => null],
            ['key' => 'stat_critical_closed', 'value' => '[XX]%', 'group' => 'stats', 'label' => 'Critical findings closed in 30 days', 'hint' => null],

            // ---- case-file statistics ------------------------------------
            ['key' => 'stat_audits_supported', 'value' => '[XX]', 'group' => 'stats', 'label' => 'Audits supported to certification', 'hint' => null],
            ['key' => 'stat_mean_time_to_respond', 'value' => '[XX] min', 'group' => 'stats', 'label' => 'Mean time to respond', 'hint' => 'Also shown in the Managed SOC list.'],
            ['key' => 'stat_endpoints_monitored', 'value' => '[XX]+', 'group' => 'stats', 'label' => 'Endpoints monitored', 'hint' => null],

            // ---- Managed SOC ---------------------------------------------
            ['key' => 'soc_cert_in_window', 'value' => 'Inside the six-hour window', 'group' => 'soc', 'label' => 'CERT-In reporting window', 'hint' => 'Client must confirm the six-hour incident-reporting window.'],
        ];
    }
}
