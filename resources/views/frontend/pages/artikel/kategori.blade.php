@extends('layouts.frontend')

@section('title', __('Kategori'))

@section('content')
    {{-- Figma "Kategori": kartu (gambar utuh + nama kategori + deskripsi). Kategori dari database; gambar = artikel terbaru di kategori itu. --}}
    @php
        // Deskripsi untuk kategori yang namanya sama dengan desain Figma; kategori lain cukup jumlah artikelnya.
        $deskripsi = [
            'Mengenal Yesus' => 'Langkah awal untuk mengenal Yesus dan memahami iman Kristen',
            'Gatsu News' => 'Berita dan liputan untuk mengikuti perkembangan kegiatan serta pelayanan gereja-gereja di bawah naungan GBI Gatsu',
            'Renungan' => 'Bacaan harian untuk merenungkan firman Tuhan dan menemukan maknanya dalam keseharian',
            'Pesan Gembala' => 'Pesan dan arahan gembala sidang untuk menuntun langkah iman kita',
            'Teologi' => 'Kajian untuk mendalami Alkitab, teologi, dan doktrin Kristen',
            'Kehidupan Kristen' => 'Panduan untuk menerapkan iman dalam keluarga, pekerjaan, relasi, dan pilihan sehari-hari',
            'Kisah Inspiratif' => 'Kesaksian dan cerita kehidupan untuk menguatkan iman dan menumbuhkan pengharapan',
            'Komunitas' => 'Bahan belajar dan diskusi untuk bertumbuh bersama dalam komunitas',
        ];
        $cadangan = ['images/about/berakar.jpg', 'images/pages/sejarah.jpg', 'images/about/mengenal.jpg', 'images/about/restorasi.jpg'];
    @endphp

    <section class="page page--slate">
        <div class="container">
            <h1 class="page-title">{{ __('Kategori') }}</h1>
            <div class="category-grid">
                @foreach ($kategori as $item)
                    <a href="{{ route('articles.category', $item) }}" class="strip-card">
                        <img class="strip-card__photo" src="{{ $item->sampul?->image_url ?? asset($cadangan[$loop->index % 4]) }}" alt="" loading="lazy">
                        <div class="strip-card__body">
                            <h3>{{ $item->name }}</h3>
                            @isset($deskripsi[$item->name]){{ __($deskripsi[$item->name]) }}<br>@endisset
                            <small>{{ $item->artikel_count }} {{ __('artikel') }}</small>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
