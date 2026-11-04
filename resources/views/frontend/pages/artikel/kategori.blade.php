@extends('layouts.frontend')

@section('title', __('Kategori'))

@section('content')
    {{-- Figma "Kategori": 8 kartu (strip gambar + deskripsi). --}}
    @php
        $categories = [
            ['Mengenal Yesus', 'Langkah awal untuk mengenal Yesus dan memahami iman Kristen'],
            ['Gatsu News', 'Berita dan liputan untuk mengikuti perkembangan kegiatan serta pelayanan gereja-gereja di bawah naungan GBI Gatsu'],
            ['Renungan', 'Bacaan harian untuk merenungkan firman Tuhan dan menemukan maknanya dalam keseharian'],
            ['Pesan Gembala', 'Pesan dan arahan gembala sidang untuk menuntun langkah iman kita'],
            ['Teologi', 'Kajian untuk mendalami Alkitab, teologi, dan doktrin Kristen'],
            ['Kehidupan Kristen', 'Panduan untuk menerapkan iman dalam keluarga, pekerjaan, relasi, dan pilihan sehari-hari'],
            ['Kisah Inspiratif', 'Kesaksian dan cerita kehidupan untuk menguatkan iman dan menumbuhkan pengharapan'],
            ['Komunitas', 'Bahan belajar dan diskusi untuk bertumbuh bersama dalam komunitas'],
        ];
        $images = ['images/about/berakar.jpg', 'images/pages/sejarah.jpg', 'images/about/mengenal.jpg', 'images/about/restorasi.jpg'];
    @endphp

    <section class="page page--slate">
        <div class="container">
            <h1 class="page-title">{{ __('Kategori') }}</h1>
            <div class="category-grid">
                @foreach ($categories as [$name, $description])
                    <a href="{{ route('articles.category') }}" class="strip-card">
                        <div class="strip-card__img" style="background-image: url('{{ asset($images[$loop->index % 4]) }}')"><span>{{ __($name) }}</span></div>
                        <div class="strip-card__body">{{ __($description) }}</div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
