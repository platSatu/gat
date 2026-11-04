{{--
    Hero beranda (Figma "Homepage"): video latar (abu-abu) + kartu sambutan yang bisa digeser.
    Video: taruh file di public/videos/hero.mp4. Selama belum ada, yang tampil gambar public/images/home/hero.jpg.
    Isi kartu sementara di $slides; nanti diambil dari admin.
--}}
@php
    $slides = [
        ['label' => __('Welcome to'), 'title' => 'GBI Jl. Jend. Gatot Subroto', 'sub_label' => __('Penggembalaan'), 'sub_title' => 'Pdt. DR. Ir. Niko Njotorahardjo', 'url' => route('about')],
        ['label' => __('Welcome to'), 'title' => 'GBI Jl. Jend. Gatot Subroto', 'sub_label' => __('Penggembalaan'), 'sub_title' => 'Pdt. DR. Ir. Niko Njotorahardjo', 'url' => route('about')],
        ['label' => __('Welcome to'), 'title' => 'GBI Jl. Jend. Gatot Subroto', 'sub_label' => __('Penggembalaan'), 'sub_title' => 'Pdt. DR. Ir. Niko Njotorahardjo', 'url' => route('about')],
        ['label' => __('Welcome to'), 'title' => 'GBI Jl. Jend. Gatot Subroto', 'sub_label' => __('Penggembalaan'), 'sub_title' => 'Pdt. DR. Ir. Niko Njotorahardjo', 'url' => route('about')],
    ];
    $hasVideo = file_exists(public_path('videos/hero.mp4'));
@endphp

<section class="home-hero" data-hero-scroll>
    @if ($hasVideo)
        <video class="home-hero__media" autoplay muted loop playsinline preload="metadata" poster="{{ asset('images/home/hero.jpg') }}">
            <source src="{{ asset('videos/hero.mp4') }}" type="video/mp4">
        </video>
    @else
        <img class="home-hero__media" src="{{ asset('images/home/hero.jpg') }}" alt="">
    @endif

    <span class="home-hero__shade" aria-hidden="true"></span>

    <div class="home-hero__card" data-slider>
        <div class="dots" data-dots></div>

        @foreach ($slides as $slide)
            <div class="home-hero__slide @if ($loop->first) is-active @endif" data-slide>
                <div>
                    <p class="home-hero__label">{{ $slide['label'] }}</p>
                    <h1 class="home-hero__title">{{ $slide['title'] }}</h1>
                    <p class="home-hero__label">{{ $slide['sub_label'] }}</p>
                    <p class="home-hero__title">{{ $slide['sub_title'] }}</p>
                </div>
                <a href="{{ $slide['url'] }}" class="btn-pill btn-sm">{{ __('Temukan Kisah Kami') }}</a>
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
