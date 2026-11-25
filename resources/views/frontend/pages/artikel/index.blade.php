@extends('layouts.frontend')

@section('title', __('Artikel'))

@section('content')
    {{-- Figma "Artikel": cari, kategori, artikel unggulan, terbaru, terpopuler. Data dari ArticleController@index. --}}
    <section class="page page--slate">
        <div class="container page-narrow">
            <h1 class="sr-only">{{ __('Artikel') }}</h1>
            @include('frontend.pages.artikel._search')

            @if ($unggulan)
                <div class="featured">
                    <div class="dots"><button class="is-active" aria-label="Slide 1"></button><button aria-label="Slide 2"></button><button aria-label="Slide 3"></button><button aria-label="Slide 4"></button></div>
                    <a href="{{ route('articles.show', $unggulan) }}" title="{{ $unggulan->tr('title') }}">
                        <img src="{{ $unggulan->image_url ?? asset('images/about/berakar.jpg') }}" alt="{{ $unggulan->tr('title') }}">
                    </a>
                </div>
            @endif

            @foreach ([
                [__('Artikel Terbaru'), $terbaru, route('articles.all')],
                [__('Artikel Terpopuler'), $terpopuler, route('articles.all', ['urut' => 'populer'])],
            ] as [$heading, $item, $semua])
                @continue(! $item)
                <div class="list-head">
                    <h2>{{ $heading }}</h2>
                    <a href="{{ $semua }}" class="btn-pill btn-blue btn-sm">{{ __('Lihat Semua') }}</a>
                </div>
                <a href="{{ route('articles.show', $item) }}" class="article-card">
                    <img src="{{ $item->image_url ?? asset('images/pages/sejarah.jpg') }}" alt="" loading="lazy">
                    <div class="article-card__body">
                        <h3>{{ $item->tr('title') }}</h3>
                        <p>{{ $item->ringkasan() }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endsection
