@extends('layouts.frontend')

@section('title', __('Tentang Gatsu'))

@section('content')
    {{--
        Figma "Desktop - 2" (revisi): bagian foto lebar (16:9) bernuansa biru, teks rata kiri di bawah.
        Isi dari tabel `about_sections` (admin: Tentang Kami), dikirim AboutController@index sebagai $sections.
        Kalau tabel belum dibuat ($sections null), dipakai isi lama di bawah ini.
        'tinted' = gambar sudah bernuansa biru (tidak diwarnai lagi oleh website).
    --}}
    @php
        $blocks = $sections?->map(fn ($s) => [
            'title' => $s->tr('name'), 'subtitle' => $s->tr('subtitle'), 'text' => $s->tr('excerpt'),
            'image_url' => $s->background_url, 'tinted' => ! $s->tint,
            'button' => $s->tombol(), 'url' => $s->url,
        ])->all() ?? [
            [
                'title' => __('Mengenal GBI Gatsu'), 'subtitle' => __('Berawal dari sebuah panggilan'), 'image' => 'images/about/mengenal-figma.jpg', 'tinted' => true,
                'text' => __('GBI Jl. Jend. Gatot Subroto, atau yang lebih dikenal sebagai GBI Gatsu, adalah gereja lokal di bawah naungan Sinode Gereja Bethel Indonesia. Berdiri sejak 4 September 1988 di Jakarta, GBI Gatsu terus bertumbuh menjadi sebuah keluarga besar gereja apostolik yang terdiri dari rayon, cabang khusus, dan gereja-gereja cabang di bawah pembinaannya.'),
                'button' => __('Ikuti Perjalanan GBI Gatsu'), 'url' => route('about.history'),
            ],
            [
                'title' => __('Dipimpin dalam Ketaatan kepada Tuhan'), 'image' => 'images/about/dipimpin.jpg', 'figma' => 'images/about/dipimpin-figma.png',
                'text' => __('GBI Gatsu digembalakan oleh Pdt. Dr. Ir. Niko Njotorahardjo sebagai Gembala Jemaat, Gembala Pembina, sekaligus Bapa Rohani. Perjalanan pelayanannya dimulai dari panggilan Tuhan untuk melayani secara penuh waktu dan membawa umat-Nya masuk ke dalam hadirat Tuhan melalui doa, pujian, dan penyembahan.'),
                'button' => __('Lihat Selengkapnya'), 'url' => route('about.history'),
            ],
            [
                'title' => __('Menghidupi Panggilan Restorasi Pondok Daud'), 'image' => 'images/about/restorasi.jpg', 'figma' => 'images/about/restorasi-figma.png',
                'text' => __('Sejak awal, GBI Gatsu menerima panggilan khusus untuk merestorasi Pondok Daud. Panggilan ini diwujudkan dengan membangun prajurit-prajurit Tuhan yang gagah perkasa, yang memiliki gaya hidup doa, pujian, dan penyembahan dalam unity siang dan malam, serta melakukan kehendak Bapa pada zaman ini.'),
                'button' => __('Pahami Panggilan yang Kami Hidupi'), 'url' => route('about.dna'),
            ],
            [
                'title' => __('Berakar dalam Firman, Melangkah dalam Iman'), 'image' => 'images/about/berakar.jpg', 'figma' => 'images/about/berakar-figma.png',
                'text' => __('Sebagai bagian dari Gereja Bethel Indonesia, GBI Gatsu berpegang pada Alkitab sebagai dasar iman dan kehidupan. Setiap pengajaran, pelayanan, dan langkah gereja diarahkan untuk membawa umat semakin mengenal Kristus, hidup dalam kuasa Roh Kudus, bertumbuh dalam kebenaran firman Tuhan, dan mengerjakan Amanat Agung.'),
                'button' => __('Kenali Dasar Iman Kami'), 'url' => route('about.dna'),
            ],
        ];
        // Isi lama: pakai gambar dari Figma (sudah bernuansa biru) kalau filenya sudah ada di public/images/about.
        foreach ($blocks as &$item) {
            if (isset($item['figma']) && file_exists(public_path($item['figma']))) {
                $item['image'] = $item['figma'];
                $item['tinted'] = true;
            }
        }
        unset($item);
    @endphp

    @foreach ($blocks as $block)
        <section @class(['about-block', 'about-block--tinted' => $block['tinted'] ?? false])>
            @if ($gambar = $block['image_url'] ?? (isset($block['image']) ? asset($block['image']) : null))
                <img class="about-block__img" src="{{ $gambar }}" alt="" @if (! $loop->first) loading="lazy" @endif>
            @endif
            <div class="about-block__content">
                <h2>{{ $block['title'] }}</h2>
                @if (filled($block['subtitle'] ?? null))<h3>{{ $block['subtitle'] }}</h3>@endif
                @if (filled($block['text'] ?? null))<p>{{ $block['text'] }}</p>@endif
                <a href="{{ $block['url'] }}" class="btn-pill about-block__btn">{{ $block['button'] }}</a>
            </div>
        </section>
    @endforeach
@endsection
