@extends('layouts.frontend')

@section('title', $highlight->title)
@section('meta_description', \Illuminate\Support\Str::limit(trim(strip_tags(html_entity_decode((string) $highlight->description))), 155))

@section('content')
    {{-- Detail highlight (/media/{id}): judul, video, isi, foto dokumentasi, highlight lain. Data dari HighlightController@show. --}}
    <section class="page page--slate">
        <div class="container">
            <h1 class="page-title">{{ $highlight->title }}</h1>
            @if ($highlight->date_post)
                <p class="article-meta">{{ $highlight->date_post->translatedFormat('j F Y') }}</p>
            @endif

            <article class="article-body">
                @if ($highlight->video_url)
                    <video class="highlight-video" controls playsinline preload="metadata" poster="{{ $highlight->banner_url }}">
                        <source src="{{ $highlight->video_url }}" type="video/mp4">
                    </video>
                @elseif ($highlight->banner_url)
                    <img class="highlight-video" src="{{ $highlight->banner_url }}" alt="{{ $highlight->title }}">
                @endif

                @if (filled(strip_tags((string) $highlight->description)))
                    <div class="strip-card__body article-content highlight-body">
                        {!! $highlight->isi !!}
                    </div>
                @endif
            </article>

            @if ($highlight->foto)
                <div class="highlight-photos">
                    <h2>{{ __('Dokumentasi') }}</h2>
                    <div class="gallery-grid">
                        @foreach ($highlight->foto as $foto)
                            <a href="{{ $foto }}" target="_blank" rel="noopener"><img src="{{ $foto }}" alt="{{ $highlight->title }}" loading="lazy"></a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    @if ($lainnya->isNotEmpty())
        <section class="home-events" data-scroller aria-label="{{ __('Highlights lainnya') }}">
            <h2 class="home-events__title highlight-more__title">{{ __('Highlights Lainnya') }}</h2>
            <div class="home-events__track" data-scroll-track>
                @foreach ($lainnya as $item)
                    <a href="{{ route('highlights.show', $item) }}" class="event-card">
                        <img src="{{ $item->image_url ?? asset('images/home/event-1.jpg') }}" alt="{{ $item->title }}" loading="lazy">
                        <h3>{{ $item->title }}</h3>
                    </a>
                @endforeach
            </div>
            <div class="home-events__nav">
                <button type="button" class="round-btn" data-scroll-prev aria-label="{{ __('Sebelumnya') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
                </button>
                <a href="{{ route('home') }}" class="btn-pill btn-sm">{{ __('Kembali ke Beranda') }}</a>
                <button type="button" class="round-btn" data-scroll-next aria-label="{{ __('Berikutnya') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </button>
            </div>
        </section>
    @endif
@endsection
