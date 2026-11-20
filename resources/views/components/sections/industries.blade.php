{{--
    Industries we aim to serve. Row markup from design/v7/index.html lines
    741-760; the nine sectors are the launch brief's §4 (config/site.php).
    The brief presents these as intended sectors, not client references, and
    gives no per-sector page content, so the rows are not links and there are
    no industry detail pages (old URLs redirect here — routes/web.php).
--}}
<section class="sec" id="industries">
<div class="wrap">
<div class="sec-t"><h2 class="dl rm"><span><span class="tnw">Industries<i class="dot"></i></span></span></h2><p class="rv" style="max-width: 420px">Industries we aim to serve.</p></div>
<div class="inds rs">
@foreach (config('site.industries') as $i => [$name, $detail, $preview])

<div class="ind-row"@if ($preview) data-v="{{ $preview }}"@endif><span class="lab-s">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $name }}</h3><p>{{ $detail }}</p></div>
@endforeach

</div>
</div>
</section>
