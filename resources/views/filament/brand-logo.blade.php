{{--
    The panel's brand lockup: the client's shield mark beside the wordmark,
    the same two files the public site's header uses.

    One markup for both themes. The mark is supplied with a transparent
    background and the wordmark's "SHIELD" is Cool Silver, so neither needs a
    light-mode variant — only the dark panel surface sits behind them, and the
    login card is Midnight Blue in both themes.
--}}
<img
    class="ub-mark"
    src="{{ asset('images/brand/ub-shield.webp') }}"
    alt=""
    width="752"
    height="775"
    aria-hidden="true"
>
<img
    class="ub-word"
    src="{{ asset('images/brand/ub-wordmark.webp') }}"
    alt="{{ config('app.name') }}"
    width="1349"
    height="165"
>
