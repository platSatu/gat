{{-- Halaman uji koneksi database lama (hanya di local, route /cek-database). Boleh dihapus setelah selesai. --}}
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cek Database</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 24px; color: #1f2937; background: #f6f7fb; }
        .kotak { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px 20px; margin-bottom: 16px; }
        .angka { display: flex; flex-wrap: wrap; gap: 12px; }
        .angka div { background: #eef2ff; border-radius: 10px; padding: 10px 14px; }
        .angka b { display: block; font-size: 1.4rem; }
        .artikel { display: flex; gap: 14px; padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
        .artikel img, .kosong { width: 140px; height: 90px; object-fit: cover; border-radius: 8px; background: #eee; flex: 0 0 140px; }
        .kosong { display: flex; align-items: center; justify-content: center; color: #999; font-size: .8rem; text-align: center; }
        .galeri { display: flex; flex-wrap: wrap; gap: 8px; }
        .galeri img { width: 110px; height: 80px; object-fit: cover; border-radius: 8px; }
        small { color: #6b7280; }
        .gagal { color: #b91c1c; }
    </style>
</head>
<body>
    <h2>Cek Database: {{ config('database.connections.mysql.database') }}</h2>

    <div class="kotak angka">
        @foreach ($jumlah as $label => $n)
            <div><small>{{ $label }}</small><b>{{ number_format($n) }}</b></div>
        @endforeach
    </div>

    <div class="kotak">
        <h3>5 artikel terbaru</h3>
        @foreach ($artikel as $a)
            <div class="artikel">
                @if ($a->image_url)
                    <img src="{{ $a->image_url }}" alt="" onerror="this.outerHTML='<div class=&quot;kosong gagal&quot;>gambar tidak ditemukan<br>{{ e($a->image) }}</div>'">
                @else
                    <div class="kosong">tanpa gambar</div>
                @endif
                <div>
                    <b>{{ $a->title }}</b><br>
                    <small>{{ optional($a->date_post)->format('d M Y') }} · {{ $a->category->name ?? '-' }} · {{ $a->status }} · slug: {{ $a->slug }}</small><br>
                    <small>path gambar: {{ $a->image ?? '-' }}</small>
                </div>
            </div>
        @endforeach
    </div>

    <div class="kotak">
        <h3>8 foto galeri terbaru</h3>
        <div class="galeri">
            @forelse ($galeri as $g)
                <img src="{{ $g->image_url }}" alt="" title="{{ $g->image }}" onerror="this.style.outline='2px solid #b91c1c'">
            @empty
                <small>Belum ada foto galeri.</small>
            @endforelse
        </div>
        <small>Foto dengan garis merah = file tidak ditemukan di public/uploads/gallery.</small>
    </div>
</body>
</html>
