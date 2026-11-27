@extends('layouts.backend')

@section('title', 'Bahasa')
@section('breadcrumb')<li class="breadcrumb-item">Settings</li><li class="breadcrumb-item active">Bahasa</li>@endsection

@section('content')
    @include('backend.settings._layout', [
        'judul' => 'Bahasa',
        'keterangan' => 'Bahasa yang bisa dipilih pengunjung. Bahasa aktif juga muncul sebagai tab di form Artikel, Highlights, Tentang Kami, dan Hero.',
        'aksi' => $siap ? [['Tambah Bahasa', route('admin.settings.bahasa.create'), 'mdi-plus']] : [],
    ])

    @unless ($siap)
        <div class="alert alert-warning">
            Tabel <code>bahasa</code> belum ada. Jalankan
            <code>php artisan migrate --path=database/migrations/2026_11_27_400000_create_bahasa_terjemahan_tables.php</code>.
            Sampai saat itu website memakai Indonesia + English bawaan.
        </div>
    @else
        <div class="set-card">
            <div class="set-card__head"><h2>{{ $bahasa->count() }} bahasa</h2></div>
            <div class="table-responsive">
                <table class="set-table">
                    <thead>
                        <tr>
                            <th style="width: 80px">Kode</th>
                            <th>Nama</th>
                            <th>Teks tampilan diterjemahkan</th>
                            <th>Status</th>
                            <th>Urutan</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bahasa as $b)
                            @php $persen = $totalTeks ? round(($terisi[$b->kode] ?? 0) / $totalTeks * 100) : 0; @endphp
                            <tr>
                                <td><span @class(['set-chip', 'set-chip--dark' => $b->utama])>{{ strtoupper($b->kode) }}</span></td>
                                <td><strong>{{ $b->nama }}</strong> @if ($b->utama)<span class="set-chip set-chip--gray"><i class="mdi mdi-lock"></i> Utama</span>@endif</td>
                                <td style="min-width: 220px;">
                                    @if ($b->utama)
                                        <span class="text-muted small">Teks asli (boleh diganti di Terjemahan)</span>
                                    @else
                                        <div class="d-flex align-items-center" style="gap: 10px;">
                                            <div style="flex: 1; height: 6px; border-radius: 4px; background: #eef2fb; overflow: hidden;">
                                                <span style="display: block; height: 100%; width: {{ $persen }}%; background: #257de0; border-radius: 4px;"></span>
                                            </div>
                                            <a href="{{ route('admin.settings.terjemahan.index', ['bahasa' => $b->kode, 'status' => 'belum']) }}" class="small text-nowrap">
                                                {{ $terisi[$b->kode] ?? 0 }} / {{ $totalTeks }}
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span @class(['set-chip', 'set-chip--green' => $b->aktif, 'set-chip--gray' => ! $b->aktif])>{{ $b->aktif ? 'Aktif' : 'Nonaktif' }}</span>
                                </td>
                                <td class="text-muted">{{ $b->urutan }}</td>
                                <td class="set-actions">
                                    <a href="{{ route('admin.settings.bahasa.edit', $b) }}" class="set-icon-btn" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                    @unless ($b->utama)
                                        <form method="POST" action="{{ route('admin.settings.bahasa.destroy', $b) }}" class="d-inline"
                                            onsubmit="return confirm('Hapus bahasa {{ addslashes($b->nama) }} dari daftar? Isi terjemahannya tetap disimpan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="set-icon-btn set-icon-btn--danger" title="Hapus"><i class="mdi mdi-delete"></i></button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="set-card">
            <div class="set-card__body small text-muted">
                <strong>Menambah bahasa baru:</strong> tambah di sini → isi teks tampilan di <a href="{{ route('admin.settings.terjemahan.index') }}">Terjemahan</a>
                → isi konten (Artikel, Highlights, Tentang Kami, Hero) lewat tab bahasa di form masing-masing. Bagian yang belum diterjemahkan otomatis tampil dalam Bahasa Indonesia.
            </div>
        </div>
    @endunless
@endsection
