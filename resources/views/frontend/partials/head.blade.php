{{--
    <head> website publik. SEO, gambar share, favicon & integrasi diatur di admin Settings → Situs & SEO (App\Support\Situs).
    Per halaman: @section('title'), @section('meta_description'), @section('og_image') (URL lengkap gambar, opsional).
    Di selain domain utama (demo, localhost): otomatis noindex dan skrip analytics tidak dimuat.
--}}
@php
    $namaSitus = \App\Support\Situs::nama();
    $judulHalaman = trim($__env->yieldContent('title'));
    $judulTab = $judulHalaman !== '' ? $judulHalaman.' | '.$namaSitus : \App\Support\Situs::judulBeranda();
    $deskripsi = trim($__env->yieldContent('meta_description')) ?: \App\Support\Situs::deskripsi();
    $gambarShare = trim($__env->yieldContent('og_image')) ?: \App\Support\Situs::ogGambar();
    $kataKunci = \App\Support\Situs::kataKunci();
    $tracking = \App\Support\Situs::pakaiTracking();
    $gtm = $tracking ? \App\Support\Situs::ambil('gtm_id') : null;
    $ga4 = $tracking && ! $gtm ? \App\Support\Situs::ambil('ga4_id') : null;
    $pixel = $tracking ? \App\Support\Situs::ambil('meta_pixel_id') : null;
    $twitter = \App\Support\Situs::ambil('twitter_akun');
@endphp
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $judulTab }}</title>
<meta name="description" content="{{ $deskripsi }}">
@if ($kataKunci)<meta name="keywords" content="{{ $kataKunci }}">@endif
@if (\App\Support\Situs::noindex())<meta name="robots" content="noindex, nofollow">@endif
<link rel="canonical" href="{{ url()->current() }}">
<link rel="shortcut icon" href="{{ \App\Support\Situs::favicon() }}">
<link rel="apple-touch-icon" href="{{ \App\Support\Situs::favicon() }}">

{{-- Tampilan saat link dibagikan (WhatsApp, Facebook, X, LinkedIn, ...) --}}
<meta property="og:type" content="{{ trim($__env->yieldContent('og_type')) ?: 'website' }}">
<meta property="og:site_name" content="{{ $namaSitus }}">
<meta property="og:title" content="{{ $judulHalaman !== '' ? $judulHalaman : $judulTab }}">
<meta property="og:description" content="{{ $deskripsi }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:locale" content="{{ app()->getLocale() === 'id' ? 'id_ID' : str_replace('-', '_', app()->getLocale()) }}">
@if ($gambarShare)
    <meta property="og:image" content="{{ $gambarShare }}">
    <meta name="twitter:image" content="{{ $gambarShare }}">
@endif
<meta name="twitter:card" content="{{ $gambarShare ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $judulHalaman !== '' ? $judulHalaman : $judulTab }}">
<meta name="twitter:description" content="{{ $deskripsi }}">
@if ($twitter)<meta name="twitter:site" content="{{ '@'.ltrim($twitter, '@') }}">@endif

{{-- Verifikasi kepemilikan website --}}
@if ($gsc = \App\Support\Situs::ambil('gsc_kode'))<meta name="google-site-verification" content="{{ $gsc }}">@endif
@if ($bing = \App\Support\Situs::ambil('bing_kode'))<meta name="msvalidate.01" content="{{ $bing }}">@endif

@if ($gtm)
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer',@json($gtm));</script>
@elseif ($ga4)
    <!-- Google Analytics 4 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4 }}"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config',@json($ga4));</script>
@endif
@if ($pixel)
    <!-- Meta Pixel -->
    <script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init',@json($pixel));fbq('track','PageView');</script>
@endif
@if ($tracking && ($kodeHead = \App\Support\Situs::ambil('kode_head')))
    {!! $kodeHead !!}
@endif

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,700;0,800;1,300;1,700&display=swap" rel="stylesheet">
<link href="{{ asset('css/frontend.css') }}?v={{ filemtime(public_path('css/frontend.css')) }}" rel="stylesheet">
@stack('styles')
