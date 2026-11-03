{{--
    Where the lead came from, as hidden inputs.

    Rendered server-side from the current request rather than read by JS, so
    attribution survives with scripting disabled. The controller keeps only the
    path from source_page, never a supplied absolute URL.
--}}
<input type="hidden" name="source_page" value="{{ request()->path() === '/' ? '/' : '/'.request()->path() }}">
@foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'] as $utm)
    @if (filled(request()->query($utm)))<input type="hidden" name="{{ $utm }}" value="{{ str(request()->query($utm))->limit(190) }}">@endif
@endforeach
