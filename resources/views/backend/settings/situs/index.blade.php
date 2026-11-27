@extends('layouts.backend')

@section('title', 'Situs & SEO')
@section('breadcrumb')<li class="breadcrumb-item">Settings</li><li class="breadcrumb-item active">Situs &amp; SEO</li>@endsection

@php
    $v = fn ($k) => old($k, $nilai[$k] ?? null);
    $aktifTab = collect($bahasa)->first(fn ($b) => $errors->has('seo_judul_'.$b['kode']) || $errors->has('seo_deskripsi_'.$b['kode']))['kode'] ?? $bahasa->first()['kode'];
    $err = fn ($k) => $errors->first($k);
@endphp

@section('content')
    @include('backend.settings._layout', [
        'judul' => 'Situs & SEO',
        'keterangan' => 'Judul & deskripsi di Google, gambar saat link dibagikan, favicon, Google Tag Manager / Analytics / Search Console, dan link media sosial.',
    ])

    @unless ($siap)
        <div class="alert alert-warning">
            Tabel <code>pengaturan</code> belum ada. Jalankan
            <code>php artisan migrate --path=database/migrations/2026_11_27_500000_create_hero_tables.php</code> (tabel ini dibuat bersama Hero).
        </div>
    @else
        @unless ($diDomainUtama)
            <div class="alert alert-info">
                <i class="mdi mdi-information-outline"></i>
                Anda membuka admin dari <strong>{{ request()->getHost() }}</strong>. Pengaturan ini dipakai bersama, tetapi di selain
                <strong>{{ $domainUtama }}</strong> halaman otomatis <em>noindex</em> dan skrip Google/Meta tidak dimuat (supaya demo tidak masuk Google dan data analytics tidak tercampur).
            </div>
        @endunless

        <form method="POST" action="{{ route('admin.settings.situs.simpan') }}" enctype="multipart/form-data" class="set-form" id="form-situs">
            @csrf

            <div class="row">
                <div class="col-xl-7">
                    {{-- 1. SEO --}}
                    <section class="set-card">
                        <div class="set-card__head"><h2><i class="mdi mdi-google"></i> Tampilan di Google</h2></div>
                        <div class="set-card__body">
                            <div class="form-group">
                                <label for="situs_nama">Nama website</label>
                                <input id="situs_nama" type="text" name="situs_nama" value="{{ $v('situs_nama') }}" maxlength="100" placeholder="{{ config('app.name') }}"
                                    @class(['form-control', 'is-invalid' => $err('situs_nama')]) data-pratinjau="nama">
                                @error('situs_nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="form-text text-muted">Dipakai di akhir judul setiap halaman, mis. "Artikel | <span data-isi="nama">{{ $v('situs_nama') ?: config('app.name') }}</span>".</small>
                            </div>

                            <ul class="nav nav-tabs lang-tabs" role="tablist">
                                @foreach ($bahasa as $b)
                                    <li class="nav-item">
                                        <a @class(['nav-link', 'active' => $aktifTab === $b['kode']]) data-toggle="tab" href="#seo-{{ $b['kode'] }}" role="tab" data-bahasa="{{ $b['kode'] }}">
                                            {{ $b['nama'] }} @if ($b['utama'])<small class="text-muted">(utama)</small>@endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content lang-tab-content">
                                @foreach ($bahasa as $b)
                                    @php $kJudul = 'seo_judul_'.$b['kode']; $kDesk = 'seo_deskripsi_'.$b['kode']; @endphp
                                    <div @class(['tab-pane', 'fade', 'show active' => $aktifTab === $b['kode']]) id="seo-{{ $b['kode'] }}" role="tabpanel">
                                        <div class="form-group">
                                            <label for="{{ $kJudul }}">Judul beranda</label>
                                            <input id="{{ $kJudul }}" type="text" name="{{ $kJudul }}" value="{{ $v($kJudul) }}" maxlength="120"
                                                @class(['form-control', 'is-invalid' => $err($kJudul)]) data-hitung="60" data-pratinjau="judul" data-bahasa="{{ $b['kode'] }}"
                                                placeholder="{{ $b['utama'] ? 'mis. GBI Gatsu – Gereja Bethel Indonesia Jl. Gatot Subroto' : 'Kosong = pakai versi Indonesia' }}">
                                            @error($kJudul)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            <small class="form-text text-muted">Judul tab browser & judul biru di Google untuk beranda. Ideal ≤ 60 karakter · <span data-hitung-untuk="{{ $kJudul }}"></span></small>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label for="{{ $kDesk }}">Meta deskripsi</label>
                                            <textarea id="{{ $kDesk }}" name="{{ $kDesk }}" rows="3" maxlength="320"
                                                @class(['form-control', 'is-invalid' => $err($kDesk)]) data-hitung="160" data-pratinjau="deskripsi" data-bahasa="{{ $b['kode'] }}"
                                                placeholder="{{ $b['utama'] ? 'Ringkasan website 1–2 kalimat, muncul di bawah judul di hasil Google.' : 'Kosong = pakai versi Indonesia' }}">{{ $v($kDesk) }}</textarea>
                                            @error($kDesk)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            <small class="form-text text-muted">Dipakai di halaman yang tidak punya deskripsi sendiri. Ideal 120–160 karakter · <span data-hitung-untuk="{{ $kDesk }}"></span></small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="form-group mb-0">
                                <label for="seo_kata_kunci">Kata kunci <small class="text-muted">(opsional)</small></label>
                                <input id="seo_kata_kunci" type="text" name="seo_kata_kunci" value="{{ $v('seo_kata_kunci') }}" maxlength="255" class="form-control"
                                    placeholder="mis. gereja jakarta, GBI Gatsu, ibadah minggu">
                                <small class="form-text text-muted">Dipisah koma. Google hampir tidak memakainya lagi, tapi mesin pencari lain masih.</small>
                            </div>
                        </div>
                    </section>

                    {{-- 2. Integrasi --}}
                    <section class="set-card">
                        <div class="set-card__head"><h2><i class="mdi mdi-code-tags"></i> Google &amp; integrasi</h2></div>
                        <div class="set-card__body">
                            @php
                                $integrasi = [
                                    ['gtm_id', 'Google Tag Manager', 'GTM-XXXXXXX', 'Container ID dari tagmanager.google.com. Kalau diisi, Google Analytics sebaiknya dipasang lewat GTM.'],
                                    ['ga4_id', 'Google Analytics 4', 'G-XXXXXXXXXX', 'Measurement ID (Admin → Data streams). Hanya dipakai kalau GTM kosong.'],
                                    ['gsc_kode', 'Google Search Console', 'tempel kode atau tag <meta> verifikasi', 'Search Console → Tambah properti → Awalan URL → metode "Tag HTML". Boleh tempel tag lengkapnya.'],
                                    ['bing_kode', 'Bing Webmaster', 'tempel kode atau tag <meta> verifikasi', 'Opsional. Bing Webmaster Tools → metode "Meta tag".'],
                                    ['meta_pixel_id', 'Meta (Facebook) Pixel', 'angka Pixel ID', 'Opsional, untuk iklan Facebook/Instagram.'],
                                ];
                            @endphp
                            @foreach ($integrasi as [$k, $label, $contoh, $bantuan])
                                <div class="form-group @if ($loop->last) mb-0 @endif">
                                    <label for="{{ $k }}">{{ $label }}
                                        @if (filled($nilai[$k] ?? null))<span class="set-chip set-chip--green" style="font-size: 11px;"><i class="mdi mdi-check-circle"></i> Terpasang</span>@endif
                                    </label>
                                    <input id="{{ $k }}" type="text" name="{{ $k }}" value="{{ $v($k) }}" placeholder="{{ $contoh }}"
                                        @class(['form-control', 'set-mono', 'is-invalid' => $err($k)])>
                                    @error($k)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="form-text text-muted">{{ $bantuan }}</small>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    {{-- 3. Media sosial --}}
                    <section class="set-card">
                        <div class="set-card__head"><h2><i class="mdi mdi-share-variant"></i> Link media sosial</h2></div>
                        <div class="set-card__body">
                            <p class="text-muted small">Tampil di menu website. Yang kosong tidak ditampilkan.</p>
                            <div class="row">
                                @foreach ([['sosial_facebook', 'Facebook', 'mdi-facebook', 'https://www.facebook.com/...'], ['sosial_instagram', 'Instagram', 'mdi-instagram', 'https://www.instagram.com/...'], ['sosial_youtube', 'YouTube', 'mdi-youtube-play', 'https://www.youtube.com/@...'], ['sosial_tiktok', 'TikTok', 'mdi-music-note', 'https://www.tiktok.com/@...'], ['sosial_whatsapp', 'WhatsApp', 'mdi-whatsapp', '0812xxxx atau https://wa.me/...']] as [$k, $label, $ikon, $contoh])
                                    <div class="col-md-6 form-group">
                                        <label for="{{ $k }}"><i class="mdi {{ $ikon }}"></i> {{ $label }}</label>
                                        <input id="{{ $k }}" type="text" name="{{ $k }}" value="{{ $v($k) }}" placeholder="{{ $contoh }}" maxlength="255"
                                            @class(['form-control', 'is-invalid' => $err($k)])>
                                        @error($k)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>

                    {{-- 4. Lanjutan --}}
                    <section class="set-card">
                        <div class="set-card__head"><h2><i class="mdi mdi-settings"></i> Lanjutan</h2></div>
                        <div class="set-card__body">
                            <div class="custom-control custom-checkbox mb-1">
                                <input type="checkbox" class="custom-control-input" id="noindex" name="noindex" value="1" @checked($v('noindex') === '1' || old('noindex') === '1')>
                                <label class="custom-control-label" for="noindex"><i class="mdi mdi-eye-off"></i> Sembunyikan website dari mesin pencari (noindex)</label>
                            </div>
                            <small class="form-text text-muted mb-3">Biarkan tidak dicentang untuk website resmi. Domain selain {{ $domainUtama }} (demo, localhost) sudah otomatis tersembunyi.</small>

                            <div class="form-group mb-0">
                                <label for="kode_head">Kode tambahan di &lt;head&gt; <small class="text-muted">(opsional)</small></label>
                                <textarea id="kode_head" name="kode_head" rows="4" maxlength="10000" @class(['form-control', 'set-mono', 'is-invalid' => $err('kode_head')])
                                    placeholder="<!-- mis. skrip chat, verifikasi layanan lain -->">{{ $v('kode_head') }}</textarea>
                                @error('kode_head')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="form-text text-muted"><i class="mdi mdi-alert-outline"></i> Kode ini dijalankan di semua halaman website. Hanya tempel kode dari layanan yang Anda percaya.</small>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-xl-5">
                    {{-- Pratinjau Google --}}
                    <section class="set-card set-sticky">
                        <div class="set-card__head"><h2>Pratinjau di Google</h2></div>
                        <div class="set-card__body">
                            <div class="g-preview">
                                <div class="g-preview__site">
                                    <img src="{{ \App\Support\Situs::favicon() }}" alt="">
                                    <div><div data-isi="nama">{{ $v('situs_nama') ?: config('app.name') }}</div><small>https://{{ $domainUtama }}</small></div>
                                </div>
                                <div class="g-preview__title" data-isi="judul"></div>
                                <div class="g-preview__desc" data-isi="deskripsi"></div>
                            </div>
                            <small class="text-muted d-block mt-2">Bahasa pratinjau mengikuti tab yang sedang dibuka.</small>
                        </div>

                        <div class="set-card__head" style="border-top: 1px solid var(--set-line);"><h2>Saat link dibagikan</h2></div>
                        <div class="set-card__body">
                            <div class="share-preview">
                                <div class="share-preview__img" data-og-preview>
                                    @if ($nilai['og_gambar'] ?? null)
                                        <img src="{{ asset($nilai['og_gambar']) }}" alt="">
                                    @else
                                        <span><i class="mdi mdi-image"></i> Belum ada gambar share</span>
                                    @endif
                                </div>
                                <div class="share-preview__body">
                                    <small>{{ strtoupper($domainUtama) }}</small>
                                    <strong data-isi="judul"></strong>
                                    <span data-isi="deskripsi"></span>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label for="og_gambar">Gambar share (OG image)</label>
                                <input id="og_gambar" type="file" name="og_gambar" accept="image/jpeg,image/png,image/webp" @class(['form-control', 'is-invalid' => $err('og_gambar')])>
                                @error('og_gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="form-text text-muted">Ukuran ideal 1200 × 630 px, JPG/PNG/WEBP maks 3 MB. Dipakai di halaman yang tidak punya gambar sendiri.</small>
                                @if ($nilai['og_gambar'] ?? null)
                                    <div class="custom-control custom-checkbox mt-1">
                                        <input type="checkbox" class="custom-control-input" id="hapus_og_gambar" name="hapus_og_gambar" value="1">
                                        <label class="custom-control-label" for="hapus_og_gambar">Hapus gambar share</label>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="twitter_akun">Akun X / Twitter <small class="text-muted">(opsional)</small></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">@</span></div>
                                    <input id="twitter_akun" type="text" name="twitter_akun" value="{{ $v('twitter_akun') }}" maxlength="30" @class(['form-control', 'is-invalid' => $err('twitter_akun')])>
                                    @error('twitter_akun')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <label for="favicon">Favicon (ikon di tab browser)</label>
                                <div class="d-flex align-items-center" style="gap: 12px;">
                                    <img src="{{ \App\Support\Situs::favicon() }}" alt="" class="favicon-now">
                                    <input id="favicon" type="file" name="favicon" accept="image/png,image/x-icon,image/vnd.microsoft.icon,image/jpeg,image/webp" @class(['form-control', 'is-invalid' => $err('favicon')])>
                                </div>
                                @error('favicon')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                <small class="form-text text-muted">PNG persegi (mis. 192 × 192 px) atau ICO, maks 1 MB.</small>
                                @if ($nilai['favicon'] ?? null)
                                    <div class="custom-control custom-checkbox mt-1">
                                        <input type="checkbox" class="custom-control-input" id="hapus_favicon" name="hapus_favicon" value="1">
                                        <label class="custom-control-label" for="hapus_favicon">Kembalikan ke favicon bawaan</label>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <div class="situs-savebar">
                <span class="text-muted small">Perubahan berlaku di website setelah disimpan.</span>
                <button type="submit" class="btn set-btn set-btn--primary"><i class="mdi mdi-content-save"></i> Simpan pengaturan</button>
            </div>
        </form>
    @endunless
@endsection

@push('styles')
    <style>
        .set-card__head h2 i { margin-right: 6px; color: var(--set-primary); }
        .set-mono { font-family: SFMono-Regular, Consolas, monospace; font-size: 13px; }
        .lang-tabs { margin-bottom: 0; }
        .lang-tab-content { padding: 18px; margin-bottom: 18px; border: 1px solid #dee2e6; border-top: 0; border-radius: 0 0 8px 8px; }
        .set-sticky { position: sticky; top: 90px; }
        .g-preview { padding: 16px; border: 1px solid var(--set-line); border-radius: 12px; font-family: Arial, sans-serif; }
        .g-preview__site { display: flex; align-items: center; gap: 10px; font-size: 14px; color: #202124; }
        .g-preview__site img { width: 26px; height: 26px; padding: 3px; border-radius: 50%; background: #f1f3f4; object-fit: contain; }
        .g-preview__site small { color: #4d5156; font-size: 12px; }
        .g-preview__title { margin: 8px 0 4px; color: #1a0dab; font-size: 20px; line-height: 1.3; }
        .g-preview__desc { color: #4d5156; font-size: 14px; line-height: 1.58; }
        .share-preview { overflow: hidden; border: 1px solid var(--set-line); border-radius: 12px; background: #f0f2f5; }
        .share-preview__img { display: grid; place-items: center; aspect-ratio: 1200 / 630; background: #e4e6eb; color: #8a8d91; }
        .share-preview__img img { width: 100%; height: 100%; object-fit: cover; }
        .share-preview__body { display: flex; flex-direction: column; gap: 2px; padding: 10px 12px; }
        .share-preview__body small { color: #65676b; font-size: 12px; }
        .share-preview__body strong { color: #050505; font-size: 15px; line-height: 1.3; }
        .share-preview__body span { color: #65676b; font-size: 13px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .favicon-now { flex: 0 0 40px; width: 40px; height: 40px; padding: 6px; border: 1px solid var(--set-line); border-radius: 8px; object-fit: contain; }
        .situs-savebar { position: sticky; bottom: 0; z-index: 5; display: flex; justify-content: flex-end; align-items: center; gap: 14px; padding: 12px 20px; margin-bottom: 20px; background: rgba(255,255,255,.96); border: 1px solid var(--set-line); border-radius: 14px; box-shadow: 0 -4px 16px rgba(20,30,70,.06); }
        .is-lebih { color: #c8382b; font-weight: 600; }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            var form = document.getElementById('form-situs');
            if (! form) { return; }
            var utama = @json($bahasa->first()['kode']);
            var bahasaAktif = @json($aktifTab);
            var bawaan = { nama: @json(config('app.name')), deskripsi: @json(__('GBI Jl. Jend. Gatot Subroto — gereja lokal di bawah naungan Sinode Gereja Bethel Indonesia.')) };

            function nilai(jenis, kode) {
                var el = form.querySelector('[data-pratinjau="' + jenis + '"][data-bahasa="' + kode + '"]');
                return el ? el.value.trim() : '';
            }
            function segarkan() {
                var nama = form.querySelector('[data-pratinjau="nama"]').value.trim() || bawaan.nama;
                var judul = nilai('judul', bahasaAktif) || nilai('judul', utama) || nama;
                var desk = nilai('deskripsi', bahasaAktif) || nilai('deskripsi', utama) || bawaan.deskripsi;
                document.querySelectorAll('[data-isi="nama"]').forEach(function (e) { e.textContent = nama; });
                document.querySelectorAll('[data-isi="judul"]').forEach(function (e) { e.textContent = judul.length > 60 ? judul.slice(0, 58) + '…' : judul; });
                document.querySelectorAll('[data-isi="deskripsi"]').forEach(function (e) { e.textContent = desk.length > 160 ? desk.slice(0, 157) + '…' : desk; });
            }
            function hitung(el) {
                var info = form.querySelector('[data-hitung-untuk="' + el.id + '"]');
                if (! info) { return; }
                var batas = parseInt(el.getAttribute('data-hitung'), 10), n = el.value.length;
                info.textContent = n + ' karakter';
                info.classList.toggle('is-lebih', n > batas);
            }

            form.querySelectorAll('input, textarea').forEach(function (el) {
                el.addEventListener('input', function () { if (el.hasAttribute('data-hitung')) { hitung(el); } segarkan(); });
                if (el.hasAttribute('data-hitung')) { hitung(el); }
            });
            document.querySelectorAll('.lang-tabs [data-bahasa]').forEach(function (a) {
                a.addEventListener('click', function () { bahasaAktif = a.getAttribute('data-bahasa'); segarkan(); });
            });

            // Pratinjau gambar share sebelum diunggah.
            var og = document.getElementById('og_gambar');
            og.addEventListener('change', function () {
                if (! og.files[0]) { return; }
                var wadah = document.querySelector('[data-og-preview]');
                wadah.innerHTML = '<img alt="">';
                wadah.querySelector('img').src = URL.createObjectURL(og.files[0]);
            });

            segarkan();
        })();
    </script>
@endpush
