{{-- Kegiatan (slider kartu). Isi sementara di $events; nanti diambil dari admin. --}}
@php
    $events = [
        ['title' => 'Planetshakers 2026', 'image' => 'images/home/event-1.jpg', 'url' => route('events')],
        ['title' => 'Planetshakers 2025', 'image' => 'images/home/event-2.jpg', 'url' => route('events')],
        ['title' => 'Everyone Asia 2024', 'image' => 'images/home/event-3.jpg', 'url' => route('events')],
    ];
@endphp

<section class="home-events" data-scroller aria-label="{{ __('Kegiatan') }}">
    {{-- Judul ini hanya tampil di HP (sesuai frame mobile Figma). --}}
    <h2 class="home-events__title">{{ __('APA YANG TERJADI DI GATSU?') }}</h2>

    <div class="home-events__track" data-scroll-track>
        @foreach ($events as $event)
            <a href="{{ $event['url'] }}" class="event-card">
                <img src="{{ asset($event['image']) }}" alt="" loading="lazy">
                <h3>{{ $event['title'] }}</h3>
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
