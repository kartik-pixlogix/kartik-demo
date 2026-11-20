<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSettings;
use App\Filament\Resources\CaseStudies\Pages\ListCaseStudies;
use App\Filament\Resources\Insights\Pages\ListInsights;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Models\CaseStudy;
use App\Models\Insight;
use App\Models\Lead;
use App\Models\Setting;
use App\Models\User;
use App\Support\LeadCsv;
use Database\Seeders\CaseStudySeeder;
use Database\Seeders\InsightSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SettingSeeder::class, CaseStudySeeder::class, InsightSeeder::class]);

        // Since Phase 4 the panel requires two-factor authentication, so a user
        // without it is redirected to setup and cannot reach any page. Most of
        // these tests are about the resources, not the login flow, so the
        // fixture user has 2FA already configured. The enforcement itself is
        // asserted separately below.
        $this->admin = User::factory()->create([
            'app_authentication_secret' => 'JBSWY3DPEHPK3PXP',
        ]);
    }

    private function lead(array $overrides = []): Lead
    {
        return Lead::create(array_merge([
            'name' => 'Priya Shah',
            'email' => 'priya@example.com',
            'company' => 'Example Finance',
            'service' => 'Governance, Risk & Compliance',
            'source_page' => '/',
            'ip_hash' => Lead::hashIp('127.0.0.1'),
        ], $overrides));
    }

    // ---- access ------------------------------------------------------------

    public function test_guests_cannot_reach_the_admin(): void
    {
        foreach (['/admin', '/admin/leads', '/admin/insights', '/admin/case-studies'] as $url) {
            $this->get($url)->assertRedirect('/admin/login');
        }
    }

    public function test_an_authenticated_user_can_open_every_admin_page(): void
    {
        $this->actingAs($this->admin);

        foreach (['/admin/leads', '/admin/insights', '/admin/case-studies', '/admin/manage-settings'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_two_factor_authentication_cannot_be_skipped(): void
    {
        // A user who has not set up an authenticator reaches no part of the
        // panel — this admin holds every lead the site has captured.
        $withoutTwoFactor = User::factory()->create(['app_authentication_secret' => null]);

        $this->actingAs($withoutTwoFactor);

        foreach (['/admin', '/admin/leads', '/admin/manage-settings'] as $url) {
            $this->get($url)->assertRedirect();
        }

        $this->assertStringContainsString(
            'multi-factor-authentication',
            $this->get('/admin/leads')->headers->get('Location')
        );
    }

    public function test_the_admin_does_not_send_staff_names_to_a_third_party(): void
    {
        // Filament's default avatar provider is ui-avatars.com, which puts the
        // signed-in user's name in a query string to another origin.
        $avatar = (new \App\Support\LocalAvatarProvider)->get($this->admin);

        $this->assertStringStartsWith('data:image/svg+xml;base64,', $avatar);
        $this->assertStringNotContainsString('ui-avatars', $avatar);

        $html = $this->actingAs($this->admin)->get('/admin/leads')->getContent();
        $this->assertStringNotContainsString('ui-avatars.com', $html);
    }

    // ---- leads are read-only ----------------------------------------------

    public function test_leads_have_no_create_or_edit_route(): void
    {
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('filament.admin.resources.leads.create'));
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('filament.admin.resources.leads.edit'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('filament.admin.resources.leads.index'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('filament.admin.resources.leads.view'));
    }

    public function test_the_lead_list_shows_submissions(): void
    {
        $lead = $this->lead();

        $this->actingAs($this->admin);

        Livewire::test(ListLeads::class)
            ->assertCanSeeTableRecords([$lead])
            ->assertCanRenderTableColumn('company')
            ->assertCanRenderTableColumn('email');
    }

    public function test_a_lead_can_be_viewed_in_detail(): void
    {
        $lead = $this->lead();

        $this->actingAs($this->admin)
            ->get("/admin/leads/{$lead->id}")
            ->assertOk()
            ->assertSee('Example Finance')
            ->assertSee('priya@example.com');
    }

    // ---- CSV export --------------------------------------------------------

    public function test_the_csv_export_contains_the_lead_data(): void
    {
        $lead = $this->lead(['utm_source' => 'linkedin']);

        $csv = $this->renderCsv(collect([$lead]));

        $this->assertStringContainsString('Business email', $csv);
        $this->assertStringContainsString('Brief requirement', $csv);
        $this->assertStringContainsString('Priya Shah', $csv);
        $this->assertStringContainsString('priya@example.com', $csv);
        $this->assertStringContainsString('linkedin', $csv);
    }

    public function test_the_csv_export_never_leaks_the_ip_hash_or_user_agent(): void
    {
        $lead = $this->lead(['user_agent' => 'Mozilla/5.0 secret-agent']);

        $csv = $this->renderCsv(collect([$lead]));

        $this->assertStringNotContainsString($lead->ip_hash, $csv);
        $this->assertStringNotContainsString('secret-agent', $csv);
    }

    public function test_the_csv_export_neutralises_spreadsheet_formulas(): void
    {
        // An attacker-controlled name that Excel would otherwise execute.
        $lead = $this->lead(['name' => '=cmd|/c calc!A1', 'email' => 'x@example.com']);

        $csv = $this->renderCsv(collect([$lead]));

        $this->assertStringNotContainsString(',=cmd', $csv);
        $this->assertStringContainsString("\t=cmd", $csv);
    }

    public function test_the_csv_export_starts_with_a_utf8_bom(): void
    {
        $csv = $this->renderCsv(collect([$this->lead()]));

        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);
    }

    private function renderCsv(\Illuminate\Support\Collection $records): string
    {
        $response = LeadCsv::download($records, 'test.csv');

        ob_start();
        $response->sendContent();

        return (string) ob_get_clean();
    }

    // ---- content resources -------------------------------------------------

    public function test_the_insight_list_shows_the_seeded_field_notes(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ListInsights::class)
            ->assertCanSeeTableRecords(Insight::all());
    }

    public function test_the_case_study_list_shows_the_seeded_case_files(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ListCaseStudies::class)
            ->assertCanSeeTableRecords(CaseStudy::all());
    }

    // ---- settings ----------------------------------------------------------

    public function test_settings_can_be_edited_and_reach_the_homepage(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ManageSettings::class)
            ->fillForm([
                'contact_email' => 'hello@ubshield.in',
                'contact_phone' => '+91 79 1234 5678',
                'stat_assessments' => '450+',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('hello@ubshield.in', Setting::get('contact_email'));

        // The stats row is hidden for launch (brief §8), so a stat setting is
        // stored but not shown.
        $this->assertSame('450+', Setting::get('stat_assessments'));

        $html = $this->get('/')->getContent();
        $this->assertStringNotContainsString('450+', $html);
        $this->assertStringContainsString('hello@ubshield.in', $html);

        // a real value becomes a real link; a placeholder does not
        $this->assertStringContainsString('mailto:hello@ubshield.in', $html);
        $this->assertStringContainsString('tel:+917912345678', $html);
    }

    public function test_a_placeholder_contact_detail_is_left_out(): void
    {
        $html = $this->get('/')->getContent();

        // Launch brief §6: only confirmed details are shown, so a placeholder
        // is omitted entirely rather than printed or linked.
        $this->assertStringNotContainsString('[email address]', $html);
        $this->assertStringNotContainsString('mailto:[email', $html);
        $this->assertStringNotContainsString('tel:', $html);
    }
}
