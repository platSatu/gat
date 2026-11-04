{{--
    Header: logo kiri + tombol Menu (pill putih) kanan, melayang di atas konten.
    Halaman berlatar terang memakai logo gelap: @section('header_theme', 'dark').
--}}
@php
    $darkHeader = trim($__env->yieldContent('header_theme')) === 'dark';
    $logo = $darkHeader ? 'images/logo/logo-dark.png' : 'images/logo/Logo-GBIGATSU.svg';
@endphp

<header class="site-header">
    <a href="{{ route('home') }}" class="site-logo" aria-label="{{ __('Beranda') }}">
        <img src="{{ asset($logo) }}?v={{ filemtime(public_path($logo)) }}" alt="{{ config('app.name') }}" width="230" height="40">
    </a>

    <button type="button" class="menu-toggle" data-menu-open aria-expanded="false" aria-controls="site-menu">
        {{ __('Menu') }}
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
    </button>
</header>
