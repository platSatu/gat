<?php

namespace App\Support;

use App\Models\Article;
use App\Models\Highlight;
use App\Models\Partner;
use App\Models\User;
use App\Models\VisitorHistory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

/**
 * Angka & grafik untuk beranda admin (/dashboard/admin).
 *
 * Sumber: visitor_histories (catatan kunjungan, jutaan baris, dipakai bersama website lama).
 * Kolom articles.read_count tidak dipakai karena website lama tidak pernah mengisinya (semua 0).
 *
 * Supaya cepat, kunjungan direkap PER HARI ke tabel statistik_kunjungan (tabel milik v2):
 *   - hari yang sudah lewat dihitung SEKALI lalu disimpan;
 *   - hari ini dihitung langsung (±beberapa ribu baris);
 *   - baris dibaca berurutan lewat primary key (cepat), penyaringan robot/admin/aset dilakukan di PHP;
 *   - pengisian awal dibatasi waktunya per permintaan (BATAS_DETIK), dilanjutkan saat dashboard dibuka lagi.
 * Hasil akhir di-cache (config gatsu.statistik.cache_menit).
 */
class StatistikDashboard
{
    public const HARI = 30;

    public const HARI_ARTIKEL = 90;

    private const TABEL = 'statistik_kunjungan';

    private const KUNCI_CACHE = 'dashboard.statistik.v3';

    private const BATAS_DETIK = 12;

    private const PER_AMBIL = 4000;

    /** URL halaman detail artikel: /article/{slug} (website lama) atau /artikel/{slug} (v2). */
    private const POLA_ARTIKEL = '#^/(article|artikel)/([^/]+)$#';

    private const POLA_ADMIN = '#^/(dashboard|admins?|auth|login|logout|api|storage|bahasa|gereja/cari|livewire|_debugbar|up)(/|$)#i';

    private const POLA_ASET = '#\.(css|js|png|jpe?g|gif|svg|webp|ico|woff2?|map|txt|xml|pdf)$#i';

    /** Zona waktu kolom visited_at (website lama menyimpan dalam WIB). */
    public static function zona(): string
    {
        return (string) config('gatsu.statistik.zona_waktu', 'Asia/Jakarta');
    }

    public static function ambil(bool $segarkan = false): array
    {
        if (! $segarkan && is_array($data = Cache::get(self::KUNCI_CACHE))) {
            return $data;
        }

        $data = (new self)->hitung();
        $menit = ($data['kunjungan']['menyiapkan'] ?? false) ? 1 : max(1, (int) config('gatsu.statistik.cache_menit', 10));
        Cache::put(self::KUNCI_CACHE, $data, now()->addMinutes($menit));

        return $data;
    }

    private function hitung(): array
    {
        $data = [
            'dihitung' => now(self::zona())->toIso8601String(),
            'konten' => $this->konten(),
            'kunjungan' => null,
            'error' => null,
        ];

        try {
            if (! Schema::hasTable(self::TABEL)) {
                $data['error'] = 'Tabel statistik_kunjungan belum dibuat. Jalankan: php artisan migrate --path=database/migrations/2026_11_27_300000_create_statistik_kunjungan_table.php';
            } elseif (Schema::hasTable('visitor_histories')) {
                $data['kunjungan'] = $this->kunjungan();
            }
        } catch (Throwable $e) {
            report($e);
            $data['error'] = 'Statistik pengunjung belum bisa dihitung: '.Str::limit($e->getMessage(), 160);
        }

        return $data;
    }

    private function konten(): array
    {
        return [
            'artikel' => Article::terbit()->count(),
            'highlights' => Schema::hasTable('highlights') ? Highlight::tampil()->count() : 0,
            'mitra' => Schema::hasTable('partners') ? Partner::aktif()->count() : 0,
            'user' => User::count(),
        ];
    }

    /* ------------------------------------------------------------- Kunjungan */

