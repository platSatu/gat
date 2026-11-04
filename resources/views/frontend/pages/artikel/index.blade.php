@extends('layouts.frontend')

@section('title', __('Artikel'))

@section('content')
    {{-- Figma "Artikel": cari, kategori, artikel unggulan, terbaru, terpopuler. --}}
    @php $images = ['images/about/berakar.jpg', 'images/pages/sejarah.jpg', 'images/about/mengenal.jpg']; @endphp

    <section class="page page--slate">
        <div class="container page-narrow">
            <h1 class="sr-only">{{ __('Artikel') }}</h1>
            @include('frontend.pages.artikel._search')

            <div class="featured">
                <div class="dots"><button class="is-active" aria-label="Slide 1"></button><button aria-label="Slide 2"></button><button aria-label="Slide 3"></button><button aria-label="Slide 4"></button></div>
                <a href="{{ route('articles.show') }}"><img src="{{ asset($images[0]) }}" alt=""></a>
            </div>

            @foreach ([__('Artikel Terbaru'), __('Artikel Terpopuler')] as $i => $heading)
                <div class="list-head">
                    <h2>{{ $heading }}</h2>
                    <a href="{{ route('articles.category') }}" class="btn-pill btn-blue btn-sm">{{ __('Lihat Semua') }}</a>
                </div>
                <a href="{{ route('articles.show') }}" class="article-card">
                    <img src="{{ asset($images[$i + 1]) }}" alt="" loading="lazy">
                    <div class="article-card__body">
                        <h3>{{ __('Judul Artikel') }}</h3>
                        <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endsection
