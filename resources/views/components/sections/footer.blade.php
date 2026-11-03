{{--
    Footer: 4 link columns, shield lockup, bottom bar.
    Ported verbatim from design/v7/index.html lines 835-846.
    Regenerate with scripts/split-prototype-html.mjs — do not retype markup.
--}}
<footer class="foot">
<div class="wrap">
<div class="foot-g rs">
<div><h3>Services<i class="dot"></i></h3><ul><li><a href="{{ route('services.show', 'offensive-security') }}">Offensive Security / VAPT</a></li><li><a href="{{ route('services.show', 'data-privacy') }}">Data Privacy</a></li><li><a href="{{ route('services.show', 'governance-risk-compliance') }}">GRC</a></li><li><a href="{{ route('services.show', 'infrastructure-cloud') }}">Infrastructure &amp; Cloud</a></li><li><a href="{{ route('services.show', 'managed-security-services') }}">Managed SOC</a></li></ul></div>
<div><h3>Company<i class="dot"></i></h3><ul><li><a href="{{ route('about') }}">About</a></li><li><a href="{{ home_anchor('industries') }}">Industries</a></li><li><a href="{{ route('insights.index') }}">Resources</a></li><li><a href="{{ route('about') }}">Careers</a></li></ul></div>
<div><h3>Contact<i class="dot"></i></h3><ul><li><a href="{{ contact_href(setting('contact_email'), 'mailto') }}">{{ setting('contact_email') }}</a></li><li><a href="{{ contact_href(setting('contact_phone'), 'tel') }}">{{ setting('contact_phone') }}</a></li><li><a href="{{ route('contact') }}">Ahmedabad, India</a></li></ul></div>
<div><h3>Follow<i class="dot"></i></h3><ul><li><a href="#top">LinkedIn</a></li><li><a href="#top">X</a></li></ul></div>
</div>
<div class="flogo" role="img" aria-label="UB Shield"><span data-px="0.06"><x-shield-emblem class="femb" /><span class="fwm"><b>UB</b> SHIELD</span></span></div>
<div class="fb lab-s"><span>Secure / Privacy / Compliance / Infrastructure / MSS</span><span>© 2026 UB Shield<a href="{{ route('privacy') }}">Privacy policy</a><a href="{{ route('terms') }}">Terms</a><a href="{{ route('responsible-disclosure') }}">Responsible disclosure</a></span></div>
</div>
</footer>
