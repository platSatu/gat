@extends('layouts.frontend')

@section('title', __('Kegiatan'))
@section('header_theme', 'dark')

@section('content')
    {{--
        Figma "Desktop - 3": Kegiatan Mendatang (besar) + Kegiatan Sebelumnya & Gatsu News.
        - Kegiatan Sebelumnya = highlight terbaru (tabel `highlights`), dari HighlightController@events.
        - Gatsu News = artikel terbaru (kategori "Gatsu News" kalau ada); tombol putar -> artikel itu, tombol -> halaman Artikel.
        - Kegiatan Mendatang masih statis (belum ada tabel kegiatan mendatang di database).
    --}}
    @php
        $mendatang = [
            'tag' => __('Kegiatan Mendatang'),
            'title' => 'Planetshakers Live in Indonesia 2027',
            'date' => 'Friday, July 2, 2027',
            'button' => __('Daftar Sekarang'),
            'image' => 'images/pages/kegiatan-3.jpg',
        ];

        $sebelumnya = $highlight ? [
            'tag' => __('Kegiatan Sebelumnya'),
            'title' => $highlight->title,
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
                @include('frontend.pages.kegiatan._tile', ['event' => $mendatang])
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
