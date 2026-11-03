{{--
    Managed SOC: radar, IST clock, incident feed.
    Ported verbatim from design/v7/index.html lines 707-739.
    Regenerate with scripts/split-prototype-html.mjs — do not retype markup.
--}}
<section class="soc ink-sec" id="soc">
<span class="ch" style="left: 64px; top: 48px"></span><span class="ch" style="right: 64px; bottom: 48px"></span>
<div class="wrap soc-g">
<div>
<span class="chip">Managed SOC / MSS</span>
<h2 class="dl rm" style="font-size: 96px; line-height: 80px; margin-top: 32px"><span>We never</span></h2>
<h2 class="dl rm" style="font-size: 96px; line-height: 80px"><span class="alt" style="font-weight: 700">clock off</span></h2>
<h2 class="dl rm" style="font-size: 96px; line-height: 80px"><span>at night.<i class="dot"></i></span></h2>
<p class="body rv">Our analysts work your alerts in shifts from Ahmedabad, every hour of the year. You get a named lead and a monthly review in plain language.</p>
<ul class="soc-list rs">
<li><span>SIEM monitoring</span><span>Cloud / endpoint / identity / firewall</span></li>
<li><span>Detection &amp; response</span><span>Containment agreed in advance</span></li>
<li><span>CERT-In reporting</span><span>{{ setting('soc_cert_in_window') }}</span></li>
<li><span>Mean time to respond</span><span>{{ setting('stat_mean_time_to_respond') }}</span></li>
</ul>
<a href="#contact" class="tl mag"><span class="t">Scope a managed SOC</span><x-ui.arrow-circle on-acid /></a>
</div>
<div>
<div class="radar" data-px="-0.06" role="img" aria-label="Illustration of the SOC watch: a radar sweep with live detections and a local clock">
<span class="ring" style="width: 100%; height: 100%"></span><span class="ring" style="width: 70%; height: 70%"></span><span class="ring" style="width: 40%; height: 40%"></span>
<span class="x"></span><span class="y"></span>
<span class="sweep"></span>
<span class="blip" style="left: 68%; top: 30%"></span><span class="blip" style="left: 30%; top: 64%; animation-delay: 1.4s"></span><span class="blip" style="left: 74%; top: 72%; animation-delay: 2.6s"></span>
<div class="soc-hud"><span class="lab-s">Ahmedabad / IST</span><span class="clock" data-clock="1">--:--:--</span></div>
</div>
<div class="feed" aria-hidden="true">
<div class="feed-in">
<div><span>02:14:07</span><span>Impossible-travel sign-in blocked</span><b>Contained</b></div><div><span>02:13:51</span><span>Unsigned binary quarantined</span><b>Resolved</b></div><div><span>02:12:30</span><span>SQL injection dropped at login API</span><b>Blocked</b></div><div><span>02:11:02</span><span>Public bucket set back to private</span><b>Fixed</b></div><div><span>02:09:44</span><span>Dormant admin key disabled</span><b>Resolved</b></div><div><span>02:08:19</span><span>Beacon to known C2 range denied</span><b>Blocked</b></div><div><span>02:14:07</span><span>Impossible-travel sign-in blocked</span><b>Contained</b></div><div><span>02:13:51</span><span>Unsigned binary quarantined</span><b>Resolved</b></div><div><span>02:12:30</span><span>SQL injection dropped at login API</span><b>Blocked</b></div><div><span>02:11:02</span><span>Public bucket set back to private</span><b>Fixed</b></div><div><span>02:09:44</span><span>Dormant admin key disabled</span><b>Resolved</b></div><div><span>02:08:19</span><span>Beacon to known C2 range denied</span><b>Blocked</b></div>
</div>
</div>
</div>
</div>
</section>
