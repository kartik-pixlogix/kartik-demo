{{--
    Services: 5 cards in the pinned horizontal track.
    Ported verbatim from design/v7/index.html lines 601-648.
    Regenerate with scripts/split-prototype-html.mjs — do not retype markup.
--}}
<section class="sec svc-sec" id="services">
<div class="svc-stick">
<div class="wrap">
<div class="svc-head">
<div>
<h2 class="dl rm"><span>Services<i class="dot"></i></span></h2>
<p class="lab-m rv" style="margin-top: 24px">Secure / Privacy / Compliance / Infrastructure / MSS</p>
</div>
<div class="counter v-hide" aria-hidden="true"><span data-slide>01</span><i></i><span class="tot">05</span></div>
</div>
<div class="track-vp">
<div class="track rs">
<a href="{{ route('services.show', 'offensive-security') }}" class="card">
<div class="art rc"><div class="zoom"><x-art.service-vapt /></div><span class="num">01</span></div>
<div class="meta"><div><p class="lab-l">Offensive Security / VAPT</p><p class="lab-s" style="margin-top: 4px">Secure</p></div><x-ui.arrow-circle /></div>
<p class="body">We attack your apps and networks the way a real adversary would. Then we hand your developers fixes they can ship.</p>
<ul><li>Web, mobile &amp; API</li><li>Network &amp; cloud</li><li>Thick-client</li><li>Code review</li><li>Red teaming</li></ul>
</a>
<a href="{{ route('services.show', 'data-privacy') }}" class="card">
<div class="art rc"><div class="zoom"><x-art.service-privacy /></div><span class="num">02</span></div>
<div class="meta"><div><p class="lab-l">Data Privacy</p><p class="lab-s" style="margin-top: 4px">Privacy</p></div><x-ui.arrow-circle /></div>
<p class="body">We map where personal data lives and who touches it. Then we build the notices and consent flows the DPDP Act expects.</p>
<ul><li>DPDPA 2023</li><li>GDPR</li><li>Data mapping &amp; DPIA</li><li>DPO-as-a-service</li></ul>
</a>
<a href="{{ route('services.show', 'governance-risk-compliance') }}" class="card">
<div class="art k rc"><div class="zoom"><x-art.service-grc /></div><span class="num">03</span></div>
<div class="meta"><div><p class="lab-l">Governance, Risk &amp; Compliance</p><p class="lab-s" style="margin-top: 4px">Compliance</p></div><x-ui.arrow-circle /></div>
<p class="body">We keep policies, controls and evidence in order all year. Audit day becomes a review, not a scramble.</p>
<ul><li>ISO 27001</li><li>SOC 2</li><li>RBI / SEBI</li><li>PCI DSS</li><li>HIPAA</li></ul>
</a>
<a href="{{ route('services.show', 'infrastructure-cloud') }}" class="card">
<div class="art rc"><div class="zoom"><x-art.service-infra /></div><span class="num">04</span></div>
<div class="meta"><div><p class="lab-l">Infrastructure &amp; Cloud</p><p class="lab-s" style="margin-top: 4px">Infrastructure</p></div><x-ui.arrow-circle /></div>
<p class="body">We review architecture and configuration across AWS, Azure and GCP. Then we harden it so your team can keep it that way.</p>
<ul><li>Architecture review</li><li>Cloud config</li><li>Hardening</li><li>Firewall &amp; endpoint</li></ul>
</a>
<a href="{{ route('services.show', 'managed-security-services') }}" class="card">
<div class="art k rc"><div class="zoom"><x-art.service-mss /></div><span class="num">05</span></div>
<div class="meta"><div><p class="lab-l">Managed Security Services</p><p class="lab-s" style="margin-top: 4px">MSS</p></div><x-ui.arrow-circle /></div>
<p class="body">Our 24/7 SOC watches, triages and responds. A named lead knows your estate.</p>
<ul><li>SOC &amp; SIEM</li><li>Detection &amp; response</li><li>Vuln management</li><li>Virtual CISO</li></ul>
</a>
</div>
</div>
<div class="pbar v-hide" aria-hidden="true"><i></i></div>
</div>
</div>
</section>