    private function kunjungan(): array
    {
        $hariIni = now(self::zona())->startOfDay();
        $awal = $hariIni->copy()->subDays(self::HARI_ARTIKEL - 1);

        [$langsungHariIni, $selesai] = $this->rekapYangBelum($awal, $hariIni);

        // [tanggal => [jenis => [kunci => jumlah]]]
        $perHari = [];
        DB::table(self::TABEL)->whereBetween('tanggal', [$awal->toDateString(), $hariIni->copy()->subDay()->toDateString()])
            ->orderBy('tanggal')->get(['tanggal', 'jenis', 'kunci', 'jumlah'])
            ->each(function ($r) use (&$perHari) {
                $perHari[substr((string) $r->tanggal, 0, 10)][$r->jenis][$r->kunci] = (int) $r->jumlah;
            });
        $perHari[$hariIni->toDateString()] = $this->bentukSimpan($langsungHariIni);

        $tanggal = fn (int $hari) => collect(range($hari - 1, 0))->map(fn ($i) => $hariIni->copy()->subDays($i))->all();

        $harian = array_map(fn (Carbon $d) => [
            'tanggal' => $d->toDateString(),
            'label' => $d->translatedFormat('d M'),
            'hari' => $d->translatedFormat('l, d M Y'),
            'kunjungan' => $perHari[$d->toDateString()]['kunjungan'][''] ?? 0,
            'unik' => $perHari[$d->toDateString()]['unik'][''] ?? 0,
        ], $tanggal(self::HARI));

        $jumlah = fn (int $dari, int $sampai) => array_sum(array_column(array_slice($harian, $dari, $sampai - $dari), 'kunjungan'));
        $n = count($harian);
        $total = $jumlah(0, $n);

        $gabung = function (string $jenis, int $hari) use ($perHari, $tanggal) {
            $hasil = [];
            foreach ($tanggal($hari) as $d) {
                foreach ($perHari[$d->toDateString()][$jenis] ?? [] as $kunci => $j) {
                    $hasil[$kunci] = ($hasil[$kunci] ?? 0) + $j;
                }
            }
            arsort($hasil);

            return $hasil;
        };

        $halaman30 = $gabung('halaman', self::HARI);
        $perangkat30 = $gabung('perangkat', self::HARI);

        return [
            'menyiapkan' => ! $selesai,
            'harian' => $harian,
            'hari_ini' => $harian[$n - 1]['kunjungan'],
            'kemarin' => $harian[$n - 2]['kunjungan'] ?? 0,
            'tujuh_hari' => $jumlah($n - 7, $n),
            'tujuh_hari_sebelumnya' => $jumlah($n - 14, $n - 7),
            'total' => $total,
            'rata' => (int) round($total / max(1, $n)),
            'halaman' => $this->halamanTerpopuler($halaman30),
            'artikel_dibuka' => array_sum(array_filter($halaman30, fn ($j, $path) => (bool) $this->slugArtikel((string) $path), ARRAY_FILTER_USE_BOTH)),
            'artikel_7_hari' => $this->artikelDariHalaman($gabung('halaman', 7), 8),
            'artikel_90_hari' => $this->artikelDariHalaman($gabung('halaman', self::HARI_ARTIKEL), 10),
            'perangkat' => collect(['HP', 'Komputer', 'Tablet'])->map(fn ($j) => ['jenis' => $j, 'n' => $perangkat30[$j] ?? 0])->all(),
            'sumber' => collect($gabung('sumber', self::HARI))->take(6)->map(fn ($n, $label) => ['label' => (string) $label, 'n' => $n])->values()->all(),
        ];
    }

    /**
     * Rekap hari-hari (sejak $awal sampai kemarin) yang belum ada di statistik_kunjungan, lalu hitung hari ini.
     *
     * @return array{0: array, 1: bool} [data mentah hari ini, selesai?]
     */
    private function rekapYangBelum(Carbon $awal, Carbon $hariIni): array
    {
        $kemarin = $hariIni->copy()->subDay();
        $sudah = DB::table(self::TABEL)->where('jenis', 'kunjungan')
            ->whereBetween('tanggal', [$awal->toDateString(), $kemarin->toDateString()])
            ->pluck('tanggal')->map(fn ($t) => substr((string) $t, 0, 10))->flip()->all();

        $dari = $hariIni->copy();
        for ($d = $awal->copy(); $d->lte($kemarin); $d->addDay()) {
            if (! isset($sudah[$d->toDateString()])) {
                $dari = $d->copy();
                break;
            }
        }

        $idMulai = $this->idMulai($dari);
        $data = [];
        $selesai = true;
        $tglTerakhir = $dari->toDateString();

        if ($idMulai !== null) {
            $mulai = microtime(true);
            $idTerakhir = $idMulai - 1;
            $kolom = ['id', 'ip_address', 'browser', 'page_url', 'visited_at', 'method', 'referrer', 'status_code'];

            while (true) {
                $baris = DB::table('visitor_histories')->where('id', '>', $idTerakhir)->orderBy('id')->limit(self::PER_AMBIL)->get($kolom);
                foreach ($baris as $b) {
                    $idTerakhir = (int) $b->id;
                    $tgl = substr((string) $b->visited_at, 0, 10);
                    if ($tgl >= $dari->toDateString()) {
                        $tglTerakhir = max($tglTerakhir, $tgl);
                        $this->tambah($data, $tgl, $b);
                    }
                }
                if ($baris->count() < self::PER_AMBIL) {
                    break;
                }
                if (microtime(true) - $mulai > self::BATAS_DETIK) {
                    $selesai = false;   // dilanjutkan saat dashboard dibuka lagi
                    break;
                }
            }
        }

        // Simpan hari yang sudah lengkap: semua hari sampai kemarin (kalau selesai), atau sebelum hari terakhir yang terbaca.
        for ($d = $dari->copy(); $d->lte($kemarin); $d->addDay()) {
            $tgl = $d->toDateString();
            if (isset($sudah[$tgl])) {
                continue;
            }
            if (! $selesai && $tgl >= $tglTerakhir) {
                break;
            }
            $this->simpan($tgl, $data[$tgl] ?? []);
        }

        return [$selesai ? ($data[$hariIni->toDateString()] ?? []) : [], $selesai];
    }

