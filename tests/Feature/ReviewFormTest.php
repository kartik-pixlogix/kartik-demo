<?php

namespace Tests\Feature;

use App\Http\Requests\ReviewRequest;
use App\Mail\ReviewAutoReply;
use App\Mail\ReviewRequested;
use App\Models\Lead;
use App\Models\Setting;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ReviewFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SettingSeeder::class);
        Mail::fake();
        RateLimiter::clear($this->throttleKey());
    }

    private function throttleKey(string $ip = '127.0.0.1'): string
    {
        return 'review:'.sha1($ip);
    }

    /** A submission from someone who filled the form like a person would. */
    private function payload(array $overrides = [], int $openedSecondsAgo = 30): array
    {
        return array_merge([
            'name' => 'Priya Shah',
            'email' => 'priya@example.com',
            'company' => 'Example Finance',
            'service' => 'Governance, Risk & Compliance',
            ReviewRequest::HONEYPOT => '',
            ReviewRequest::TIMESTAMP => Crypt::encryptString((string) (time() - $openedSecondsAgo)),
        ], $overrides);
    }

    // ---- the happy path ----------------------------------------------------

    public function test_it_stores_a_lead_and_redirects_with_the_success_message(): void
    {
        $response = $this->from('/')->post(route('review.store'), $this->payload());

        $response->assertRedirect();
        $response->assertSessionHas('review.success', config('review.success_message'));

        $lead = Lead::sole();
        $this->assertSame('Priya Shah', $lead->name);
        $this->assertSame('priya@example.com', $lead->email);
        $this->assertSame('Example Finance', $lead->company);
        $this->assertSame('Governance, Risk & Compliance', $lead->service);
    }

    public function test_it_queues_the_admin_notification_and_the_visitor_auto_reply(): void
    {
        Setting::query()->updateOrCreate(
            ['key' => 'admin_notification_email'],
            ['value' => 'desk@ubshield.test', 'group' => 'contact', 'label' => 'Lead notification inbox']
        );

        $this->post(route('review.store'), $this->payload());

        Mail::assertQueued(ReviewRequested::class, fn ($mail) => $mail->hasTo('desk@ubshield.test'));
        Mail::assertQueued(ReviewAutoReply::class, fn ($mail) => $mail->hasTo('priya@example.com'));

        $this->assertNotNull(Lead::sole()->notified_at);
    }

    public function test_the_admin_notification_replies_to_the_visitor(): void
    {
        $this->post(route('review.store'), $this->payload());

        Mail::assertQueued(ReviewRequested::class, function ($mail) {
            return $mail->hasReplyTo('priya@example.com')
                && str_contains($mail->envelope()->subject, 'Example Finance');
        });
    }

    public function test_it_answers_json_for_the_scripted_path(): void
    {
        $response = $this->postJson(route('review.store'), $this->payload());

        $response->assertOk()->assertJson([
            'ok' => true,
            'message' => config('review.success_message'),
        ]);
    }

    public function test_the_success_message_replaces_the_form_without_javascript(): void
    {
        $html = $this->followingRedirects()
            ->from('/')
            ->post(route('review.store'), $this->payload())
            ->getContent();

        $this->assertStringContainsString(config('review.success_message'), $html);
        $this->assertStringNotContainsString('data-review-form', $html);
    }

    // ---- attribution and privacy -------------------------------------------

    public function test_it_records_the_source_page_and_campaign(): void
    {
        $this->post(route('review.store'), $this->payload([
            'source_page' => '/',
            'utm_source' => 'linkedin',
            'utm_medium' => 'social',
            'utm_campaign' => 'dpdpa',
        ]));

        $lead = Lead::sole();
        $this->assertSame('/', $lead->source_page);
        $this->assertSame('linkedin', $lead->utm_source);
        $this->assertSame('social', $lead->utm_medium);
        $this->assertSame('dpdpa', $lead->utm_campaign);
    }

    public function test_it_keeps_only_the_path_from_a_supplied_source_page(): void
    {
        $this->post(route('review.store'), $this->payload([
            'source_page' => 'https://evil.example.com/phish?a=1',
        ]));

        $this->assertSame('/phish', Lead::sole()->source_page);
    }

    public function test_it_hashes_the_ip_address_rather_than_storing_it(): void
    {
        $this->post(route('review.store'), $this->payload());

        $lead = Lead::sole();
        $this->assertNotNull($lead->ip_hash);
        $this->assertSame(64, strlen($lead->ip_hash));
        $this->assertStringNotContainsString('127.0.0.1', $lead->ip_hash);
        $this->assertSame(Lead::hashIp('127.0.0.1'), $lead->ip_hash);
    }

    // ---- validation --------------------------------------------------------

    public function test_it_rejects_a_submission_with_missing_fields(): void
    {
        $this->from('/')
            ->post(route('review.store'), $this->payload([
                'name' => '', 'email' => '', 'company' => '',
            ]))
            ->assertSessionHasErrors(['name', 'email', 'company']);

        $this->assertSame(0, Lead::count());
    }

    public function test_it_rejects_an_invalid_email(): void
    {
        $this->from('/')
            ->post(route('review.store'), $this->payload(['email' => 'not-an-email']))
            ->assertSessionHasErrors('email');

        $this->assertSame(0, Lead::count());
    }

    public function test_it_rejects_a_service_that_is_not_on_the_list(): void
    {
        $this->from('/')
            ->post(route('review.store'), $this->payload(['service' => 'Free Pizza']))
            ->assertSessionHasErrors('service');

        $this->assertSame(0, Lead::count());
    }

    public function test_it_accepts_every_service_option_the_form_offers(): void
    {
        foreach (ReviewRequest::SERVICES as $i => $service) {
            RateLimiter::clear($this->throttleKey());
            $this->post(route('review.store'), $this->payload([
                'email' => "person{$i}@example.com",
                'service' => $service,
            ]))->assertSessionHasNoErrors();
        }

        $this->assertSame(count(ReviewRequest::SERVICES), Lead::count());
    }

    // ---- spam defences -----------------------------------------------------

    public function test_it_silently_rejects_a_filled_honeypot(): void
    {
        $this->from('/')
            ->post(route('review.store'), $this->payload([ReviewRequest::HONEYPOT => 'http://spam.example']))
            ->assertSessionHasErrors('form');

        $this->assertSame(0, Lead::count());
        Mail::assertNothingQueued();
    }

    public function test_it_rejects_a_form_submitted_faster_than_a_human_could(): void
    {
        config(['review.minimum_fill_seconds' => 3]);

        $this->from('/')
            ->post(route('review.store'), $this->payload(openedSecondsAgo: 0))
            ->assertSessionHasErrors('form');

        $this->assertSame(0, Lead::count());
    }

    public function test_it_rejects_a_missing_or_forged_timestamp(): void
    {
        config(['review.minimum_fill_seconds' => 3]);

        foreach (['', 'not-encrypted'] as $value) {
            $this->from('/')
                ->post(route('review.store'), $this->payload([ReviewRequest::TIMESTAMP => $value]))
                ->assertSessionHasErrors('form');
        }

        $this->assertSame(0, Lead::count());
    }

    public function test_it_rejects_a_stale_replayed_form(): void
    {
        config(['review.maximum_form_age_seconds' => 3600]);

        $this->from('/')
            ->post(route('review.store'), $this->payload(openedSecondsAgo: 7200))
            ->assertSessionHasErrors('form');

        $this->assertSame(0, Lead::count());
    }

    public function test_the_rejection_message_does_not_say_which_check_caught_it(): void
    {
        // Asserted on the rendered page, because what matters is what a bot
        // author can read back. Naming the check that caught them is free
        // tuning information.
        $html = $this->followingRedirects()
            ->from('/')
            ->post(route('review.store'), $this->payload([ReviewRequest::HONEYPOT => 'spam']))
            ->getContent();

        $this->assertStringContainsString('We could not accept that submission', $html);

        // Whole words only — "bot" would otherwise match the "bottom" in an
        // inline style on the hero artwork.
        foreach (['honeypot', 'too fast', 'spam', 'bot', 'robot', 'timestamp', 'automated'] as $tell) {
            $this->assertDoesNotMatchRegularExpression(
                '/\b'.preg_quote($tell, '/').'\b/i',
                $html,
                "the rejection should not mention '{$tell}'"
            );
        }
    }

    public function test_it_rate_limits_after_five_submissions_an_hour(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('review.store'), $this->payload(['email' => "p{$i}@example.com"]))
                ->assertSessionHasNoErrors();
        }

        $this->from('/')
            ->post(route('review.store'), $this->payload(['email' => 'sixth@example.com']))
            ->assertSessionHasErrors('form');

        $this->assertSame(5, Lead::count());
    }

    // ---- Turnstile ---------------------------------------------------------

    public function test_turnstile_is_skipped_when_disabled(): void
    {
        config(['review.turnstile.enabled' => false]);

        $this->post(route('review.store'), $this->payload())->assertSessionHasNoErrors();
        $this->assertSame(1, Lead::count());
    }

    public function test_turnstile_rejects_a_missing_token_when_enabled(): void
    {
        config([
            'review.turnstile.enabled' => true,
            'review.turnstile.secret_key' => 'secret',
        ]);

        $this->from('/')
            ->post(route('review.store'), $this->payload())
            ->assertSessionHasErrors('form');

        $this->assertSame(0, Lead::count());
    }
}
