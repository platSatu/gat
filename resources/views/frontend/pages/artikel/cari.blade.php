@extends('layouts.frontend')

@section('title', __('Cari Artikel'))

@section('content')
    {{-- Figma "Artikel Pengajran": hasil pencarian kosong + saran. Kata dicari disorot di hasil. --}}
    @php
        $q = request('q', 'doa');
        $highlight = fn (string $text) => preg_replace('/('.preg_quote(e($q), '/').')/iu', '<mark>$1</mark>', e($text));
        $sample = 'Ketika doa belum dijawab, apakah artinya doa kita salah? Apakah Tuhan tidak melihat kita atau kita punya kesalahan sehingga Tuhan tidak menjawab? Mengapa hal ini terjadi.';
    @endphp

    <section class="page page--slate">
        <div class="container page-narrow">
            <h1 class="sr-only">{{ __('Cari Artikel') }}</h1>
            @include('frontend.pages.artikel._search', ['withCategories' => false])

            <div class="card-cream search-empty">
                <div class="search-empty__head">
                    <svg viewBox="0 0 140 110" aria-hidden="true"><circle cx="58" cy="26" r="16"/><path d="M20 110c0-30 14-58 38-58 14 0 24 8 30 18l-14 12c-4-6-9-10-16-10-14 0-20 20-20 38z"/><circle cx="100" cy="38" r="20" fill="none" stroke="#000" stroke-width="7"/><path d="M86 52L70 72" stroke="#000" stroke-width="9" stroke-linecap="round"/></svg>
                    <div>
                        <h2>{{ __('Kami belum menemukan artikel mengenai') }} “{{ $q }}”</h2>
                        <p>{{ __('Coba gunakan kata yang lebih umum atau jelajahi topik berikut') }}</p>
                    </div>
                </div>

                <h3>{{ __('Mungkin yang anda maksud:') }}</h3>
                <div class="chips">@foreach (['Doa', 'Doa puasa', 'Menara doa', 'Pujian'] as $chip)<a href="{{ route('articles.search', ['q' => $chip]) }}">{{ $chip }}</a>@endforeach</div>

                <h3>{{ __('Topik Relevan') }}</h3>
                <div class="chips">@foreach (['Renungan', 'Pengajaran', 'Kehidupan Kristen'] as $chip)<a href="{{ route('articles.category') }}">{{ $chip }}</a>@endforeach</div>

                <h3>{{ __('Artikel Terpopuler') }}</h3>
                <small>{{ __('Ditemukan :count artikel terkait', ['count' => 24]) }}</small>

                <div class="results">
                @foreach (['images/about/berakar.jpg', 'images/pages/sejarah.jpg'] as $image)
                    <a href="{{ route('articles.show') }}" class="result">
                        <img src="{{ asset($image) }}" alt="" loading="lazy">
                        <div>
                            <h4>Mengapa Tuhan belum menjawab doa...</h4>
                            <p>{!! $highlight($sample) !!}</p>
                            <small class="result__meta">Kehidupan Kristen - 31 Juli 2026</small>
                        </div>
                    </a>
                @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
