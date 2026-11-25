@extends('layouts.frontend')

@section('title', __('Kegiatan'))
@section('header_theme', 'dark')

@section('content')
    {{--
        Figma "Desktop - 3": Kegiatan Mendatang (besar) + Kegiatan Sebelumnya & Gatsu News.
        - Kegiatan Sebelumnya = highlight terbaru (tabel `highlights`), dari HighlightController@events.
        - Gatsu News = artikel terbaru (kategori "Gatsu News" kalau ada); tombol putar -> artikel itu, tombol -> halaman Artikel.
        - Kegiatan Mendatang: kalau tidak ada data, tampil kotak "Nantikan kegiatan berikutnya!".
    --}}
    @php
        // Kegiatan Mendatang: belum ada sumber data di database, jadi $mendatang dari controller masih null
        // dan yang tampil adalah kotak "Tidak ada acara" (Figma node 1205-402).
        // Gambarnya: public/images/pages/kegiatan-kosong.jpg (ekspor dari Figma); selama belum ada, pakai latar pola titik.
        $mendatang ??= null;
        $kosong = [
            'tag' => __('Kegiatan Mendatang'),
            'title' => __('Nantikan kegiatan berikutnya!'),
            'text' => __('Saat ini belum ada kegiatan mendatang yang diumumkan, sambil menunggu, lihat kembali'),
            'button' => __('Lihat Selengkapnya'),
            'url' => $highlight ? route('highlights.show', $highlight) : route('home'),
            'image' => file_exists(public_path('images/pages/kegiatan-kosong.jpg')) ? 'images/pages/kegiatan-kosong.jpg' : null,
            'play' => false,
            'class' => 'event-tile--empty',
        ];

        $sebelumnya = $highlight ? [
            'tag' => __('Kegiatan Sebelumnya'),
            'title' => $highlight->tr('title'),
            'date' => $highlight->date_post?->translatedFormat('l, j F Y'),
            'button' => __('Lihat Dokumentasi'),
            'image_url' => $highlight->image_url,
            'image' => 'images/pages/kegiatan-2.jpg',
            'url' => route('highlights.show', $highlight),
        ] : null;

        $news = [
            'tag' => 'Gatsu News',
            'button' => __('Lihat Selengkapnya'),
            'image_url' => $gatsuNews?->image_url,
            'image' => 'images/pages/kegiatan-1.jpg',
            'url' => route('articles'),
            'play' => $gatsuNews ? route('articles.show', $gatsuNews) : null,
        ];
    @endphp

    <section class="page page--cream">
        <div class="container">
            <h1 class="sr-only">{{ __('Kegiatan') }}</h1>
            <div class="events-grid">
                @include('frontend.pages.kegiatan._tile', ['event' => $mendatang ?? $kosong])
                <div class="events-grid__side">
                    @if ($sebelumnya)
                        @include('frontend.pages.kegiatan._tile', ['event' => $sebelumnya])
                    @endif
                    @include('frontend.pages.kegiatan._tile', ['event' => $news])
                </div>
            </div>
        </div>
    </section>
@endsection
