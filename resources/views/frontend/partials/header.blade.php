{{--
    Header: logo kiri + (pilihan bahasa & tombol Menu) kanan. Daftar bahasa dari tabel `bahasa`.
    Seluruh header tetap diam di atas saat digulung (position: fixed). Di paling atas tanpa latar;
    setelah digulung muncul latar tipis (kelas is-scrolled dari frontend.js) supaya tidak menimpa teks/foto.
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
        {{-- Pilihan bahasa (Figma "BAHASA"): bahasa aktif terang, yang lain redup. Daftarnya dari admin Settings → Bahasa.
             Indonesia & English memakai gambar bendera; bahasa lain tampil sebagai kode (mis. ZH). --}}
        @php $daftarBahasa = [...\App\Models\Bahasa::lain()->all(), ...\App\Models\Bahasa::daftar()->where('kode', 'id')->all()]; @endphp
        @if (count($daftarBahasa) > 1)
            <div class="lang-switch" role="group" aria-label="Bahasa / Language">
                @foreach ($daftarBahasa as $b)
                    <a href="{{ route('locale', $b['kode']) }}" hreflang="{{ $b['kode'] }}" lang="{{ $b['kode'] }}" title="{{ $b['nama'] }}" aria-label="{{ $b['nama'] }}"
                        @class(['is-active' => $locale === $b['kode']]) @if ($locale === $b['kode']) aria-current="true" @endif>
                        @if ($b['kode'] === 'en')
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
                        @elseif ($b['kode'] === 'id')
                            <svg viewBox="0 0 30 20" aria-hidden="true">
                                <rect width="30" height="20" fill="#fff"/>
                                <rect width="30" height="10" fill="#e70011"/>
                            </svg>
                        @else
                            <span class="lang-switch__code" aria-hidden="true">{{ strtoupper($b['kode']) }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif

        <button type="button" class="menu-toggle" data-menu-open aria-expanded="false" aria-controls="site-menu">
            {{ __('Menu') }}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
    </div>
</header>
