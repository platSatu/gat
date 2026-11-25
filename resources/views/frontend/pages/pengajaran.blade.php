@extends('layouts.frontend')

@section('title', __('Pengajaran'))

@section('content')
    {{-- Figma "Artikel" (Pengajaran): cari, KOM Everyone, kategori (geser ke samping), artikel terpopuler.
         Kategori & artikel dari database (ArticleController@teaching). Gambar KOM sementara dari Figma. --}}
    <section class="page page--slate teach-page">
        <div class="container">
            <h1 class="teach-page__label">{{ __('Pengajaran') }}</h1>
            @include('frontend.pages.artikel._search', ['withCategories' => false])

            {{-- KOM Everyone --}}
            <div class="teach-kom">
                <div class="teach-kom__banner">
                    <img src="{{ asset('images/pengajaran/kom-everyone.jpg') }}" alt="">
                    <div class="teach-kom__text">
                        <h2>KOM EVERYONE</h2>
                        <p>{!! __('<strong>Kelas Online</strong> dengan materi firman Tuhan yang tersusun secara bertahap untuk membangun dasar iman dan mendukung pertumbuhan rohani Anda.') !!}</p>
                        <a href="#" class="btn-pill btn-blue btn-sm">{{ __('Mulai Belajar') }}</a>
                    </div>
                </div>
            </div>

            {{-- Kategori: kartu bisa digeser ke samping --}}
            @if ($kategori->isNotEmpty())
                <div class="teach-section-head">
                    <h2><a href="{{ route('articles.categories') }}">{{ __('Kategori') }}</a></h2>
                </div>
                <div class="teach-cats" data-drag-scroll aria-label="{{ __('Kategori') }}">
                    @foreach ($kategori as $item)
                        <a href="{{ route('articles.category', $item) }}" class="teach-cat">
                            <div class="teach-cat__img">
                                <img src="{{ $item->sampul?->image_url ?? asset('images/about/berakar.jpg') }}" alt="" loading="lazy">
                                <h3>{{ $item->name }}</h3>
                            </div>
                            <p class="teach-cat__body">{{ $item->deskripsi ?? $item->artikel_count.' '.__('artikel') }}</p>
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Artikel terpopuler: titik di kanan atas untuk berganti artikel --}}
            @if ($terpopuler->isNotEmpty())
                <div class="teach-section-head">
                    <h2>{{ __('Artikel Terpopuler') }}</h2>
                    <a href="{{ route('articles.all', ['urut' => 'populer']) }}" class="btn-pill btn-blue teach-more">{{ __('Lihat Semua') }}</a>
                </div>
                <div class="teach-popular" data-dot-slider>
                    @if ($terpopuler->count() > 1)
                        <div class="dots teach-popular__dots">
                            @foreach ($terpopuler as $item)
                                <button type="button" data-dot="{{ $loop->index }}" @class(['is-active' => $loop->first]) aria-label="{{ __('Artikel') }} {{ $loop->iteration }}"></button>
                            @endforeach
                        </div>
                    @endif
                    @foreach ($terpopuler as $item)
                        <a href="{{ route('articles.show', $item) }}" class="teach-popular__slide @if ($loop->first) is-active @endif" data-dot-slide @unless ($loop->first) hidden @endunless>
                            <img src="{{ $item->image_url ?? asset('images/pages/sejarah.jpg') }}" alt="" @unless ($loop->first) loading="lazy" @endunless>
                            <div class="teach-popular__body">
                                @if ($item->category)<span class="teach-popular__cat">{{ $item->category->name }}</span>@endif
                                <h3>{{ $item->tr('title') }}</h3>
                                <p>{{ $item->ringkasan(240) }}</p>
                                <span class="teach-popular__meta">{{ $item->date_post?->translatedFormat('j F Y') }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
