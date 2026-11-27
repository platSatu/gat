@extends('layouts.backend')

@section('title', 'Terjemahan')
@section('breadcrumb')<li class="breadcrumb-item">Settings</li><li class="breadcrumb-item active">Terjemahan</li>@endsection

@section('content')
    @include('backend.settings._layout', [
        'judul' => 'Terjemahan',
        'keterangan' => 'Teks tampilan website: menu, tombol, judul bagian, footer. Isi terjemahannya per bahasa, atau ganti teks Indonesia-nya tanpa mengubah kode.',
    ])

    @unless ($siap)
        <div class="alert alert-warning">
            Tabel <code>terjemahan</code> belum ada. Jalankan
            <code>php artisan migrate --path=database/migrations/2026_11_27_400000_create_bahasa_terjemahan_tables.php</code>.
        </div>
    @else
        {{-- Kemajuan per bahasa --}}
        <div class="set-stats">
            @foreach ($ringkasan as $r)
                @php $persen = $r['total'] ? round($r['terisi'] / $r['total'] * 100) : 0; @endphp
                <a href="{{ route('admin.settings.terjemahan.index', ['bahasa' => $r['kode'], 'status' => 'belum']) }}" class="set-stat" style="text-decoration: none;">
                    <div class="set-stat__icon"><i class="mdi mdi-translate"></i></div>
                    <div style="flex: 1;">
                        <div class="set-stat__num">{{ $persen }}%</div>
                        <div class="set-stat__label">{{ $r['nama'] }} · {{ $r['terisi'] }}/{{ $r['total'] }} teks</div>
                        <div style="margin-top: 6px; height: 5px; border-radius: 4px; background: #eef2fb; overflow: hidden;">
                            <span style="display: block; height: 100%; width: {{ $persen }}%; background: #257de0;"></span>
                        </div>
                    </div>
                </a>
            @endforeach
            <div class="set-stat" style="flex-direction: column; align-items: stretch; gap: 8px;">
                <form method="POST" action="{{ route('admin.settings.terjemahan.pindai') }}">
                    @csrf
                    <button type="submit" class="btn set-btn set-btn--primary btn-block"><i class="mdi mdi-magnify-plus"></i> Pindai teks baru</button>
                </form>
                <small class="text-muted">Mencari semua teks di halaman website dan menambahkan yang belum terdaftar.</small>
            </div>
        </div>

        <div class="set-card">
            <div class="set-card__head">
                <h2>{{ $daftar->total() }} teks</h2>
                <form method="GET" class="set-filter">
                    <div class="set-search">
                        <i class="mdi mdi-magnify"></i>
                        <input type="text" name="cari" value="{{ $cari }}" class="form-control" placeholder="Cari teks...">
                    </div>
                    <select name="bahasa" class="form-control">
                        <option value="">Semua bahasa</option>
                        @foreach ($lain as $b)
                            <option value="{{ $b['kode'] }}" @selected($kodeFilter === $b['kode'])>{{ $b['nama'] }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="form-control">
                        <option value="">Semua</option>
                        <option value="belum" @selected($status === 'belum')>Belum diterjemahkan</option>
                        <option value="sudah" @selected($status === 'sudah')>Sudah diterjemahkan</option>
                    </select>
                    <button type="submit" class="btn set-btn set-btn--primary">Tampilkan</button>
                    @if ($cari !== '' || $kodeFilter || $status)
                        <a href="{{ route('admin.settings.terjemahan.index') }}" class="btn set-btn set-btn--ghost">Reset</a>
                    @endif
                </form>
            </div>

            @if ($status && ! $kodeFilter)
                <div class="alert alert-info m-3 mb-0">Pilih bahasanya dulu untuk menyaring yang belum/sudah diterjemahkan.</div>
            @endif

            @if ($daftar->isEmpty())
                <div class="set-empty"><i class="mdi mdi-translate"></i>Tidak ada teks yang cocok.</div>
            @else
                <form method="POST" action="{{ route('admin.settings.terjemahan.simpan') }}" id="form-terjemahan">
                    @csrf
                    <div class="table-responsive">
                        <table class="set-table tr-table">
                            <thead>
                                <tr>
                                    <th style="width: 26%">Teks asli (di kode)</th>
                                    <th>Indonesia <small class="text-muted text-lowercase">(pengganti, opsional)</small></th>
                                    @foreach ($lain as $b)
                                        <th>{{ $b['nama'] }}</th>
                                    @endforeach
                                    <th style="width: 50px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($daftar as $t)
                                    <tr>
                                        <td>
                                            <div class="tr-asli">{{ $t->kunci }}</div>
                                            @if ($t->sumber)<div class="tr-sumber" title="{{ $t->sumber }}">{{ \Illuminate\Support\Str::afterLast($t->sumber, 'views/') }}</div>@endif
                                        </td>
                                        <td>
                                            <textarea name="isi[{{ $t->id }}][id]" rows="2" class="form-control tr-input" placeholder="(sama dengan teks asli)">{{ $t->teks('id') }}</textarea>
                                        </td>
                                        @foreach ($lain as $b)
                                            <td>
                                                <textarea name="isi[{{ $t->id }}][{{ $b['kode'] }}]" rows="2" lang="{{ $b['kode'] }}"
                                                    @class(['form-control', 'tr-input', 'is-empty' => ! $t->teks($b['kode'])]) placeholder="Belum diterjemahkan">{{ $t->teks($b['kode']) }}</textarea>
                                            </td>
                                        @endforeach
                                        <td class="text-right">
                                            <button type="submit" form="hapus-{{ $t->id }}" class="set-icon-btn set-icon-btn--danger" title="Hapus dari daftar"
                                                onclick="return confirm('Hapus teks ini dari daftar? Di website akan tampil teks aslinya.')"><i class="mdi mdi-delete"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="tr-savebar">
                        <span class="text-muted small"><span data-tr-ubah>0</span> isian diubah di halaman ini</span>
                        <button type="submit" class="btn set-btn set-btn--primary"><i class="mdi mdi-content-save"></i> Simpan halaman ini</button>
                    </div>
                </form>

                @foreach ($daftar as $t)
                    <form method="POST" action="{{ route('admin.settings.terjemahan.destroy', $t) }}" id="hapus-{{ $t->id }}" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach

                @if ($daftar->hasPages())
                    <div class="set-pagination">{{ $daftar->links('pagination::bootstrap-4') }}</div>
                @endif
            @endif
        </div>

        <div class="set-card">
            <div class="set-card__head"><h2>Tambah teks manual</h2></div>
            <div class="set-card__body">
                <form method="POST" action="{{ route('admin.settings.terjemahan.store') }}" class="d-flex flex-wrap set-form" style="gap: 8px;">
                    @csrf
                    <input type="text" name="kunci" class="form-control" style="flex: 1; min-width: 260px;" placeholder="Teks Indonesia persis seperti di kode, mis. Beranda" required>
                    <button type="submit" class="btn set-btn set-btn--ghost"><i class="mdi mdi-plus"></i> Tambah</button>
                </form>
                <small class="text-muted">Biasanya tidak perlu: tombol <strong>Pindai teks baru</strong> menemukan teks secara otomatis.</small>
            </div>
        </div>
    @endunless
@endsection

@push('styles')
    <style>
        .tr-table td { vertical-align: top; }
        .tr-asli { font-weight: 600; white-space: pre-wrap; word-break: break-word; }
        .tr-sumber { margin-top: 4px; font-size: 11px; color: #9aa0b2; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 260px; }
        .tr-input { min-width: 200px; font-size: 13px; border-radius: 8px; border-color: #e9ebf2; resize: vertical; }
        .tr-input.is-empty { background: #fffaf0; border-color: #f3dca8; }
        .tr-input.is-changed { border-color: #2b3a8c; box-shadow: 0 0 0 2px rgba(43, 58, 140, .12); background: #fff; }
        .tr-savebar { position: sticky; bottom: 0; z-index: 5; display: flex; justify-content: flex-end; align-items: center; gap: 14px; padding: 12px 20px; background: rgba(255,255,255,.96); border-top: 1px solid #e9ebf2; border-radius: 0 0 14px 14px; }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            var hitung = document.querySelector('[data-tr-ubah]');
            if (! hitung) { return; }
            var isian = document.querySelectorAll('.tr-input');
            isian.forEach(function (el) {
                el.dataset.awal = el.value;
                el.addEventListener('input', function () {
                    el.classList.toggle('is-changed', el.value !== el.dataset.awal);
                    hitung.textContent = document.querySelectorAll('.tr-input.is-changed').length;
                });
            });
            // Ingatkan kalau meninggalkan halaman sebelum menyimpan.
            var form = document.getElementById('form-terjemahan'), simpan = false;
            form.addEventListener('submit', function () { simpan = true; });
            window.addEventListener('beforeunload', function (e) {
                if (! simpan && document.querySelector('.tr-input.is-changed')) { e.preventDefault(); e.returnValue = ''; }
            });
        })();
    </script>
@endpush
