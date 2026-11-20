<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ReviewRequest extends FormRequest
{
    /** The last service-interest option, for enquiries that do not fit one. */
    public const NOT_SURE = 'Not sure yet';

    /**
     * The service-interest options: the six approved services in catalogue
     * order (config/practices.php), then "Not sure yet".
     *
     * @return list<string>
     */
    public static function services(): array
    {
        return [...\App\Support\Practices::all()->pluck('nav')->values()->all(), self::NOT_SURE];
    }

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
            'service' => ['required', 'string', Rule::in(self::services())],
            // Launch brief §6: City, Brief Requirement and consent to be contacted.
            'city' => ['required', 'string', 'min:2', 'max:80'],
            'requirement' => ['required', 'string', 'min:10', 'max:2000'],
            'consent' => ['accepted'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'Please tell us your name',
            'name.min' => 'Please tell us your name',
            'email.required' => 'Please give us a business email',
            'email.email' => 'That does not look like a working email address',
            'company.required' => 'Please tell us your company',
            'company.min' => 'Please tell us your company',
            'service.required' => 'Please choose a service',
            'service.in' => 'Please choose a service from the list',
            'city.required' => 'Please tell us your city',
            'city.min' => 'Please tell us your city',
            'requirement.required' => 'Please describe your requirement briefly',
            'requirement.min' => 'Please add a little more detail',
            'consent.accepted' => 'Please confirm we may contact you',
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
