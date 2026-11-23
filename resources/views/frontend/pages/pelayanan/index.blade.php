@extends('layouts.frontend')

@section('title', __('Pelayanan'))

@section('content')
    {{-- Figma "Pelayanan & Ministry" (revisi): hero lebih pendek, teks & tombol di tengah. Gambar latar tetap yang sudah ada. --}}
    <section class="ministry-hero">
        <img src="{{ asset('images/pages/pelayanan.jpg') }}" alt="">
        <div>
            <h1>{{ __('Pelayanan GBI Gatsu') }}</h1>
            <div class="ministry-hero__card">
                <p>{{ __('Melalui berbagai pelayanan, GBI Jl. Jend. Gatot Subroto hadir untuk melayani, membangun kehidupan, dan menghadirkan kasih Tuhan bagi jemaat serta masyarakat. Temukan pelayanan kami dan lihat bagaimana Anda dapat terhubung, terlibat, atau memperoleh dukungan yang dibutuhkan.') }}</p>
                <a href="#daftar-pelayanan" class="btn-pill btn-sm">{{ __('Jelajahi Pelayanan Kami') }}</a>
            </div>
        </div>
    </section>

    {{--
        Daftar pelayanan (Figma: 2 kartu Graha Bina Asuh & Menara Doa SICC), tampilan etalase ala nivisgear.com:
        judul besar kiri, foto tengah, keterangan kanan, gambar kecil di atas untuk berpindah pelayanan.
        Interaksi di frontend.js "Etalase pelayanan". Gambar sementara dari Figma (public/images/pelayanan).
        TODO: teks Menara Doa SICC masih sementara, ganti dengan teks resmi / ambil dari admin.
    --}}
    @php
        $pelayanan = [
            [
                'nama' => 'Panti Werda - Graha Bina Asuh',
                'slogan' => __('Rumah kedua yang penuh kasih.'),
                'teks' => __('Ketika seseorang yang Anda kasihi membutuhkan tempat untuk merasa dicintai, pulih, dan hidup dengan tenang, Graha Bina Asuh hadir mendampingi orang tua dan anak-anak dengan lingkungan yang hangat dan pendampingan yang tulus.'),
                'gambar' => 'images/pelayanan/graha-bina-asuh.jpg',
                'url' => route('ministry.show'),
            ],
            [
                'nama' => 'Menara Doa SICC',
                'slogan' => __('Doa, pujian, dan penyembahan siang dan malam.'),
                'teks' => __('Menara Doa menjadi tempat jemaat berkumpul untuk berdoa, memuji, dan menyembah Tuhan dalam kesatuan, sebagai bagian dari panggilan Merestorasi Pondok Daud.'),
                'gambar' => 'images/pelayanan/menara-doa-sicc.jpg',
                'url' => null,
            ],
        ];
    @endphp

    <section class="ministry-list" id="daftar-pelayanan">
        <div class="ministry-showcase" data-showcase>
            <div class="ministry-showcase__thumbs" role="tablist" aria-label="{{ __('Pilih pelayanan') }}">
                @foreach ($pelayanan as $item)
                    <button type="button" role="tab" data-showcase-tab="{{ $loop->index }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                        aria-controls="pelayanan-{{ $loop->iteration }}" @class(['is-active' => $loop->first]) title="{{ $item['nama'] }}">
                        <img src="{{ asset($item['gambar']) }}" alt="{{ $item['nama'] }}" loading="lazy">
                    </button>
                @endforeach
            </div>

            @foreach ($pelayanan as $item)
                <article class="ministry-showcase__slide @if ($loop->first) is-active @endif" id="pelayanan-{{ $loop->iteration }}"
                    role="tabpanel" data-showcase-slide @unless ($loop->first) hidden @endunless>
                    <div class="ministry-showcase__lead">
                        <p class="ministry-showcase__slogan">{{ $item['slogan'] }}</p>
                        <p class="ministry-showcase__label"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span> {{ $item['nama'] }}</p>
                    </div>

                    <figure class="ministry-showcase__media">
                        <img src="{{ asset($item['gambar']) }}" alt="{{ $item['nama'] }}" @unless ($loop->first) loading="lazy" @endunless>
                    </figure>

                    <div class="ministry-showcase__info">
                        <div>
                            <h2>{{ $item['nama'] }}</h2>
                            <p>{{ $item['teks'] }}</p>
                        </div>
                        <div class="ministry-showcase__actions">
                            @if ($item['url'])
                                <a href="{{ $item['url'] }}" class="ministry-showcase__btn ministry-showcase__btn--light">{{ __('Jelajahi Pelayanan Kami') }}</a>
                            @endif
                            <button type="button" class="ministry-showcase__btn" data-showcase-next>
                                {{ __('Pelayanan berikutnya') }}
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M7 17L17 7M9 7h8v8"/></svg>
                            </button>
                        </div>
                        <p class="ministry-showcase__count">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} / {{ str_pad($loop->count, 2, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection
