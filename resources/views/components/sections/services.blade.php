{{--
    Services: the six approved services (config/practices.php) in the pinned
    horizontal track. Card markup is the prototype's (design/v7/index.html
    lines 601-648); only the content now comes from the launch brief.
--}}
@php($services = \App\Support\Practices::all())
<section class="sec svc-sec" id="services">
<div class="svc-stick">
<div class="wrap">
<div class="svc-head">
<div>
<h2 class="dl rm"><span><span class="tnw">Services<i class="dot"></i></span></span></h2>
<p class="lab-m rv" style="margin-top: 24px">{{ $services->pluck('kicker')->implode(' / ') }}</p>
</div>
<div class="counter v-hide" aria-hidden="true"><span data-slide>01</span><i></i><span class="tot">{{ str_pad((string) $services->count(), 2, '0', STR_PAD_LEFT) }}</span></div>
</div>
<div class="track-vp">
<div class="track rs">
@foreach ($services as $slug => $service)
<a href="{{ route('services.show', $slug) }}" class="card">
<div class="art{{ $service['art_inverted'] ? ' k' : '' }} rc"><div class="zoom"><x-dynamic-component :component="'art.'.$service['art']" /></div><span class="num">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></div>
<div class="meta"><div><p class="lab-l">{{ $service['nav'] }}</p><p class="lab-s" style="margin-top: 4px">{{ $service['kicker'] }}</p></div><x-ui.arrow-circle /></div>
<p class="body">{{ $service['lead'] }}</p>
<ul>@foreach (array_slice($service['included'], 0, 5) as $item)<li>{{ $item }}</li>@endforeach</ul>
</a>
@endforeach
</div>
</div>
<div class="pbar v-hide" aria-hidden="true"><i></i></div>
</div>
</div>
</section>