    /** Masukkan satu baris visitor_histories ke rekap harian (kalau memang kunjungan halaman publik). */
    private function tambah(array &$data, string $tgl, object $b): void
    {
        if (($b->method && strtoupper($b->method) !== 'GET') || ($b->status_code && (int) $b->status_code >= 400)) {
            return;
        }

        $url = parse_url((string) $b->page_url);
        $host = strtolower($url['host'] ?? '');
        $domain = strtolower((string) config('gatsu.statistik.domain', 'gbigatsu.id'));
        if ($host !== $domain && $host !== 'www.'.$domain) {
            return;
        }

        $path = '/'.trim((string) ($url['path'] ?? ''), '/');
        if (preg_match(self::POLA_ADMIN, $path) || preg_match(self::POLA_ASET, $path) || VisitorHistory::bot($b->browser)) {
            return;
        }

        $data[$tgl]['kunjungan'] = ($data[$tgl]['kunjungan'] ?? 0) + 1;
        $data[$tgl]['unik'][md5($b->ip_address.'|'.substr((string) $b->browser, 0, 190))] = true;
        $path = Str::limit(rawurldecode($path), 190, '');
        $data[$tgl]['halaman'][$path] = ($data[$tgl]['halaman'][$path] ?? 0) + 1;

        $ua = strtolower((string) $b->browser);
        $alat = preg_match('/ipad|tablet/', $ua) ? 'Tablet' : (preg_match('/mobile|android|iphone|ipod/', $ua) ? 'HP' : 'Komputer');
        $data[$tgl]['perangkat'][$alat] = ($data[$tgl]['perangkat'][$alat] ?? 0) + 1;

        if (($sumber = $this->labelSumber($b->referrer, $domain)) !== null) {
            $data[$tgl]['sumber'][$sumber] = ($data[$tgl]['sumber'][$sumber] ?? 0) + 1;
        }
    }

    /** Data mentah satu hari → [jenis => [kunci => jumlah]] (bentuk yang sama dengan isi tabel). */
    private function bentukSimpan(array $h): array
    {
        $halaman = $h['halaman'] ?? [];
        arsort($halaman);
        // Semua halaman artikel disimpan (untuk peringkat artikel); halaman lain cukup 60 teratas per hari.
        $artikel = array_filter($halaman, fn ($j, $p) => (bool) $this->slugArtikel((string) $p), ARRAY_FILTER_USE_BOTH);
        $lain = array_slice(array_diff_key($halaman, $artikel), 0, 60, true);
        $sumber = $h['sumber'] ?? [];
        arsort($sumber);

        return [
            'kunjungan' => ['' => $h['kunjungan'] ?? 0],
            'unik' => ['' => count($h['unik'] ?? [])],
            'halaman' => $artikel + $lain,
            'perangkat' => $h['perangkat'] ?? [],
            'sumber' => array_slice($sumber, 0, 20, true),
        ];
    }

    private function simpan(string $tgl, array $h): void
    {
        $baris = [];
        foreach ($this->bentukSimpan($h) as $jenis => $isi) {
            foreach ($isi as $kunci => $jumlah) {
                $baris[] = ['tanggal' => $tgl, 'jenis' => $jenis, 'kunci' => (string) $kunci, 'jumlah' => (int) $jumlah];
            }
        }

        DB::transaction(function () use ($tgl, $baris) {
            DB::table(self::TABEL)->where('tanggal', $tgl)->delete();
            foreach (array_chunk($baris, 500) as $potong) {
                DB::table(self::TABEL)->insertOrIgnore($potong);
            }
        });
    }

    /* ---------------------------------------------------------- Peringkat */

    private function halamanTerpopuler(array $halaman): array
    {
        $teratas = array_slice($halaman, 0, 8, true);
        $judul = $this->judulArtikel(array_filter(array_map(fn ($p) => $this->slugArtikel((string) $p), array_keys($teratas))));

        return collect($teratas)->map(function ($n, $path) use ($judul) {
            $slug = $this->slugArtikel((string) $path);

            return ['path' => (string) $path, 'label' => $slug && isset($judul[$slug]) ? $judul[$slug] : $this->labelHalaman((string) $path), 'n' => $n];
        })->values()->all();
    }

