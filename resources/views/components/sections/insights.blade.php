{{--
    Field notes: 3 posts and the All resources tile.
    Ported verbatim from design/v7/index.html lines 802-812.
    Regenerate with scripts/split-prototype-html.mjs — do not retype markup.
--}}
<section class="sec" id="insights">
<div class="wrap">
<div class="sec-t"><h2 class="dl rm"><span>Field <span class="tnw">notes<i class="dot"></i></span></span></h2></div>
<div class="grid4 rs">
@foreach ($insights as $insight)
<a href="{{ route('insights.show', $insight->slug) }}" class="post"><x-cover-art :record="$insight" /><div class="meta"><p class="lab-l">{{ $insight->title }}</p><span class="lab-s">{{ $insight->meta_line }}</span></div></a>
@endforeach
<a href="{{ route('insights.index') }}" class="all"><span class="dl">All<br><span class="alt">resources</span></span><x-ui.arrow-circle /></a>
</div>
</div>
</section>
