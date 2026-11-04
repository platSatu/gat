{{-- Satu kartu kegiatan: gambar/video, label, judul, tanggal, tombol. --}}
<article class="event-tile">
    <img src="{{ asset($event['image']) }}" alt="">
    <span class="tag-pill">{{ $event['tag'] }}</span>
    <a href="#" class="play-btn" aria-label="{{ __('Putar video') }}">@include('frontend.partials.icon', ['name' => 'play'])</a>
    <div class="event-tile__body">
        <h2>{{ $event['title'] }}</h2>
        <small>{{ $event['date'] }}</small>
        <a href="{{ $event['url'] ?? '#' }}" class="btn-pill btn-blue">{{ $event['button'] }}</a>
    </div>
</article>
