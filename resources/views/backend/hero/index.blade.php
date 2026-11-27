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
    </style>
@endpush

@include('backend.partials.crud-styles')
