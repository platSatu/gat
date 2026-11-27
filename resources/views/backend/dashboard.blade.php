@extends('layouts.backend')

@section('title', 'Dashboard')

@php
    $k = $stat['kunjungan'];
    $konten = $stat['konten'];
    $angka = fn ($n) => number_format((int) $n, 0, ',', '.');
    $ringkas = fn ($n) => $n >= 1000000 ? str_replace('.', ',', round($n / 1000000, 1)).' jt' : ($n >= 10000 ? str_replace('.', ',', round($n / 1000, 1)).' rb' : number_format((int) $n, 0, ',', '.'));
    // Perubahan dalam persen: [teks, naik?] atau null kalau tidak bisa dibandingkan.
    $selisih = function ($kini, $lalu) {
        if (! $lalu) return null;
        $p = round(($kini - $lalu) / $lalu * 100);
        return [($p > 0 ? '+' : '').$p.'%', $p >= 0];
    };
    $dihitung = \Illuminate\Support\Carbon::parse($stat['dihitung'])->timezone(\App\Support\StatistikDashboard::zona());
@endphp

@section('content')
<div class="dash">
    <div class="dash-hero">
        <div>
            <span class="dash-hero__date">{{ now(\App\Support\StatistikDashboard::zona())->translatedFormat('l, d F Y') }}</span>
            <h1 class="dash-hero__title">Selamat datang, {{ auth()->user()->name }}</h1>
            <p class="dash-hero__desc">Ringkasan website {{ config('app.name') }}: pengunjung {{ \App\Support\StatistikDashboard::HARI }} hari terakhir dan artikel yang paling banyak dibaca.</p>
        </div>
        <div class="dash-hero__meta">
            <span>Diperbarui {{ $dihitung->format('H:i') }}</span>
            <a href="{{ route('admin.dashboard', ['segarkan' => 1]) }}" class="dash-btn"><i class="mdi mdi-refresh"></i> Segarkan</a>
        </div>
    </div>

    @unless (auth()->user()->punyaAksesAdmin())
        <div class="alert alert-warning">Akun Anda belum punya akses ke menu admin mana pun. Minta superadmin mengaturnya di Settings → Setting Menu.</div>
    @endunless
    @if ($stat['error'])
        <div class="alert alert-warning">{{ $stat['error'] }}</div>
    @endif
    @if ($k['menyiapkan'] ?? false)
        <div class="alert alert-info">
            <i class="mdi mdi-timer-sand"></i> Data kunjungan lama sedang direkap (sekali saja, bertahap supaya halaman tidak lambat).
            Angka di bawah belum lengkap. <a href="{{ route('admin.dashboard', ['segarkan' => 1]) }}">Segarkan</a> beberapa kali sampai pesan ini hilang.
        </div>
    @endif

    {{-- Angka utama --}}
    <div class="dash-tiles">
        @php
            $tiles = [
                ['Kunjungan hari ini', $k['hari_ini'] ?? null, $k ? $selisih($k['hari_ini'], $k['kemarin']) : null, 'vs kemarin', 'mdi-eye'],
                ['Kunjungan 7 hari', $k['tujuh_hari'] ?? null, $k ? $selisih($k['tujuh_hari'], $k['tujuh_hari_sebelumnya']) : null, 'vs 7 hari sebelumnya', 'mdi-chart-line'],
                ['Rata-rata per hari', $k['rata'] ?? null, null, 'kunjungan, 30 hari terakhir', 'mdi-account-multiple'],
                ['Artikel dibuka · 30 hari', $k['artikel_dibuka'] ?? null, null, $angka($konten['artikel']).' artikel terbit', 'mdi-book-open-page-variant'],
            ];
        @endphp
        @foreach ($tiles as [$label, $nilai, $delta, $catatan, $icon])
            <div class="dash-tile">
                <div class="dash-tile__top">
                    <span class="dash-tile__label">{{ $label }}</span>
                    <span class="dash-tile__icon"><i class="mdi {{ $icon }}"></i></span>
                </div>
                <div class="dash-tile__value">{{ $nilai === null ? '–' : $ringkas($nilai) }}</div>
                <div class="dash-tile__foot">
                    @if ($delta)
                        <span @class(['dash-delta', 'is-up' => $delta[1], 'is-down' => ! $delta[1]])>
                            <i class="mdi {{ $delta[1] ? 'mdi-arrow-up' : 'mdi-arrow-down' }}"></i>{{ $delta[0] }}
                        </span>
                    @endif
                    <span>{{ $catatan }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row">
        {{-- Grafik kunjungan harian --}}
        <div class="col-xl-8">
            <section class="dash-card">
                <header class="dash-card__head">
                    <div>
                        <h2>Kunjungan {{ \App\Support\StatistikDashboard::HARI }} hari terakhir</h2>
                        <p>{{ $k ? $angka($k['total']).' kunjungan halaman' : 'Belum ada data' }}</p>
                    </div>
                    <div class="dash-legend">
                        <span><i style="background: var(--series-1)"></i>Kunjungan halaman</span>
                        <span><i style="background: var(--series-2)"></i>Pengunjung unik</span>
                    </div>
                </header>
                @if ($k)
                    <div class="dash-line" id="grafik-harian" role="img" aria-label="Grafik kunjungan halaman dan pengunjung unik per hari"></div>
                    <details class="dash-table-toggle">
                        <summary>Lihat sebagai tabel</summary>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Tanggal</th><th class="text-right">Kunjungan</th><th class="text-right">Unik</th></tr></thead>
                                <tbody>
                                    @foreach (array_reverse($k['harian']) as $h)
                                        <tr><td>{{ $h['hari'] }}</td><td class="text-right">{{ $angka($h['kunjungan']) }}</td><td class="text-right">{{ $angka($h['unik']) }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </details>
                @else
                    <div class="dash-empty"><i class="mdi mdi-chart-line"></i>Belum ada data kunjungan.</div>
                @endif
            </section>
        </div>

        {{-- Perangkat & sumber --}}
        <div class="col-xl-4">
            <section class="dash-card">
                <header class="dash-card__head"><div><h2>Perangkat</h2><p>30 hari terakhir</p></div></header>
                @php $totalPerangkat = $k ? max(1, array_sum(array_column($k['perangkat'], 'n'))) : 0; @endphp
                @if ($k && array_sum(array_column($k['perangkat'], 'n')) > 0)
                    <div class="dash-seg">
                        @foreach ($k['perangkat'] as $i => $p)
                            @if ($p['n'] > 0)
                                <span style="width: {{ $p['n'] / $totalPerangkat * 100 }}%; background: var(--series-{{ $i + 1 }})" title="{{ $p['jenis'] }}: {{ $angka($p['n']) }}"></span>
                            @endif
                        @endforeach
                    </div>
                    <ul class="dash-seg-legend">
                        @foreach ($k['perangkat'] as $i => $p)
                            <li>
                                <span><i style="background: var(--series-{{ $i + 1 }})"></i>{{ $p['jenis'] }}</span>
                                <strong>{{ round($p['n'] / $totalPerangkat * 100) }}%</strong>
                                <small>{{ $angka($p['n']) }}</small>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="dash-empty small">Belum ada data.</div>
                @endif
            </section>

            <section class="dash-card">
                <header class="dash-card__head"><div><h2>Sumber pengunjung</h2><p>Dari mana pengunjung datang</p></div></header>
                @if ($k && count($k['sumber']))
                    @php $maxSumber = max(array_column($k['sumber'], 'n')); @endphp
                    <ul class="dash-bars">
                        @foreach ($k['sumber'] as $s)
                            <li title="{{ $s['label'] }}: {{ $angka($s['n']) }} kunjungan">
                                <div class="dash-bars__row"><span class="dash-bars__label">{{ $s['label'] }}</span><span class="dash-bars__value">{{ $angka($s['n']) }}</span></div>
                                <div class="dash-bars__track"><span style="width: {{ $s['n'] / $maxSumber * 100 }}%"></span></div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="dash-empty small">Belum ada data.</div>
                @endif
            </section>
        </div>
    </div>

    <div class="row">
        {{-- Halaman terpopuler --}}
        <div class="col-xl-6">
            <section class="dash-card">
                <header class="dash-card__head"><div><h2>Halaman terpopuler</h2><p>Paling banyak dibuka, 30 hari terakhir</p></div></header>
                @if ($k && count($k['halaman']))
                    @php $maxHalaman = max(array_column($k['halaman'], 'n')); @endphp
                    <ol class="dash-bars dash-bars--ranked">
                        @foreach ($k['halaman'] as $h)
                            <li title="{{ $h['path'] }} · {{ $angka($h['n']) }} kunjungan">
                                <div class="dash-bars__row">
                                    <span class="dash-bars__label">{{ $h['label'] }} <small>{{ \Illuminate\Support\Str::limit($h['path'], 40) }}</small></span>
                                    <span class="dash-bars__value">{{ $angka($h['n']) }}</span>
                                </div>
                                <div class="dash-bars__track"><span style="width: {{ $h['n'] / $maxHalaman * 100 }}%"></span></div>
                            </li>
                        @endforeach
                    </ol>
                @else
                    <div class="dash-empty"><i class="mdi mdi-file-outline"></i>Belum ada data.</div>
                @endif
            </section>
        </div>

        {{-- Artikel paling dibaca 30 hari --}}
        <div class="col-xl-6">
            <section class="dash-card">
                <header class="dash-card__head"><div><h2>Artikel trending</h2><p>Paling banyak dibuka, 7 hari terakhir</p></div></header>
                @if ($k && count($k['artikel_7_hari']))
                    @php $maxArtikel = max(array_column($k['artikel_7_hari'], 'n')); @endphp
                    <ol class="dash-articles">
                        @foreach ($k['artikel_7_hari'] as $i => $a)
                            <li>
                                <span class="dash-articles__rank">{{ $i + 1 }}</span>
                                @if ($a['gambar'])<img src="{{ $a['gambar'] }}" alt="" loading="lazy">@else<span class="dash-articles__noimg"><i class="mdi mdi-file-document"></i></span>@endif
                                <div class="dash-articles__body">
                                    <a href="{{ $a['url'] }}" target="_blank" rel="noopener">{{ $a['judul'] }}</a>
                                    <div class="dash-bars__track"><span style="width: {{ $a['n'] / $maxArtikel * 100 }}%"></span></div>
                                </div>
                                <span class="dash-articles__num">{{ $angka($a['n']) }}<small>dibuka</small></span>
                            </li>
                        @endforeach
                    </ol>
                @else
                    <div class="dash-empty"><i class="mdi mdi-book-open"></i>Belum ada artikel yang dibuka minggu ini.</div>
                @endif
            </section>
        </div>
    </div>

    {{-- Artikel terpopuler sepanjang waktu --}}
    <section class="dash-card">
        <header class="dash-card__head">
            <div><h2>Artikel paling banyak dibaca</h2><p>90 hari terakhir, dari catatan kunjungan</p></div>
            <div class="dash-chips">
                <span class="dash-chip"><i class="mdi mdi-star-circle"></i> {{ $konten['highlights'] }} highlights aktif</span>
                <span class="dash-chip"><i class="mdi mdi-account-multiple"></i> {{ $konten['mitra'] }} mitra</span>
                <span class="dash-chip"><i class="mdi mdi-account-box"></i> {{ $konten['user'] }} user admin</span>
            </div>
        </header>
        @if ($k && count($k['artikel_90_hari']))
            @php $maxBaca = max(1, max(array_column($k['artikel_90_hari'], 'n'))); @endphp
            <div class="table-responsive">
                <table class="dash-table">
                    <thead><tr><th style="width: 48px">#</th><th>Artikel</th><th>Kategori</th><th>Terbit</th><th style="width: 30%">Dibuka</th></tr></thead>
                    <tbody>
                        @foreach ($k['artikel_90_hari'] as $i => $a)
                            <tr>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td>
                                    <div class="dash-article-cell">
                                        @if ($a['gambar'])<img src="{{ $a['gambar'] }}" alt="" loading="lazy">@else<span class="dash-articles__noimg"><i class="mdi mdi-file-document"></i></span>@endif
                                        <a href="{{ $a['url'] }}" target="_blank" rel="noopener">{{ $a['judul'] }}</a>
                                    </div>
                                </td>
                                <td><span class="dash-chip">{{ $a['kategori'] ?? '-' }}</span></td>
                                <td class="text-nowrap text-muted">{{ $a['tanggal'] }}</td>
                                <td>
                                    <div class="dash-inline-bar">
                                        <div class="dash-bars__track"><span style="width: {{ $a['n'] / $maxBaca * 100 }}%"></span></div>
                                        <strong>{{ $angka($a['n']) }}</strong>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="dash-empty">Belum ada artikel.</div>
        @endif
    </section>

    <div class="dash-tooltip" id="dash-tooltip" role="status" aria-live="polite"></div>
</div>
@endsection

@push('styles')
<style>
    .dash {
        --surface-1: #ffffff; --text-primary: #1f2433; --text-secondary: #6b7287; --line: #e9ebf2; --grid: #eef0f5;
        --brand: #2b3a8c; --series-1: #257de0; --series-2: #f47230; --series-3: #00b894;
        --good: #1e9e5a; --bad: #c8382b; --radius: 14px;
        color: var(--text-primary);
    }
    .dash-hero { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-end; gap: 16px; padding: 26px 28px; margin-bottom: 20px; border-radius: var(--radius); color: #fff;
        background: radial-gradient(circle at 90% 10%, rgba(255,255,255,.14), transparent 40%), linear-gradient(120deg, #24306f 0%, #2b3a8c 55%, #4458c4 100%); box-shadow: 0 10px 30px rgba(36,48,111,.18); }
    .dash-hero__date { font-size: 12px; letter-spacing: .05em; text-transform: uppercase; opacity: .8; }
    .dash-hero__title { margin: 6px 0 4px; font-size: 26px; font-weight: 700; color: #fff; }
    .dash-hero__desc { margin: 0; max-width: 620px; font-size: 14px; color: rgba(255,255,255,.8); }
    .dash-hero__meta { display: flex; align-items: center; gap: 12px; font-size: 12px; color: rgba(255,255,255,.75); }
    .dash-btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 10px; background: rgba(255,255,255,.15); color: #fff; font-weight: 600; }
    .dash-btn:hover { background: rgba(255,255,255,.25); color: #fff; text-decoration: none; }

    .dash-tiles { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 20px; }
    .dash-tile { padding: 18px 20px; background: var(--surface-1); border: 1px solid var(--line); border-radius: var(--radius); box-shadow: 0 2px 10px rgba(20,30,70,.04); }
    .dash-tile__top { display: flex; justify-content: space-between; align-items: center; }
    .dash-tile__label { font-size: 13px; color: var(--text-secondary); }
    .dash-tile__icon { display: grid; place-items: center; width: 34px; height: 34px; border-radius: 10px; background: #eef0fb; color: var(--brand); font-size: 18px; }
    .dash-tile__value { margin: 8px 0 6px; font-size: 30px; font-weight: 700; line-height: 1; letter-spacing: -.02em; }
    .dash-tile__foot { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; font-size: 12px; color: var(--text-secondary); }
    .dash-delta { display: inline-flex; align-items: center; padding: 1px 7px; border-radius: 999px; font-weight: 600; }
    .dash-delta.is-up { background: #e7f8ef; color: var(--good); }
    .dash-delta.is-down { background: #fdeceb; color: var(--bad); }

    .dash-card { margin-bottom: 20px; background: var(--surface-1); border: 1px solid var(--line); border-radius: var(--radius); box-shadow: 0 2px 10px rgba(20,30,70,.04); }
    .dash-card__head { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 10px; padding: 18px 20px 6px; }
    .dash-card__head h2 { margin: 0; font-size: 16px; font-weight: 700; color: var(--text-primary); }
    .dash-card__head p { margin: 2px 0 0; font-size: 12px; color: var(--text-secondary); }
    .dash-legend { display: flex; flex-wrap: wrap; gap: 14px; font-size: 12px; color: var(--text-secondary); }
    .dash-legend i, .dash-seg-legend i { display: inline-block; width: 10px; height: 10px; margin-right: 6px; border-radius: 3px; vertical-align: -1px; }
    .dash-empty { padding: 40px 20px; text-align: center; color: var(--text-secondary); }
    .dash-empty i { display: block; margin-bottom: 6px; font-size: 34px; color: #c5cada; }

    .dash-line { position: relative; height: 300px; padding: 4px 12px 8px 4px; }
    .dash-line svg { display: block; width: 100%; height: 100%; overflow: visible; }
    .dash-line .grid { stroke: var(--grid); stroke-width: 1; }
    .dash-line .axis-text { fill: var(--text-secondary); font-size: 11px; }
    .dash-line .end-label { fill: var(--text-primary); font-size: 11px; font-weight: 600; }
    .dash-line .crosshair { stroke: #b8bdcc; stroke-width: 1; stroke-dasharray: 3 3; }
    .dash-table-toggle { padding: 0 20px 14px; font-size: 13px; }
    .dash-table-toggle summary { color: var(--text-secondary); cursor: pointer; }

    .dash-tooltip { position: fixed; z-index: 1050; display: none; min-width: 170px; padding: 10px 12px; border-radius: 10px; background: #1f2433; color: #fff; font-size: 12px; box-shadow: 0 8px 24px rgba(0,0,0,.18); pointer-events: none; }
    .dash-tooltip strong { display: block; margin-bottom: 6px; font-size: 12px; }
    .dash-tooltip .row-t { display: flex; justify-content: space-between; gap: 16px; }
    .dash-tooltip i { display: inline-block; width: 8px; height: 8px; margin-right: 6px; border-radius: 2px; }

    .dash-seg { display: flex; gap: 2px; height: 14px; margin: 10px 20px 14px; }
    .dash-seg span { display: block; height: 100%; border-radius: 4px; }
    .dash-seg-legend { margin: 0; padding: 0 20px 18px; list-style: none; }
    .dash-seg-legend li { display: grid; grid-template-columns: 1fr auto 70px; align-items: center; gap: 10px; padding: 6px 0; font-size: 13px; border-top: 1px solid var(--line); }
    .dash-seg-legend li:first-child { border-top: 0; }
    .dash-seg-legend small { text-align: right; color: var(--text-secondary); }

    .dash-bars { margin: 0; padding: 6px 20px 18px; list-style: none; }
    .dash-bars li { padding: 7px 0; }
    .dash-bars--ranked { counter-reset: r; }
    .dash-bars--ranked .dash-bars__label::before { counter-increment: r; content: counter(r); display: inline-grid; place-items: center; width: 20px; height: 20px; margin-right: 8px; border-radius: 6px; background: #f0f1f5; color: var(--text-secondary); font-size: 11px; font-weight: 700; }
    .dash-bars__row { display: flex; justify-content: space-between; gap: 12px; margin-bottom: 5px; font-size: 13px; }
    .dash-bars__label { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .dash-bars__label small { margin-left: 6px; color: var(--text-secondary); }
    .dash-bars__value { font-weight: 600; font-variant-numeric: tabular-nums; }
    .dash-bars__track { height: 6px; border-radius: 4px; background: #eef2fb; overflow: hidden; }
    .dash-bars__track span { display: block; height: 100%; border-radius: 4px; background: var(--series-1); }

    .dash-articles { margin: 0; padding: 6px 20px 14px; list-style: none; }
    .dash-articles li { display: grid; grid-template-columns: 22px 52px 1fr auto; align-items: center; gap: 12px; padding: 9px 0; border-top: 1px solid var(--line); }
    .dash-articles li:first-child { border-top: 0; }
    .dash-articles__rank { font-weight: 700; color: var(--text-secondary); text-align: center; }
    .dash-articles img, .dash-articles__noimg, .dash-article-cell img { width: 52px; height: 38px; border-radius: 8px; object-fit: cover; background: #eef0f5; }
    .dash-articles__noimg { display: grid; place-items: center; color: #b2b8c9; }
    .dash-articles__body { min-width: 0; }
    .dash-articles__body a, .dash-article-cell a { display: block; margin-bottom: 6px; overflow: hidden; color: var(--text-primary); font-weight: 600; font-size: 13px; text-overflow: ellipsis; white-space: nowrap; }
    .dash-articles__body a:hover, .dash-article-cell a:hover { color: var(--brand); }
    .dash-articles__num { font-weight: 700; text-align: right; font-variant-numeric: tabular-nums; }
    .dash-articles__num small { display: block; font-size: 11px; font-weight: 400; color: var(--text-secondary); }

    .dash-chips { display: flex; flex-wrap: wrap; gap: 6px; }
    .dash-chip { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 999px; background: #eef0fb; color: var(--brand); font-size: 12px; font-weight: 500; white-space: nowrap; }
    .dash-table { width: 100%; }
    .dash-table th { padding: 10px 20px; font-size: 11px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; color: var(--text-secondary); background: #fafbfd; border-bottom: 1px solid var(--line); }
    .dash-table td { padding: 12px 20px; border-top: 1px solid var(--line); vertical-align: middle; }
    .dash-article-cell { display: flex; align-items: center; gap: 12px; max-width: 520px; }
    .dash-article-cell a { margin: 0; white-space: normal; }
    .dash-inline-bar { display: flex; align-items: center; gap: 10px; }
    .dash-inline-bar .dash-bars__track { flex: 1; }
    .dash-inline-bar strong { min-width: 64px; text-align: right; font-variant-numeric: tabular-nums; }
</style>
@endpush

@push('scripts')
@if ($k)
<script>
(function () {
    var data = @json($k['harian']);
    var wadah = document.getElementById('grafik-harian');
    var tip = document.getElementById('dash-tooltip');
    var seri = [
        { key: 'kunjungan', nama: 'Kunjungan halaman', warna: 'var(--series-1)' },
        { key: 'unik', nama: 'Pengunjung unik', warna: 'var(--series-2)' }
    ];
    var fmt = function (n) { return Number(n).toLocaleString('id-ID'); };
    var NS = 'http://www.w3.org/2000/svg';

    function el(tag, attr, induk) {
        var e = document.createElementNS(NS, tag);
        for (var k in attr) { e.setAttribute(k, attr[k]); }
        if (induk) { induk.appendChild(e); }
        return e;
    }

    // Angka sumbu yang bulat: 0, 50, 100 ... (atau 1 rb, 2 rb ...)
    function skala(maks) {
        if (maks <= 0) { return { maks: 4, langkah: 1 }; }
        var kasar = maks / 4, p = Math.pow(10, Math.floor(Math.log10(kasar))), n = kasar / p;
        var langkah = (n <= 1 ? 1 : n <= 2 ? 2 : n <= 2.5 ? 2.5 : n <= 5 ? 5 : 10) * p;
        return { maks: Math.ceil(maks / langkah) * langkah, langkah: langkah };
    }
    function label(v) { return v >= 10000 ? (v / 1000).toLocaleString('id-ID') + ' rb' : fmt(v); }

    function gambar() {
        wadah.innerHTML = '';
        var W = wadah.clientWidth - 16, H = wadah.clientHeight - 12;
        var kiri = 44, kanan = 92, atas = 12, bawah = 26;
        var lebar = W - kiri - kanan, tinggi = H - atas - bawah;
        var maks = Math.max.apply(null, data.map(function (d) { return d.kunjungan; }));
        var s = skala(maks);
        var x = function (i) { return kiri + (data.length < 2 ? lebar / 2 : i * lebar / (data.length - 1)); };
        var y = function (v) { return atas + tinggi - v / s.maks * tinggi; };

        var svg = el('svg', { viewBox: '0 0 ' + W + ' ' + H, preserveAspectRatio: 'none' }, wadah);

        for (var v = 0; v <= s.maks + 1e-9; v += s.langkah) {
            el('line', { x1: kiri, x2: kiri + lebar, y1: y(v), y2: y(v), class: 'grid' }, svg);
            var t = el('text', { x: kiri - 8, y: y(v) + 4, 'text-anchor': 'end', class: 'axis-text' }, svg);
            t.textContent = label(v);
        }
        var tiap = Math.max(1, Math.ceil(data.length / Math.max(2, Math.floor(lebar / 70))));
        data.forEach(function (d, i) {
            if (i % tiap === 0 || i === data.length - 1) {
                var t = el('text', { x: x(i), y: H - 6, 'text-anchor': 'middle', class: 'axis-text' }, svg);
                t.textContent = d.label;
            }
        });

        // Area lembut di bawah garis kunjungan, lalu dua garis 2px.
        var grad = el('linearGradient', { id: 'isi-kunjungan', x1: 0, x2: 0, y1: 0, y2: 1 }, el('defs', {}, svg));
        el('stop', { offset: '0%', 'stop-color': '#257de0', 'stop-opacity': .18 }, grad);
        el('stop', { offset: '100%', 'stop-color': '#257de0', 'stop-opacity': 0 }, grad);
        var titik = function (key) { return data.map(function (d, i) { return x(i) + ',' + y(d[key]); }).join(' L'); };
        el('path', { d: 'M' + titik('kunjungan') + ' L' + x(data.length - 1) + ',' + y(0) + ' L' + x(0) + ',' + y(0) + ' Z', fill: 'url(#isi-kunjungan)' }, svg);

        var ujung = [];
        seri.forEach(function (sr) {
            el('path', { d: 'M' + titik(sr.key), fill: 'none', stroke: sr.warna, 'stroke-width': 2, 'stroke-linejoin': 'round', 'stroke-linecap': 'round' }, svg);
            var akhir = data[data.length - 1][sr.key];
            ujung.push({ y: y(akhir), teks: fmt(akhir), sr: sr });
        });
        // Label nilai di ujung garis (digeser kalau bertabrakan).
        ujung.sort(function (a, b) { return a.y - b.y; });
        for (var j = 1; j < ujung.length; j++) { if (ujung[j].y - ujung[j - 1].y < 14) { ujung[j].y = ujung[j - 1].y + 14; } }
        ujung.forEach(function (u) {
            el('circle', { cx: x(data.length - 1), cy: y(data[data.length - 1][u.sr.key]), r: 4, fill: u.sr.warna, stroke: '#fff', 'stroke-width': 2 }, svg);
            var t = el('text', { x: x(data.length - 1) + 10, y: u.y + 4, class: 'end-label' }, svg);
            t.textContent = u.teks + ' ' + (u.sr.key === 'unik' ? 'unik' : 'kunjungan');
        });

        // Lapisan hover: garis bantu + titik + tooltip.
        var garis = el('line', { y1: atas, y2: atas + tinggi, class: 'crosshair', visibility: 'hidden' }, svg);
        var bulat = seri.map(function (sr) { return el('circle', { r: 5, fill: sr.warna, stroke: '#fff', 'stroke-width': 2, visibility: 'hidden' }, svg); });
        var area = el('rect', { x: kiri, y: atas, width: lebar, height: tinggi, fill: 'transparent' }, svg);

        function tunjuk(e) {
            var kotak = svg.getBoundingClientRect();
            var px = (e.clientX - kotak.left) * (W / kotak.width);
            var i = Math.round((px - kiri) / (lebar / Math.max(1, data.length - 1)));
            i = Math.max(0, Math.min(data.length - 1, i));
            var d = data[i];
            garis.setAttribute('x1', x(i)); garis.setAttribute('x2', x(i)); garis.setAttribute('visibility', 'visible');
            seri.forEach(function (sr, k) { bulat[k].setAttribute('cx', x(i)); bulat[k].setAttribute('cy', y(d[sr.key])); bulat[k].setAttribute('visibility', 'visible'); });
            tip.innerHTML = '<strong>' + d.hari + '</strong>' + seri.map(function (sr) {
                return '<div class="row-t"><span><i style="background:' + sr.warna + '"></i>' + sr.nama + '</span><b>' + fmt(d[sr.key]) + '</b></div>';
            }).join('');
            tip.style.display = 'block';
            var kiriTip = e.clientX + 16;
            if (kiriTip + tip.offsetWidth > window.innerWidth - 8) { kiriTip = e.clientX - tip.offsetWidth - 16; }
            tip.style.left = kiriTip + 'px';
            tip.style.top = (e.clientY - tip.offsetHeight / 2) + 'px';
        }
        function sembunyi() {
            tip.style.display = 'none'; garis.setAttribute('visibility', 'hidden');
            bulat.forEach(function (b) { b.setAttribute('visibility', 'hidden'); });
        }
        area.addEventListener('mousemove', tunjuk);
        area.addEventListener('touchstart', function (e) { tunjuk(e.touches[0]); }, { passive: true });
        area.addEventListener('mouseleave', sembunyi);
    }

    gambar();
    var jeda;
    window.addEventListener('resize', function () { clearTimeout(jeda); jeda = setTimeout(gambar, 150); });
})();
</script>
@endif
@endpush
