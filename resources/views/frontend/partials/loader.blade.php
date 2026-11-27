{{--
    Layar pembuka / loading screen (Figma "Loading Screen"), diatur di admin Hero Beranda → Layar Pembuka.
    Animasi: tulisan/logo diam, lalu bergeser ke kiri; setelah itu lingkaran mengecil ke tengah dan membuka halaman.
    Bawaan (isian admin kosong): teks "GBI Gatsu", warna Figma, diam 0,9 detik, sekali per kunjungan.
    Kapan muncul:
      sesi    = sekali per kunjungan (sessionStorage)
      beranda = setiap kali beranda dibuka (halaman lain tidak)
      sekali  = hanya kunjungan pertama di browser itu (localStorage)
    Skrip kecil di bawah langsung menghapusnya kalau sudah pernah tampil, supaya tidak sempat berkedip.
    Sisa animasinya di public/js/frontend.js.
--}}
@php
    $pembuka = \App\Support\Situs::pembuka();
    $tampil = $pembuka['aktif'] && ($pembuka['muncul'] !== 'beranda' || request()->routeIs('home'));
    $gaya = collect([
        $pembuka['latar'] ? '--loader-bg: '.$pembuka['latar'] : null,
        $pembuka['warna_teks'] ? '--loader-fg: '.$pembuka['warna_teks'] : null,
    ])->filter()->implode('; ');
@endphp

@if ($tampil)
    <div class="site-loader" data-loader data-hold="{{ $pembuka['diam'] }}" data-mode="{{ $pembuka['muncul'] }}" aria-hidden="true" @if ($gaya) style="{{ $gaya }}" @endif>
        <span class="site-loader__circle"></span>
        <span class="site-loader__mask">
            @if ($pembuka['logo'])
                <img class="site-loader__logo" src="{{ $pembuka['logo'] }}" alt="">
            @else
                <span class="site-loader__text">{{ $pembuka['teks'] }}</span>
            @endif
        </span>
    </div>
    <script>
        (function () {
            var el = document.querySelector('[data-loader]'), mode = el.getAttribute('data-mode');
            try {
                var tempat = mode === 'sekali' ? localStorage : (mode === 'sesi' ? sessionStorage : null);
                if (tempat && tempat.getItem('gatsu-loaded') === '1') { el.remove(); }
            } catch (e) {}
        })();
    </script>
@endif
