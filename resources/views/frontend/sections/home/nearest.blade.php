{{-- Teks berjalan "DOA – PUJIAN – PENYEMBAHAN" + Gereja Terdekat (peta). --}}
@php $words = __('DOA – PUJIAN – PENYEMBAHAN') . ' – '; @endphp

<div class="home-marquee" aria-hidden="true">
    <div class="home-marquee__track">
        {{-- Isi diulang dua kali supaya animasinya menyambung tanpa jeda. --}}
        @for ($i = 0; $i < 12; $i++)
            <span>{{ $words }}</span>
        @endfor
    </div>
</div>

<section class="home-nearest">
    <div class="container home-nearest__inner">
        <div>
            <p class="home-nearest__eyebrow">{{ __('Terhubung dengan') }}</p>
            <h2>{{ __('GEREJA TERDEKAT ANDA') }}</h2>
            <p>{{ __('Temukan gereja di bawah pembinaan GBI Jl. Jend. Gatot Subroto yang berada di kota atau wilayah anda') }}</p>
        </div>
        <div class="home-nearest__map">
            <img src="{{ asset('images/home/world-map.png') }}" alt="" width="1080" height="650" loading="lazy">
            <a href="#" class="btn-pill btn-blue">{{ __('Cari') }}</a>
        </div>
    </div>
</section>
