{{--
    Closing strip: CTA block, enquiry form, artwork, contact.
    Markup from design/v7/index.html lines 814-833. Copy and form fields are
    the launch brief's §6: no free 45-minute review, no response-time promise,
    and only confirmed contact details (anything still a [placeholder] in Site
    settings is left out rather than shown).
--}}
@props(["preselect" => null])
@php
    $email = setting('contact_email');
    $phone = setting('contact_phone');
    $address = setting('contact_address');
    $hours = setting('contact_hours');
@endphp
<section class="strip" id="contact">
<div class="cta-b">
<h2 class="dl">@foreach (config('site.contact_title') as $line)<span class="rm"><span @class(['alt' => $loop->index === 1])>{{ $line }}</span></span>@endforeach</h2>
<p class="body rv">{{ config('site.contact_lead') }}</p>
@if (session('review.success'))
<p class="form-done" role="status">{{ session('review.success') }}</p>
@else
<form class="form" method="post" action="{{ route('review.store') }}" data-review-form>
@csrf
<x-review-honeypot />
<x-review-context />
@error('form')<p class="form-note is-error" role="alert">{{ $message }}</p>@enderror
<div class="f"><label for="v6n">Name</label><input id="v6n" name="name" type="text" autocomplete="name" placeholder="Your full name" required maxlength="120" value="{{ old('name') }}" @error('name') aria-invalid="true" aria-describedby="v6n-err" @enderror>@error('name')<span class="err" id="v6n-err">{{ $message }}</span>@enderror</div>
<div class="f"><label for="v6e">Business email</label><input id="v6e" name="email" type="email" inputmode="email" spellcheck="false" autocapitalize="off" autocomplete="email" placeholder="you@company.com" required maxlength="190" value="{{ old('email') }}" @error('email') aria-invalid="true" aria-describedby="v6e-err" @enderror>@error('email')<span class="err" id="v6e-err">{{ $message }}</span>@enderror</div>
<div class="f"><label for="v6c">Company / organization</label><input id="v6c" name="company" type="text" autocomplete="organization" placeholder="Company name" required maxlength="120" value="{{ old('company') }}" @error('company') aria-invalid="true" aria-describedby="v6c-err" @enderror>@error('company')<span class="err" id="v6c-err">{{ $message }}</span>@enderror</div>
<div class="f"><label for="v6t">City</label><input id="v6t" name="city" type="text" autocomplete="address-level2" placeholder="Your city" required maxlength="80" value="{{ old('city') }}" @error('city') aria-invalid="true" aria-describedby="v6t-err" @enderror>@error('city')<span class="err" id="v6t-err">{{ $message }}</span>@enderror</div>
<div class="f full"><label for="v6s">Service interested in</label><select id="v6s" name="service" required @error('service') aria-invalid="true" aria-describedby="v6s-err" @enderror><option value="" disabled @selected(! old('service', $preselect))>Choose a service</option>@foreach (\App\Http\Requests\ReviewRequest::services() as $service)<option @selected(old('service', $preselect ?? null) === $service)>{{ $service }}</option>@endforeach</select>@error('service')<span class="err" id="v6s-err">{{ $message }}</span>@enderror</div>
<div class="f full"><label for="v6m">Brief requirement</label><textarea id="v6m" name="requirement" rows="3" placeholder="Your IT environment and what you need" required maxlength="2000" @error('requirement') aria-invalid="true" aria-describedby="v6m-err" @enderror>{{ old('requirement') }}</textarea>@error('requirement')<span class="err" id="v6m-err">{{ $message }}</span>@enderror</div>
<div class="f full consent"><input id="v6k" name="consent" type="checkbox" value="1" required @checked(old('consent')) @error('consent') aria-invalid="true" aria-describedby="v6k-err" @enderror><label for="v6k">I agree to be contacted by UB Shield about this enquiry. See our <a href="{{ route('privacy') }}" class="lnk">privacy policy</a>.</label>@error('consent')<span class="err" id="v6k-err">{{ $message }}</span>@enderror</div>
<div class="f full" style="margin-top: 12px"><button type="submit" class="btn mag" style="justify-content: space-between; width: 100%"><span data-submit-label>Submit enquiry</span> <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"></path></svg></button></div>
</form>
@endif
</div>
<div class="img" aria-hidden="true"><x-art.strip-circuit /></div>
<div class="contact">
@unless (is_placeholder($email))<div><span class="lab-s">Email</span><a href="mailto:{{ $email }}" class="lab-m">{{ $email }}</a></div>@endunless
@unless (is_placeholder($phone))<div><span class="lab-s">Phone</span><a href="{{ contact_href($phone, 'tel') }}" class="lab-m">{{ $phone }}</a></div>@endunless
@unless (is_placeholder($address))<div><span class="lab-s">Office</span><span class="lab-m">{{ $address }}</span></div>@endunless
<div><span class="lab-s">Service locations</span><span class="lab-m">{{ config('site.locations') }}</span></div>
@unless (is_placeholder($hours))<div><span class="lab-s">Working hours</span><span class="lab-m">{{ $hours }}</span></div>@endunless
</div>
</section>
