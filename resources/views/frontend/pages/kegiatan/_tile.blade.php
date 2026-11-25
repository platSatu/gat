{{--
    Satu kartu kegiatan: gambar, label, tombol putar, judul, tanggal, teks, tombol.
    $event: tag, button, (title), (date), (text), image_url (URL penuh) atau image (path di public, boleh null),
            (url), (play = link tombol putar; false = tanpa tombol putar), (class = kelas tambahan).
--}}
@php
    $gambar = $event['image_url'] ?? (! empty($event['image']) ? asset($event['image']) : null);
@endphp
<article class="event-tile {{ $event['class'] ?? '' }}">
    @if ($gambar)
        <img src="{{ $gambar }}" alt="{{ $event['title'] ?? '' }}" loading="lazy">
    @endif
    <span class="tag-pill">{{ $event['tag'] }}</span>
    @if (($event['play'] ?? null) !== false)
        <a href="{{ $event['play'] ?? $event['url'] ?? '#' }}" class="play-btn" aria-label="{{ __('Putar video') }}">@include('frontend.partials.icon', ['name' => 'play'])</a>
    @endif
    <div class="event-tile__body">
        @if (! empty($event['title']))
            <h2>{{ $event['title'] }}</h2>
        @endif
        @if (! empty($event['date']))
            <small>{{ $event['date'] }}</small>
        @endif
        @if (! empty($event['text']))
            <p class="event-tile__text">{{ $event['text'] }}</p>
        @endif
        <a href="{{ $event['url'] ?? '#' }}" class="btn-pill btn-blue">{{ $event['button'] }}</a>
    </div>
</article>
