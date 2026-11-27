{{-- Bagian <body> dari Google Tag Manager & Meta Pixel (untuk browser tanpa JavaScript). Diatur di Settings → Situs & SEO. --}}
@if (\App\Support\Situs::pakaiTracking())
    @if ($gtm = \App\Support\Situs::ambil('gtm_id'))
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtm }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif
    @if ($pixel = \App\Support\Situs::ambil('meta_pixel_id'))
        <noscript><img height="1" width="1" style="display:none" alt="" src="https://www.facebook.com/tr?id={{ $pixel }}&ev=PageView&noscript=1"></noscript>
    @endif
@endif
