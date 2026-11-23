@extends('layouts.frontend')

@section('title', $judul)

@section('content')
    {{-- Figma "Gatsu News": daftar artikel dalam satu kategori (atau semua artikel). Data dari ArticleController. --}}
    <section class="page page--slate">
        <div class="container">
            <div class="page-narrow">
                @include('frontend.pages.artikel._search')
            </div>

            <h1 class="page-title list-title">{{ $judul }}</h1>

            <div class="news-list">
                @forelse ($artikel as $item)
                    <a href="{{ route('articles.show', $item) }}" class="strip-card">
                        <div class="strip-card__img" style="background-image: url('{{ $item->image_url ?? asset('images/pages/sejarah.jpg') }}')"></div>
                        <div class="strip-card__body">
                            <h3>{{ $item->title }}</h3>
                            <p>{{ $item->ringkasan(220) }}</p>
                            <small class="strip-card__meta">
                                {{ $item->category?->name }}@if ($item->category && $item->date_post) · @endif{{ $item->date_post?->translatedFormat('j F Y') }}
                            </small>
                        </div>
                    </a>
                @empty
                    <p class="list-empty">{{ __('Belum ada artikel di kategori ini.') }}</p>
                @endforelse
            </div>

            @if ($artikel->hasPages())
                <nav class="pager" aria-label="{{ __('Halaman') }}">
                    @if ($artikel->previousPageUrl())
                        <a href="{{ $artikel->previousPageUrl() }}" class="btn-pill btn-blue btn-sm">{{ __('Sebelumnya') }}</a>
                    @endif
                    <span>{{ __('Halaman') }} {{ $artikel->currentPage() }}</span>
                    @if ($artikel->nextPageUrl())
                        <a href="{{ $artikel->nextPageUrl() }}" class="btn-pill btn-blue btn-sm">{{ __('Berikutnya') }}</a>
                    @endif
                </nav>
            @endif
        </div>
    </section>
@endsection
