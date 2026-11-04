@extends('layouts.frontend')

@section('title', __('Tentang Gatsu'))

@section('content')
    {{-- Figma "Desktop - 2": 4 bagian foto layar penuh. Isi sementara di $blocks; nanti dari admin. --}}
    @php
        $blocks = [
            [
                'title' => __('Mengenal GBI Gatsu'), 'subtitle' => __('Berawal dari sebuah panggilan'), 'image' => 'images/about/mengenal.jpg',
                'text' => __('GBI Jl. Jend. Gatot Subroto, atau yang lebih dikenal sebagai GBI Gatsu, adalah gereja lokal di bawah naungan Sinode Gereja Bethel Indonesia. Berdiri sejak 4 September 1988 di Jakarta, GBI Gatsu terus bertumbuh menjadi sebuah keluarga besar gereja apostolik yang terdiri dari rayon, cabang khusus, dan gereja-gereja cabang di bawah pembinaannya.'),
                'button' => __('Ikuti Perjalanan GBI Gatsu'), 'url' => route('about.history'),
            ],
            [
                'title' => __('Dipimpin dalam Ketaatan kepada Tuhan'), 'image' => 'images/about/dipimpin.jpg',
                'text' => __('GBI Gatsu digembalakan oleh Pdt. Dr. Ir. Niko Njotorahardjo sebagai Gembala Jemaat, Gembala Pembina, sekaligus Bapa Rohani. Perjalanan pelayanannya dimulai dari panggilan Tuhan untuk melayani secara penuh waktu dan membawa umat-Nya masuk ke dalam hadirat Tuhan melalui doa, pujian, dan penyembahan.'),
                'button' => __('Lihat Selengkapnya'), 'url' => route('about.history'),
            ],
            [
                'title' => __('Menghidupi Panggilan Restorasi Pondok Daud'), 'image' => 'images/about/restorasi.jpg',
                'text' => __('Sejak awal, GBI Gatsu menerima panggilan khusus untuk merestorasi Pondok Daud. Panggilan ini diwujudkan dengan membangun prajurit-prajurit Tuhan yang gagah perkasa, yang memiliki gaya hidup doa, pujian, dan penyembahan dalam unity siang dan malam, serta melakukan kehendak Bapa pada zaman ini.'),
                'button' => __('Pahami Panggilan yang Kami Hidupi'), 'url' => route('about.dna'),
            ],
            [
                'title' => __('Berakar dalam Firman, Melangkah dalam Iman'), 'image' => 'images/about/berakar.jpg',
                'text' => __('Sebagai bagian dari Gereja Bethel Indonesia, GBI Gatsu berpegang pada Alkitab sebagai dasar iman dan kehidupan. Setiap pengajaran, pelayanan, dan langkah gereja diarahkan untuk membawa umat semakin mengenal Kristus, hidup dalam kuasa Roh Kudus, bertumbuh dalam kebenaran firman Tuhan, dan mengerjakan Amanat Agung.'),
                'button' => __('Kenali Dasar Iman Kami'), 'url' => route('about.dna'),
            ],
        ];
    @endphp

    @foreach ($blocks as $block)
        <section class="about-block">
            <img class="about-block__img" src="{{ asset($block['image']) }}" alt="" @if (! $loop->first) loading="lazy" @endif>
            <div class="container">
                <h2>{{ $block['title'] }}</h2>
                @isset($block['subtitle'])<h3>{{ $block['subtitle'] }}</h3>@endisset
                <p>{{ $block['text'] }}</p>
                <a href="{{ $block['url'] }}" class="btn-pill btn-sm text-ink">{{ $block['button'] }}</a>
            </div>
        </section>
    @endforeach
@endsection
