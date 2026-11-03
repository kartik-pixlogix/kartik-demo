{{--
    Closing strip: CTA block, form, artwork, contact.
    Ported verbatim from design/v7/index.html lines 814-833.
    Regenerate with scripts/split-prototype-html.mjs — do not retype markup.
--}}
<section class="strip" id="contact">
<div class="cta-b">
<h2 class="dl"><span class="rm"><span>Let's run</span></span><span class="rm"><span class="alt">your free</span></span><span class="rm"><span>posture review.</span></span></h2>
<p class="body rv">Forty-five minutes with a senior consultant. You leave with your five biggest risks and the order to fix them in.</p>
@if (session('review.success'))
<p class="form-done" role="status">{{ session('review.success') }}</p>
@else
<form class="form" method="post" action="{{ route('review.store') }}" data-review-form>
@csrf
<x-review-honeypot />
<x-review-context />
@error('form')<p class="form-note is-error" role="alert">{{ $message }}</p>@enderror
<div class="f"><label for="v6n">Name</label><input id="v6n" name="name" type="text" autocomplete="name" placeholder="Your full name" required maxlength="120" value="{{ old('name') }}" @error('name') aria-invalid="true" aria-describedby="v6n-err" @enderror>@error('name')<span class="err" id="v6n-err">{{ $message }}</span>@enderror</div>
<div class="f"><label for="v6e">Work email</label><input id="v6e" name="email" type="email" inputmode="email" spellcheck="false" autocapitalize="off" autocomplete="email" placeholder="you@company.com" required maxlength="190" value="{{ old('email') }}" @error('email') aria-invalid="true" aria-describedby="v6e-err" @enderror>@error('email')<span class="err" id="v6e-err">{{ $message }}</span>@enderror</div>
<div class="f"><label for="v6c">Company</label><input id="v6c" name="company" type="text" autocomplete="organization" placeholder="Company name" required maxlength="120" value="{{ old('company') }}" @error('company') aria-invalid="true" aria-describedby="v6c-err" @enderror>@error('company')<span class="err" id="v6c-err">{{ $message }}</span>@enderror</div>
<div class="f"><label for="v6s">Service interest</label><select id="v6s" name="service" required @error('service') aria-invalid="true" aria-describedby="v6s-err" @enderror>@foreach (\App\Http\Requests\ReviewRequest::SERVICES as $service)<option @selected(old('service') === $service)>{{ $service }}</option>@endforeach</select>@error('service')<span class="err" id="v6s-err">{{ $message }}</span>@enderror</div>
<div class="f full" style="margin-top: 12px"><button type="submit" class="btn mag" style="justify-content: space-between; width: 100%"><span data-submit-label>Request my review</span> <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"></path></svg></button></div>
</form>
@endif
</div>
<div class="img" aria-hidden="true"><x-art.strip-circuit /></div>
<div class="contact">
<div><span class="lab-s">Email</span><a href="{{ contact_href(setting('contact_email'), 'mailto') }}" class="lab-m">{{ setting('contact_email') }}</a></div>
<div><span class="lab-s">Phone</span><a href="{{ contact_href(setting('contact_phone'), 'tel') }}" class="lab-m">{{ setting('contact_phone') }}</a></div>
<div><span class="lab-s">Office</span><span class="lab-m">{{ setting('contact_address') }}<br>Ahmedabad, Gujarat, India</span></div>
<div><span class="lab-s">Response</span><span class="lab-m">{{ setting('contact_response') }}</span></div>
</div>
</section>
