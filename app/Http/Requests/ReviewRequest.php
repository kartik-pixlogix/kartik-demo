<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ReviewRequest extends FormRequest
{
    /** The service-interest options, in the prototype's order. */
    public const SERVICES = [
        'Offensive Security / VAPT',
        'Data Privacy',
        'Governance, Risk & Compliance',
        'Infrastructure & Cloud',
        'Managed Security Services',
        'Not sure yet',
    ];

    /** Field a human never sees and never fills. */
    public const HONEYPOT = 'website';

    /** Encrypted render timestamp, used for the minimum fill time. */
    public const TIMESTAMP = 'form_opened';

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            // The DNS check catches typo'd domains, which is worth a lot for lead
            // quality — but it is a live network lookup, so it is configurable and
            // off in the test suite rather than making validation depend on DNS.
            'email' => ['required', 'string', config('review.validate_email_dns') ? 'email:rfc,dns' : 'email:rfc', 'max:190'],
            'company' => ['required', 'string', 'min:2', 'max:120'],
            // Rule::in, not the string form: two of the options contain a comma,
            // which the string parser would split into separate values.
            'service' => ['required', 'string', Rule::in(self::SERVICES)],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'Please tell us your name',
            'name.min' => 'Please tell us your name',
            'email.required' => 'Please give us a work email',
            'email.email' => 'That does not look like a working email address',
            'company.required' => 'Please tell us your company',
            'company.min' => 'Please tell us your company',
            'service.required' => 'Please choose a service',
            'service.in' => 'Please choose a service from the list',
        ];
    }

    /**
     * Spam checks run after the field rules so a real person who mistypes an
     * email still gets a useful message rather than a generic rejection.
     *
     * Both failures are reported as the same vague form-level error. Telling a
     * bot which check caught it is free tuning information.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (filled($this->input(self::HONEYPOT))) {
                $validator->errors()->add('form', $this->rejectionMessage());

                return;
            }

            if (! $this->waitedLongEnough()) {
                $validator->errors()->add('form', $this->rejectionMessage());
            }
        });
    }

    private function rejectionMessage(): string
    {
        return 'We could not accept that submission. Please try again, or email us directly.';
    }

    /**
     * A human cannot read the block, fill four fields and submit in under a
     * couple of seconds. The render time is encrypted so it cannot be forged,
     * and a missing or unreadable value fails closed.
     */
    private function waitedLongEnough(): bool
    {
        $minimum = (int) config('review.minimum_fill_seconds');

        if ($minimum <= 0) {
            return true;
        }

        $raw = $this->input(self::TIMESTAMP);

        if (! is_string($raw) || $raw === '') {
            return false;
        }

        try {
            $openedAt = (int) Crypt::decryptString($raw);
        } catch (\Throwable) {
            return false;
        }

        $elapsed = time() - $openedAt;

        // Also reject a timestamp from far in the past: a replayed or scraped
        // form rather than one a person just opened.
        return $elapsed >= $minimum && $elapsed <= config('review.maximum_form_age_seconds');
    }
}
