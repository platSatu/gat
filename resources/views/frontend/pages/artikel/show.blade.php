@extends('layouts.frontend')

@section('title', $article->metaJudul())
@section('meta_description', $article->metaDeskripsi())

@section('content')
    {{-- Figma "Gatsu News" (detail): judul, gambar + isi, "Baca juga". Data dari ArticleController@show. --}}
    <section class="page page--slate">
        <div class="container">
            <h1 class="page-title">{{ $article->tr('title') }}</h1>
            <p class="article-meta">
                @if ($article->category)
                    <a href="{{ route('articles.category', $article->category) }}">{{ $article->category->name }}</a>
                @endif
                @if ($article->category && $article->date_post) · @endif
                {{ $article->date_post?->translatedFormat('j F Y') }}
            </p>

            <article class="article-body">
                <img class="strip-card__photo" src="{{ $article->image_url ?? asset('images/pages/sejarah.jpg') }}" alt="{{ $article->tr('title') }}">
                <div class="strip-card__body article-content">
                    {!! $article->isi !!}
                </div>
            </article>

            <div class="related">
                @if ($bacaJuga->isNotEmpty())
                    <h2>{{ __('Baca juga:') }}</h2>
                    @foreach ($bacaJuga as $item)
                        <a href="{{ route('articles.show', $item) }}" class="related-card">
                            <img src="{{ $item->image_url ?? asset('images/about/berakar.jpg') }}" alt="" loading="lazy">
                            <div>
                                <h3>{{ $item->tr('title') }}</h3>
                                <p>{{ $item->ringkasan(180) }}</p>
                            </div>
                        </a>
                    @endforeach
                @endif
                <a href="{{ $article->category ? route('articles.category', $article->category) : route('articles.all') }}" class="btn-pill btn-blue btn-sm">{{ __('Lihat Lebih Banyak Artikel') }}</a>
            </div>
        </div>
    </section>
@endsection
