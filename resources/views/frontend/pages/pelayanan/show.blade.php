@extends('layouts.frontend')

@section('title', 'Panti Werda - Graha Bina Asuh')

@section('content')
    {{-- Figma "Pelayanan & Ministry" (detail): kartu krem bertumpuk di atas foto. --}}
    <section class="ministry-stack">
        <img src="{{ asset('images/about/berakar.jpg') }}" alt="">
        <article class="ministry-card">
            <div class="ministry-card__head">
                <h1>PANTI WERDA - GRAHA BINA ASUH</h1>
                <span>@include('frontend.partials.icon', ['name' => 'chevron-down'])</span>
            </div>

            <div class="ministry-card__grid">
                <div class="ministry-card__video">
                    <img src="{{ asset('images/pages/kegiatan-1.jpg') }}" alt="">
                    <a href="#" class="play-btn" aria-label="{{ __('Putar video') }}">@include('frontend.partials.icon', ['name' => 'play'])</a>
                    <div class="ministry-card__tools">
                        <a href="#" aria-label="{{ __('Bagikan') }}">@include('frontend.partials.icon', ['name' => 'share'])</a>
                        <a href="#" aria-label="{{ __('Unduh') }}">@include('frontend.partials.icon', ['name' => 'download'])</a>
                    </div>
                </div>

                <div class="ministry-card__text">
                    <p>Rumah Kedua yang Penuh Kasih. Ketika seseorang yang Anda kasihi membutuhkan tempat untuk merasa dicintai, tempat untuk pulih dan hidup dengan tenang, kami rindu Anda teringat satu nama: Graha Bina Asuh. Kami percaya bahwa kasih Tuhan nyata saat kita peduli pada sesama. Di Graha Bina Asuh, kami melayani:</p>
                    <ul>
                        <li>Orang tua yang membutuhkan pendampingan dan perhatian khusus</li>
                        <li>Anak-anak yang kehilangan orang tua dan butuh kasih sayang untuk bertumbuh</li>
                    </ul>
                    <p>Kami menghadirkan:</p>
                    <ul>
                        <li>Lingkungan yang hangat dan penuh pengertian</li>
                        <li>Pendampingan yang tulus dan menyeluruh</li>
                        <li>Suasana yang menumbuhkan damai dan harapan akan masa depan</li>
                    </ul>
                    <p>Mari bersama, kita hadirkan harapan baru, langkah baru, dan semangat baru bagi mereka yang membutuhkan.</p>
                    <a href="#" class="btn-pill btn-blue btn-sm">{{ __('Jelajahi Pelayanan Kami') }}</a>
                </div>
            </div>
        </article>
    </section>
@endsection
