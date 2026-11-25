{{--
    Satu kartu kegiatan: gambar, label, tombol putar, judul, tanggal, tombol.
    $event: tag, button, (title), (date), image_url (URL penuh) atau image (path di public), (url), (play = link tombol putar).
--}}
<article class="event-tile">
    <img src="{{ $event['image_url'] ?? asset($event['image']) }}" alt="{{ $event['title'] ?? '' }}" loading="lazy">
    <span class="tag-pill">{{ $event['tag'] }}</span>
    <a href="{{ $event['play'] ?? $event['url'] ?? '#' }}" class="play-btn" aria-label="{{ __('Putar video') }}">@include('frontend.partials.icon', ['name' => 'play'])</a>
    <div class="event-tile__body">
        @if (! empty($event['title']))
            <h2>{{ $event['title'] }}</h2>
        @endif
        @if (! empty($event['date']))
            <small>{{ $event['date'] }}</small>
        @endif
        <a href="{{ $event['url'] ?? '#' }}" class="btn-pill btn-blue">{{ $event['button'] }}</a>
    </div>
</article>
