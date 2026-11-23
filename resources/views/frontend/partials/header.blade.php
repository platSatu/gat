{{--
    Header: logo kiri + (pilihan bahasa & tombol Menu) kanan.
    Seluruh header tetap diam di atas saat digulung (position: fixed). Setelah digulung,
    header diberi latar (class .is-scrolled dari frontend.js) supaya logo tetap terbaca.
    Halaman berlatar terang memakai logo gelap: @section('header_theme', 'dark').
--}}
@php
    $darkHeader = trim($__env->yieldContent('header_theme')) === 'dark';
    $logo = $darkHeader ? 'images/logo/logo-dark.png' : 'images/logo/Logo-GBIGATSU.svg';
    $locale = app()->getLocale();
@endphp

<header @class(['site-header', 'site-header--dark' => $darkHeader]) data-site-header>
    <a href="{{ route('home') }}" class="site-logo" aria-label="{{ __('Beranda') }}">
        <img src="{{ asset($logo) }}?v={{ filemtime(public_path($logo)) }}" alt="{{ config('app.name') }}" width="230" height="40">
    </a>

    <div class="site-header__actions">
        {{-- Pilihan bahasa (Figma "BAHASA"): bendera aktif terang, yang lain redup. --}}
        <div class="lang-switch" role="group" aria-label="Bahasa / Language">
            <a href="{{ route('locale', 'en') }}" hreflang="en" lang="en" title="English" aria-label="English"
                @class(['is-active' => $locale === 'en']) @if ($locale === 'en') aria-current="true" @endif>
                <svg viewBox="0 0 38 20" aria-hidden="true">
                    <rect width="38" height="20" fill="#b22234"/>
                    <path d="M0 2.3h38M0 5.4h38M0 8.5h38M0 11.5h38M0 14.6h38M0 17.7h38" stroke="#fff" stroke-width="1.54"/>
                    <rect width="15.2" height="10.8" fill="#3c3b6e"/>
                    <g fill="#fff">
                        <circle cx="2.5" cy="2" r=".6"/><circle cx="5.5" cy="2" r=".6"/><circle cx="8.5" cy="2" r=".6"/><circle cx="11.5" cy="2" r=".6"/>
                        <circle cx="4" cy="4.3" r=".6"/><circle cx="7" cy="4.3" r=".6"/><circle cx="10" cy="4.3" r=".6"/><circle cx="13" cy="4.3" r=".6"/>
                        <circle cx="2.5" cy="6.6" r=".6"/><circle cx="5.5" cy="6.6" r=".6"/><circle cx="8.5" cy="6.6" r=".6"/><circle cx="11.5" cy="6.6" r=".6"/>
                        <circle cx="4" cy="8.9" r=".6"/><circle cx="7" cy="8.9" r=".6"/><circle cx="10" cy="8.9" r=".6"/><circle cx="13" cy="8.9" r=".6"/>
                    </g>
                </svg>
            </a>
            <a href="{{ route('locale', 'id') }}" hreflang="id" lang="id" title="Bahasa Indonesia" aria-label="Bahasa Indonesia"
                @class(['is-active' => $locale === 'id']) @if ($locale === 'id') aria-current="true" @endif>
                <svg viewBox="0 0 30 20" aria-hidden="true">
                    <rect width="30" height="20" fill="#fff"/>
                    <rect width="30" height="10" fill="#e70011"/>
                </svg>
            </a>
        </div>

        <button type="button" class="menu-toggle" data-menu-open aria-expanded="false" aria-controls="site-menu">
            {{ __('Menu') }}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
    </div>
</header>
