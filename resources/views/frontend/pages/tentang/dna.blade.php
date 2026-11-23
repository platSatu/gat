@extends('layouts.frontend')

@section('title', __('DNA Kami'))
@section('header_theme', 'dark')

@section('content')
    {{--
        Figma "Desktop - 9" (detail Tentang) + komponen "Pentakosta Ketiga" & "Restorasi Pondok Daud":
        dua kartu persegi, bisa dibalik (klik/tap) untuk menampilkan penjelasannya.
    --}}
    @php
        $cards = [
            [
                'front' => '<span class="is-medium">Pentakosta</span><em>Ketiga</em>',
                'title' => 'Pentakosta Ketiga',
                'body' => [
                    'Pentakosta Ketiga adalah pencurahan Roh Kudus yang dahsyat di zaman ini melebihi yang terjadi di Azusa Street.',
                    'Pentakosta Ketiga akan mengakibatkan penuaian jiwa yang terbesar dan yang terakhir sebelum Tuhan Yesus datang kembali.',
                    'Pentakosta Ketiga akan membangkitkan generasi Yeremia yaitu anak-anak muda yang dipenuhi Roh Kudus, cinta mati-matian kepada Tuhan Yesus, tidak kompromi terhadap dosa, dan akan bergerak untuk memenangkan jiwa.',
                    'Pentakosta Ketiga lahir di Indonesia dan bergerak ke bangsa-bangsa. Gerakan ini dari Timur ke Barat dan akan kembali ke Yerusalem.',
                    'Pentakosta Ketiga akan memberikan kuasa untuk menyelesaikan Amanat Agung dan setelah itu Tuhan Yesus datang kembali.',
                ],
            ],
            [
                'front' => 'Restorasi<br>Pondok Daud',
                'title' => 'Restorasi Pondok Daud',
                'body' => [
                    'GBI Jalan Gatot Subroto berdiri dengan sebuah tugas khusus yang jelas, yaitu:',
                    '<strong>Restorasi Pondok Daud</strong>',
                    'Prajurit-prajurit Tuhan yang gagah perkasa yang gaya hidupnya doa, pujian, penyembahan dalam unity siang dan malam serta melakukan kehendak Bapa pada zaman ini.',
                ],
            ],
        ];
    @endphp

    <section class="page page--white page--center">
        <h1 class="sr-only">{{ __('DNA Kami') }}</h1>
        <div class="dna-grid">
            @foreach ($cards as $card)
                <button type="button" class="dna-card" data-flip aria-pressed="false" aria-label="{{ $card['title'] }}">
                    <span class="dna-card__inner">
                        <span class="dna-card__face dna-card__front"><span>{!! $card['front'] !!}</span></span>
                        <span class="dna-card__face dna-card__back">
                            <span class="dna-card__title">{{ $card['title'] }}</span>
                            @foreach ($card['body'] as $paragraph)
                                <span class="dna-card__text">{!! $paragraph !!}</span>
                            @endforeach
                        </span>
                    </span>
                </button>
            @endforeach
        </div>
    </section>
@endsection
