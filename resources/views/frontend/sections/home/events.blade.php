{{-- Highlights (slider kartu). Data dari tabel `highlights` (status 1, terbaru dulu), dikirim oleh Frontend\HighlightController@home. --}}
@php $highlights = $highlights ?? collect(); @endphp

@if ($highlights->isNotEmpty())
<section class="home-events" data-scroller aria-label="{{ __('Highlights') }}">
    {{-- Judul ini hanya tampil di HP (sesuai frame mobile Figma). --}}
    <h2 class="home-events__title">{{ __('APA YANG TERJADI DI GATSU?') }}</h2>

    <div class="home-events__track" data-scroll-track>
        @foreach ($highlights as $highlight)
            <a href="{{ route('highlights.show', $highlight) }}" class="event-card">
                <img src="{{ $highlight->image_url ?? asset('images/home/event-1.jpg') }}" alt="{{ $highlight->tr('title') }}" loading="lazy">
                <h3>{{ $highlight->tr('title') }}</h3>
            </a>
        @endforeach
    </div>

    <div class="home-events__nav">
        <button type="button" class="round-btn" data-scroll-prev aria-label="{{ __('Sebelumnya') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
        </button>
        <a href="{{ route('events') }}" class="btn-pill btn-sm">{{ __('Temukan Kisah Kami') }}</a>
        <button type="button" class="round-btn" data-scroll-next aria-label="{{ __('Berikutnya') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </button>
    </div>
</section>
@endif
