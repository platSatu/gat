@extends('layouts.backend')

@section('title', 'Hero Beranda')
@section('breadcrumb')<li class="breadcrumb-item active">Hero Beranda</li>@endsection

@section('content')
    {{-- Hero beranda: latar (video/gambar) + kartu sambutan yang bisa digeser, urut seperti tampil di website. --}}
    @unless ($siap)
        <div class="alert alert-warning">
            Tabel hero belum ada di database. Jalankan
            <code>php artisan migrate --path=database/migrations/2026_11_27_500000_create_hero_tables.php</code>.
            Sampai saat itu beranda memakai isi lama.
        </div>
    @else
        <div class="row">
            <div class="col-xl-7">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" style="gap: 8px;">
                            <h1 class="admin-heading mb-0">Kartu Hero</h1>
                            <div>
                                <a href="{{ route('admin.hero.create') }}" class="btn btn-primary">Tambah Kartu</a>
                                <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn btn-link">Lihat beranda</a>
                            </div>
                        </div>
                        <p class="text-muted">Kartu sambutan di atas beranda. Lebih dari satu kartu aktif = bisa digeser (titik & panah). Gunakan ▲▼ untuk urutan.</p>

                        <div class="table-responsive">
                            <table class="table table-hover mb-0 admin-table">
                                <thead>
                                    <tr>
                                        <th style="width: 90px">Urutan</th>
                                        <th>Isi kartu</th>
                                        <th>Status</th>
                                        <th style="width: 130px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($slides as $item)
                                        <tr>
                                            <td class="text-nowrap">
                                                <span class="mr-1">{{ $loop->iteration }}</span>
                                                @foreach (['up' => '▲', 'down' => '▼'] as $arah => $ikon)
                                                    <form method="POST" action="{{ route('admin.hero.move', [$item, $arah]) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-light btn-sm px-2" title="{{ $arah === 'up' ? 'Naikkan' : 'Turunkan' }}"
                                                            @disabled(($arah === 'up' && $loop->parent->first) || ($arah === 'down' && $loop->parent->last))>{{ $ikon }}</button>
                                                    </form>
                                                @endforeach
                                            </td>
                                            <td>
                                                <div class="hero-mini">
                                                    @if ($item->label)<small>{{ $item->label }}</small>@endif
                                                    <strong>{{ $item->title }}</strong>
                                                    @if ($item->sub_label || $item->sub_title)
                                                        <small>{{ $item->sub_label }}</small><span>{{ $item->sub_title }}</span>
                                                    @endif
                                                </div>
                                                @if ($item->punya_en)<span class="badge badge-info mt-1" title="Sudah diterjemahkan ke {{ $item->punya_en }} bahasa">{{ $item->punya_en }} bhs</span>@endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $item->status ? 'badge-success' : 'badge-secondary' }}">{{ $item->status ? 'Active' : 'Inactive' }}</span>
                                            </td>
                                            <td class="text-nowrap">
                                                <a href="{{ route('admin.hero.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                                                <form method="POST" action="{{ route('admin.hero.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Hapus kartu hero ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada kartu. Beranda akan memakai isi lama.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="card">
                    <div class="card-body">
                        <h2 class="admin-heading" style="font-size: 22px;">Latar Hero</h2>
                        <p class="text-muted">Video diputar otomatis tanpa suara. Gambar dipakai sebagai cadangan selama video dimuat, atau kalau tidak ada video.</p>

                        @php
                            $videoUrl = $video ? asset($video) : (file_exists(public_path('videos/hero.mp4')) ? asset('videos/hero.mp4') : null);
                            $gambarUrl = $gambar ? asset($gambar) : asset('images/home/hero.jpg');
                        @endphp
                        <div class="hero-preview">
                            @if ($videoUrl)
                                <video src="{{ $videoUrl }}" poster="{{ $gambarUrl }}" muted loop autoplay playsinline></video>
                            @else
                                <img src="{{ $gambarUrl }}" alt="">
                            @endif
                            <span class="hero-preview__tag">{{ $videoUrl ? 'Video' : 'Gambar' }}{{ ($video || $gambar) ? '' : ' (bawaan)' }}</span>
                        </div>

                        <form method="POST" action="{{ route('admin.hero.latar') }}" enctype="multipart/form-data" class="mt-3">
                            @csrf
                            <div class="form-group">
                                <label for="video">Video latar <small class="text-muted">(MP4/WEBM, maks 100 MB)</small></label>
                                <input id="video" type="file" name="video" accept="video/mp4,video/webm" @class(['form-control', 'is-invalid' => $errors->has('video')])>
                                @error('video')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @if ($video)
                                    <div class="custom-control custom-checkbox mt-1">
                                        <input type="checkbox" class="custom-control-input" id="hapus_video" name="hapus_video" value="1">
                                        <label class="custom-control-label" for="hapus_video">Hapus video (pakai bawaan/gambar)</label>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="gambar">Gambar latar <small class="text-muted">(JPG/PNG/WEBP, maks 5 MB, lebar ≥ 1920px)</small></label>
                                <input id="gambar" type="file" name="gambar" accept="image/jpeg,image/png,image/webp" @class(['form-control', 'is-invalid' => $errors->has('gambar')])>
                                @error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @if ($gambar)
                                    <div class="custom-control custom-checkbox mt-1">
                                        <input type="checkbox" class="custom-control-input" id="hapus_gambar" name="hapus_gambar" value="1">
                                        <label class="custom-control-label" for="hapus_gambar">Hapus gambar (pakai bawaan)</label>
                                    </div>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan Latar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Layar pembuka (loading screen) --}}
        @php
            $bahasaPembuka = \App\Models\Bahasa::daftar();
            $bawaanPembuka = \App\Support\Situs::PEMBUKA_BAWAAN;
        @endphp
        <div class="card" id="layar-pembuka">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-start mb-2" style="gap: 8px;">
                    <div>
                        <h2 class="admin-heading mb-1" style="font-size: 22px;">Layar Pembuka</h2>
                        <p class="text-muted mb-0">Layar biru dengan tulisan "GBI Gatsu" yang muncul saat website pertama kali dibuka. Isian kosong = tampilan bawaan seperti semula.</p>
                    </div>
                    <span @class(['badge', 'badge-success' => $pembuka['aktif'], 'badge-secondary' => ! $pembuka['aktif']])>{{ $pembuka['aktif'] ? 'Aktif' : 'Nonaktif' }}</span>
                </div>

                <form method="POST" action="{{ route('admin.hero.pembuka') }}" enctype="multipart/form-data" class="mt-3" id="form-pembuka">
                    @csrf
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="custom-control custom-checkbox mb-3">
                                <input type="checkbox" class="custom-control-input" id="pembuka_aktif" name="pembuka_aktif" value="1" @checked(old('pembuka_aktif', $pembuka['aktif']))>
                                <label class="custom-control-label" for="pembuka_aktif">Tampilkan layar pembuka</label>
                            </div>

                            <label class="d-block">Tulisan</label>
                            <div class="row">
                                @foreach ($bahasaPembuka as $b)
                                    @php $k = 'pembuka_teks_'.$b['kode']; @endphp
                                    <div class="col-sm-6 form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text" title="{{ $b['nama'] }}">{{ strtoupper($b['kode']) }}</span></div>
                                            <input type="text" name="{{ $k }}" value="{{ old($k, $pembuka['teks'][$b['kode']]) }}" maxlength="40"
                                                placeholder="{{ $b['utama'] ? $bawaanPembuka['teks'] : 'Kosong = sama dengan Indonesia' }}"
                                                @class(['form-control', 'is-invalid' => $errors->has($k)]) @if ($b['utama']) data-pv-teks @endif>
                                        </div>
                                        @error($k)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                @endforeach
                            </div>

                            <div class="form-group">
                                <label for="pembuka_logo">Atau pakai logo <small class="text-muted">(menggantikan tulisan; PNG/WEBP transparan, maks 2 MB)</small></label>
                                <input id="pembuka_logo" type="file" name="pembuka_logo" accept="image/png,image/webp,image/jpeg" @class(['form-control', 'is-invalid' => $errors->has('pembuka_logo')])>
                                @error('pembuka_logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @if ($pembuka['logo'])
                                    <div class="custom-control custom-checkbox mt-1">
                                        <input type="checkbox" class="custom-control-input" id="hapus_pembuka_logo" name="hapus_pembuka_logo" value="1">
                                        <label class="custom-control-label" for="hapus_pembuka_logo">Hapus logo (kembali pakai tulisan)</label>
                                    </div>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-sm-4 form-group">
                                    <label for="pembuka_warna_latar">Warna latar</label>
                                    <div class="d-flex" style="gap: 6px;">
                                        <input type="color" id="pembuka_warna_latar" name="pembuka_warna_latar" value="{{ old('pembuka_warna_latar', $pembuka['latar']) }}" class="pv-color" data-pv-latar>
                                        <button type="button" class="btn btn-light btn-sm" data-pv-reset="pembuka_warna_latar" data-nilai="{{ $bawaanPembuka['latar'] }}" title="Kembalikan warna bawaan">Bawaan</button>
                                    </div>
                                    @error('pembuka_warna_latar')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-sm-4 form-group">
                                    <label for="pembuka_warna_teks">Warna tulisan</label>
                                    <div class="d-flex" style="gap: 6px;">
                                        <input type="color" id="pembuka_warna_teks" name="pembuka_warna_teks" value="{{ old('pembuka_warna_teks', $pembuka['warna_teks']) }}" class="pv-color" data-pv-warna>
                                        <button type="button" class="btn btn-light btn-sm" data-pv-reset="pembuka_warna_teks" data-nilai="{{ $bawaanPembuka['warna_teks'] }}" title="Kembalikan warna bawaan">Bawaan</button>
                                    </div>
                                    @error('pembuka_warna_teks')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-sm-4 form-group">
                                    <label for="pembuka_diam">Tulisan diam dulu</label>
                                    <div class="input-group">
                                        <input type="number" id="pembuka_diam" name="pembuka_diam" min="0" max="5" step="0.1" value="{{ old('pembuka_diam', $pembuka['diam']) }}"
                                            @class(['form-control', 'is-invalid' => $errors->has('pembuka_diam')]) data-pv-diam>
                                        <div class="input-group-append"><span class="input-group-text">detik</span></div>
                                    </div>
                                    @error('pembuka_diam')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Kapan muncul</label>
                                @foreach (\App\Support\Situs::PEMBUKA_MUNCUL as $nilai => $label)
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" id="muncul_{{ $nilai }}" name="pembuka_muncul" value="{{ $nilai }}" @checked(old('pembuka_muncul', $pembuka['muncul']) === $nilai)>
                                        <label class="custom-control-label" for="muncul_{{ $nilai }}">{{ $label }}@if ($nilai === $bawaanPembuka['muncul']) <small class="text-muted">(bawaan)</small>@endif</label>
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan Layar Pembuka</button>
                        </div>

                        <div class="col-lg-5">
                            <label class="d-flex justify-content-between align-items-center">
                                <span>Pratinjau</span>
                                <button type="button" class="btn btn-light btn-sm" data-pv-putar><i class="mdi mdi-replay"></i> Putar ulang</button>
                            </label>
                            <div class="pv-loader" data-pv>
                                <div class="pv-loader__page">Halaman website</div>
                                <span class="pv-loader__circle"></span>
                                <span class="pv-loader__mask">
                                    <img class="pv-loader__logo" alt="" @if ($pembuka['logo']) src="{{ asset($pembuka['logo']) }}" @else hidden @endif data-pv-logo>
                                    <span class="pv-loader__text" @if ($pembuka['logo']) hidden @endif data-pv-tulisan>{{ $pembuka['teks'][$bahasaPembuka->first()['kode']] ?: $bawaanPembuka['teks'] }}</span>
                                </span>
                            </div>
                            <small class="text-muted d-block mt-2">Pratinjau diperkecil. Di website tampil memenuhi layar, total ±{{ number_format((float) $pembuka['diam'] + 3, 1, ',', '') }} detik.</small>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endunless
@endsection

@push('styles')
    <style>
        .hero-mini { display: flex; flex-direction: column; line-height: 1.3; }
        .hero-mini small { color: #7a8194; font-size: 11px; text-transform: uppercase; letter-spacing: .04em; }
        .hero-mini strong { font-size: 15px; }
        .hero-mini small + span { font-weight: 600; }
        .hero-preview { position: relative; aspect-ratio: 16 / 9; overflow: hidden; border-radius: 10px; background: #1f2433; }
        .hero-preview video, .hero-preview img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .hero-preview__tag { position: absolute; left: 10px; top: 10px; padding: 2px 10px; border-radius: 999px; background: rgba(0,0,0,.55); color: #fff; font-size: 12px; }

        /* Pratinjau layar pembuka (tiruan kecil dari .site-loader di frontend.css) */
        .pv-color { width: 52px; height: 38px; padding: 2px; border: 1px solid #dee2e6; border-radius: 6px; background: #fff; cursor: pointer; }
        .pv-loader { position: relative; display: grid; place-items: center; aspect-ratio: 16 / 9; overflow: hidden; border-radius: 10px; background: #f4f5f8; border: 1px solid #e9ebf2; }
        .pv-loader__page { position: absolute; inset: 0; display: grid; place-items: center; color: #9aa0b2; font-size: 13px; }
        .pv-loader__circle { position: absolute; top: 50%; left: 50%; width: 250%; aspect-ratio: 1; border-radius: 50%; background: var(--pv-bg, #466883);
            transform: translate(-50%, -50%) scale(1); transition: transform .8s ease 2s; }
        .pv-loader__mask { position: relative; display: block; overflow: hidden; padding: .05em .1em; }
        .pv-loader__text { display: block; color: var(--pv-fg, #ece3d4); font-size: clamp(1.6rem, 3.2vw, 2.6rem); font-weight: 700; line-height: 1.15; font-family: Inter, system-ui, sans-serif;
            transition: transform 1.9s cubic-bezier(.77, 0, .175, 1); }
        .pv-loader__logo { display: block; height: clamp(2rem, 4vw, 3.4rem); width: auto; max-width: 80%; object-fit: contain; transition: transform 1.9s cubic-bezier(.77, 0, .175, 1); }
        .pv-loader.is-done .pv-loader__text, .pv-loader.is-done .pv-loader__logo { transform: translateX(-100%); }
        .pv-loader.is-done .pv-loader__circle { transform: translate(-50%, -50%) scale(0); }
        .pv-loader.is-reset .pv-loader__text, .pv-loader.is-reset .pv-loader__logo, .pv-loader.is-reset .pv-loader__circle { transition: none; }
        .pv-loader.is-off { opacity: .35; }
        .pv-loader [hidden] { display: none !important; }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            var pv = document.querySelector('[data-pv]');
            if (! pv) { return; }
            var q = function (s) { return document.querySelector(s); };
            var teks = q('[data-pv-teks]'), latar = q('[data-pv-latar]'), warna = q('[data-pv-warna]'), diam = q('[data-pv-diam]');
            var tulisan = q('[data-pv-tulisan]'), logo = q('[data-pv-logo]'), fileLogo = q('#pembuka_logo'), aktif = q('#pembuka_aktif');
            var bawaan = @json($bawaanPembuka['teks']), timer = [];

            function terapkan() {
                pv.style.setProperty('--pv-bg', latar.value);
                pv.style.setProperty('--pv-fg', warna.value);
                tulisan.textContent = (teks && teks.value.trim()) || bawaan;
                pv.classList.toggle('is-off', ! aktif.checked);
            }
            function putar() {
                timer.forEach(clearTimeout); timer = [];
                pv.classList.add('is-reset'); pv.classList.remove('is-done');
                void pv.offsetWidth;   // mulai ulang animasi
                pv.classList.remove('is-reset');
                var tahan = Math.max(0, parseFloat(diam.value || '0.9')) * 1000;
                timer.push(setTimeout(function () { pv.classList.add('is-done'); }, tahan));
                timer.push(setTimeout(putar, tahan + 4500));   // ulangi terus supaya mudah dilihat
            }

            [teks, latar, warna, diam, aktif].forEach(function (el) { if (el) { el.addEventListener('input', terapkan); el.addEventListener('change', terapkan); } });
            document.querySelectorAll('[data-pv-reset]').forEach(function (b) {
                b.addEventListener('click', function () { var i = document.getElementById(b.getAttribute('data-pv-reset')); i.value = b.getAttribute('data-nilai'); terapkan(); });
            });
            fileLogo.addEventListener('change', function () {
                if (! fileLogo.files[0]) { return; }
                logo.src = URL.createObjectURL(fileLogo.files[0]); logo.hidden = false; tulisan.hidden = true; putar();
            });
            var hapus = q('#hapus_pembuka_logo');
            if (hapus) { hapus.addEventListener('change', function () { logo.hidden = hapus.checked; tulisan.hidden = ! hapus.checked; }); }
            q('[data-pv-putar]').addEventListener('click', putar);

            terapkan(); putar();
        })();
    </script>
@endpush

@include('backend.partials.crud-styles')
