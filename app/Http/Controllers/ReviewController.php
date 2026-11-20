<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Mail\ReviewAutoReply;
use App\Mail\ReviewRequested;
use App\Models\Lead;
use App\Models\Setting;
use App\Support\Turnstile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function store(ReviewRequest $request): RedirectResponse|JsonResponse
    {
        $this->assertNotRateLimited($request->ip());

        if (! Turnstile::passes($request->input('cf-turnstile-response'), $request->ip())) {
            throw ValidationException::withMessages([
                'form' => 'We could not verify that submission. Please try again.',
            ]);
        }

        RateLimiter::hit(
            $this->throttleKey($request->ip()),
            config('review.rate_limit.per_minutes') * 60
        );

        $lead = Lead::create([
            ...$request->safe()->only(['name', 'email', 'company', 'city', 'service', 'requirement']),
            // Validation requires the box ticked, so reaching here means consent
            // was given now. Stored as a time, not a flag, for the record.
            'consented_at' => now(),
            'source_page' => $this->sourcePage($request->input('source_page')),
            'utm_source' => $request->input('utm_source'),
            'utm_medium' => $request->input('utm_medium'),
            'utm_campaign' => $request->input('utm_campaign'),
            'utm_term' => $request->input('utm_term'),
            'utm_content' => $request->input('utm_content'),
            'ip_hash' => Lead::hashIp($request->ip()),
            'user_agent' => str($request->userAgent() ?? '')->limit(500)->value(),
        ]);

        $this->dispatchMail($lead);

        $message = config('review.success_message');

        // The JS path swaps the button label in place; without JS we redirect
        // back to the form and render the same message where the form was.
        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => $message]);
        }

        return back()
            ->with('review.success', $message)
            ->withFragment('contact');
    }

    private function assertNotRateLimited(?string $ip): void
    {
        $key = $this->throttleKey($ip);

        if (! RateLimiter::tooManyAttempts($key, config('review.rate_limit.attempts'))) {
            return;
        }

        $minutes = (int) ceil(RateLimiter::availableIn($key) / 60);

        throw ValidationException::withMessages([
            'form' => "You have sent several requests already. Please try again in {$minutes} minutes, or email us directly.",
        ]);
    }

    private function throttleKey(?string $ip): string
    {
        return 'review:'.sha1((string) $ip);
    }

    /** Keep only a path we served, never an attacker-supplied absolute URL. */
    private function sourcePage(?string $value): string
    {
        $path = is_string($value) ? parse_url($value, PHP_URL_PATH) : null;

        return str($path ?: '/')->limit(190)->value();
    }

    /**
     * Both messages are queued. A slow or unreachable mail host must not make
     * the visitor wait, and must never lose a lead that is already stored.
     */
    private function dispatchMail(Lead $lead): void
    {
        $admin = Setting::get('admin_notification_email') ?: config('review.notification_email');

        try {
            if (filled($admin)) {
                Mail::to($admin)->queue(new ReviewRequested($lead));
            } else {
                Log::warning('No lead notification address configured; lead stored but nobody told.', [
                    'lead_id' => $lead->id,
                ]);
            }

            Mail::to($lead->email)->queue(new ReviewAutoReply($lead));

            $lead->forceFill(['notified_at' => now()])->save();
        } catch (\Throwable $e) {
            // The lead is already saved. Queueing failing is an operations
            // problem, not the visitor's, so it is logged rather than shown.
            Log::error('Could not queue enquiry mail.', [
                'lead_id' => $lead->id,
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