    private function artikelDariHalaman(array $halaman, int $jumlah): array
    {
        $perSlug = [];
        foreach ($halaman as $path => $n) {
            if ($slug = $this->slugArtikel((string) $path)) {
                $perSlug[$slug] = ($perSlug[$slug] ?? 0) + $n;
            }
        }
        arsort($perSlug);
        $perSlug = array_slice($perSlug, 0, $jumlah * 3, true);

        $artikel = Article::query()->with('category:id,name')->whereIn('slug', array_map('strval', array_keys($perSlug)))
            ->get(['id', 'category_id', 'slug', 'title', 'image', 'date_post'])->keyBy('slug');

        return collect($perSlug)->filter(fn ($n, $slug) => isset($artikel[$slug]))->take($jumlah)
            ->map(fn ($n, $slug) => [
                'judul' => $artikel[$slug]->title,
                'url' => route('articles.show', $artikel[$slug]),
                'gambar' => $artikel[$slug]->image_url,
                'kategori' => $artikel[$slug]->category?->name,
                'tanggal' => $artikel[$slug]->date_post?->translatedFormat('d M Y'),
                'n' => $n,
            ])->values()->all();
    }

    /* --------------------------------------------------------------- Bantuan */

    /** id pertama yang visited_at-nya >= $sejak (pencarian biner lewat primary key; tabel diisi berurutan waktu). */
    private function idMulai(Carbon $sejak): ?int
    {
        $tabel = fn () => DB::table('visitor_histories');
        $lo = (int) $tabel()->min('id');
        $hi = (int) $tabel()->max('id');
        if ($hi === 0) {
            return null;
        }

        $batas = $sejak->toDateTimeString();
        $hi++;
        while ($lo < $hi) {
            $tengah = intdiv($lo + $hi, 2);
            $baris = $tabel()->where('id', '>=', $tengah)->orderBy('id')->first(['id', 'visited_at']);
            if (! $baris || (string) $baris->visited_at >= $batas) {
                $hi = $tengah;
            } else {
                $lo = (int) $baris->id + 1;
            }
        }

        return $lo;
    }

    /** "/article/judul-x" (website lama) atau "/artikel/judul-x" (v2) → "judul-x". */
    private function slugArtikel(string $path): ?string
    {
        if (! preg_match(self::POLA_ARTIKEL, $path, $m)) {
            return null;
        }

        return in_array($m[2], ['semua', 'kategori', 'cari'], true) ? null : $m[2];
    }

    private function judulArtikel(array $slug): array
    {
        return $slug ? Article::query()->whereIn('slug', array_values($slug))->pluck('title', 'slug')->all() : [];
    }

    private function labelSumber(?string $referrer, string $domain): ?string
    {
        $host = strtolower((string) parse_url((string) $referrer, PHP_URL_HOST));
        if ($host === '') {
            return 'Langsung / tanpa referrer';
        }
        $host = (string) preg_replace('#^(www\.|m\.|l\.|lm\.)#', '', $host);
        if (Str::endsWith($host, $domain)) {
            return null;   // pindah halaman di dalam website sendiri
        }

        return match (true) {
            Str::contains($host, 'google') => 'Google',
            Str::contains($host, ['facebook', 'fb.']) => 'Facebook',
            Str::contains($host, 'instagram') => 'Instagram',
            Str::contains($host, ['youtube', 'youtu.be']) => 'YouTube',
            Str::contains($host, ['whatsapp', 'wa.me']) => 'WhatsApp',
            $host === 't.co' || Str::contains($host, ['twitter', 'x.com']) => 'X / Twitter',
            Str::contains($host, 'bing') => 'Bing',
            default => Str::limit($host, 60, ''),
        };
    }

    private function labelHalaman(string $path): string
    {
        return match (true) {
            $path === '/' => 'Beranda',
            Str::startsWith($path, '/category') => 'Kategori: '.Str::headline(Str::afterLast($path, '/')),
            Str::startsWith($path, ['/artikel', '/article']) => 'Artikel',
            Str::startsWith($path, '/tentang') => 'Tentang Kami',
            Str::startsWith($path, '/kegiatan') => 'Kegiatan',
            Str::startsWith($path, '/pelayanan') => 'Pelayanan',
            Str::startsWith($path, '/pengajaran') => 'Pengajaran',
            Str::startsWith($path, ['/highlights', '/media']) => 'Highlights',
            default => Str::headline(trim(str_replace('/', ' ', $path))) ?: $path,
        };
    }
}
