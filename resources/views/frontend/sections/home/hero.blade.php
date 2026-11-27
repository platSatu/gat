{{--
    Hero beranda (Figma "Homepage"): video latar + kartu sambutan yang bisa digeser.
    Isi kartu dari tabel hero_slides (admin: Hero Beranda), ikut bahasa pengunjung.
    Latar: video/gambar dari admin (tabel pengaturan); kalau kosong pakai public/videos/hero.mp4 / public/images/home/hero.jpg.
    Kalau tabel belum dibuat, dipakai isi lama di bawah ini.
--}}
@php
    $dariDb = \Illuminate\Support\Facades\Schema::hasTable('hero_slides')
        ? \App\Models\HeroSlide::aktif()->urut()->denganTerjemahan()->get()
        : null;

    $slides = $dariDb && $dariDb->isNotEmpty()
        ? $dariDb->map(fn ($s) => [
            'label' => $s->tr('label'), 'title' => $s->tr('title'),
            'sub_label' => $s->tr('sub_label'), 'sub_title' => $s->tr('sub_title'),
            'button' => $s->tombol(), 'url' => $s->url,
        ])->all()
        : [[
            'label' => __('Welcome to'), 'title' => 'GBI Jl. Jend. Gatot Subroto',
            'sub_label' => __('Penggembalaan'), 'sub_title' => 'Pdt. DR. Ir. Niko Njotorahardjo',
            'button' => __('Temukan Kisah Kami'), 'url' => route('about'),
        ]];

    $pakaiDb = $dariDb !== null;
    $videoAdmin = $pakaiDb ? \App\Models\Pengaturan::ambil('hero_video') : null;
    $gambarAdmin = $pakaiDb ? \App\Models\Pengaturan::ambil('hero_gambar') : null;
    $video = $videoAdmin ?: (file_exists(public_path('videos/hero.mp4')) ? 'videos/hero.mp4' : null);
    $gambar = $gambarAdmin ?: 'images/home/hero.jpg';
@endphp

<section class="home-hero" data-hero-scroll>
    @if ($video)
        <video class="home-hero__media" autoplay muted loop playsinline preload="metadata" poster="{{ asset($gambar) }}">
            <source src="{{ asset($video) }}" type="{{ str_ends_with(strtolower($video), '.webm') ? 'video/webm' : 'video/mp4' }}">
        </video>
    @else
        <img class="home-hero__media" src="{{ asset($gambar) }}" alt="">
    @endif

    <span class="home-hero__shade" aria-hidden="true"></span>

    <div class="home-hero__card" data-slider>
        <div class="dots" data-dots></div>

        @foreach ($slides as $slide)
            <div class="home-hero__slide @if ($loop->first) is-active @endif" data-slide>
                <div>
                    @if (filled($slide['label']))<p class="home-hero__label">{{ $slide['label'] }}</p>@endif
                    @if ($loop->first)
                        <h1 class="home-hero__title">{{ $slide['title'] }}</h1>
                    @else
                        <p class="home-hero__title">{{ $slide['title'] }}</p>
                    @endif
                    @if (filled($slide['sub_label']))<p class="home-hero__label">{{ $slide['sub_label'] }}</p>@endif
                    @if (filled($slide['sub_title']))<p class="home-hero__title">{{ $slide['sub_title'] }}</p>@endif
                </div>
                <a href="{{ $slide['url'] }}" class="btn-pill btn-sm">{{ $slide['button'] }}</a>
            </div>
        @endforeach

        <button type="button" class="round-btn home-hero__arrow home-hero__arrow--prev" data-prev aria-label="{{ __('Sebelumnya') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
        </button>
        <button type="button" class="round-btn home-hero__arrow home-hero__arrow--next" data-next aria-label="{{ __('Berikutnya') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </button>
    </div>
</section>
