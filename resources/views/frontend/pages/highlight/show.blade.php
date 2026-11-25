@extends('layouts.frontend')

@section('title', $highlight->title)
@section('meta_description', \Illuminate\Support\Str::limit(trim(strip_tags(html_entity_decode((string) $highlight->description))), 155))

@section('content')
    {{-- Detail highlight (/media/{id}): judul, video, isi, foto dokumentasi, Data dari HighlightController@show. --}}
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

@endsection
