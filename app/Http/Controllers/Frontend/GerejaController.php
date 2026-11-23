<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Pencarian gereja & jadwal ibadah untuk website publik (bagian "Gereja Terdekat").
 *
 * Sama seperti cabangProxy() di website lama: data cabang + kebaktian diambil dari HMMinistry,
 * kebaktian dikelompokkan per kdcabang, lalu dicari di nama/alamat/kota/provinsi/negara/benua.
 * Bedanya: data disimpan sementara (cache) sehingga tidak mengunduh ulang setiap ketikan,
 * dan hasil yang dikirim ke browser sudah dirapikan & dibatasi jumlahnya.
 */
class GerejaController extends Controller
{
    private const MAKS_HASIL = 25;

    private const KUNCI_CACHE = 'hmministry.gereja.v1';

    public function cari(Request $request): JsonResponse
    {
        $kata = trim(Str::limit((string) $request->query('q', ''), 60, ''));

        if (mb_strlen($kata) < 2) {
            return response()->json(['total' => 0, 'data' => []]);
        }

        try {
            $semua = $this->dataGereja();
        } catch (Throwable $e) {
            Log::warning('Gagal mengambil data gereja dari HMMinistry: '.$e->getMessage());

            return response()->json(['message' => 'Sumber data sedang tidak bisa dihubungi.'], 503);
        }

        $cocok = [];
        foreach ($semua as $gereja) {
            $skor = $this->skor($gereja, $kata);
            if ($skor > 0) {
                $cocok[] = [$skor, $gereja];
            }
        }

        // Paling relevan dulu (nama/kota diawali kata yang dicari), lalu urut nama.
        usort($cocok, fn ($a, $b) => [$b[0], $a[1]['nama']] <=> [$a[0], $b[1]['nama']]);

        return response()
            ->json([
                'total' => count($cocok),
                'data' => array_map(fn ($c) => $c[1], array_slice($cocok, 0, self::MAKS_HASIL)),
            ])
            ->header('Cache-Control', 'public, max-age=300');
    }

    /** Semua gereja + jadwalnya, dirapikan, disimpan sementara di cache file. */
    private function dataGereja(): array
    {
        $cache = Cache::store('file');
        $data = $cache->get(self::KUNCI_CACHE);

        if (is_array($data)) {
            return $data;
        }

        $config = config('gatsu.hmministry');
        $http = Http::timeout(30)->retry(1, 500)->withOptions(['verify' => $config['verify_ssl']])->acceptJson();

        $cabangs = $http->get($config['cabang_url'])->throw()->json();
        $kebaktians = $http->get($config['kebaktian_url'])->throw()->json();

        if (! is_array($cabangs) || ! is_array($kebaktians)) {
            throw new \RuntimeException('Format data HMMinistry tidak dikenali.');
        }

        $jadwal = [];
        foreach ($kebaktians as $k) {
            if (! empty($k['kdcabang'])) {
                $jadwal[$k['kdcabang']][] = [
                    'nama' => trim((string) ($k['nmkebaktian'] ?? '')),
                    'jam' => trim((string) ($k['jamkebaktian'] ?? '')),
                    'alamat' => trim((string) ($k['Alamat'] ?? '')),
                ];
            }
        }

        $data = [];
        foreach ($cabangs as $c) {
            if (empty($c['nmcabang'])) {
                continue;
            }

            $ibadah = $jadwal[$c['kdcabang'] ?? ''] ?? [];
            usort($ibadah, fn ($a, $b) => strcmp($a['jam'], $b['jam']));

            $alamat = trim((string) ($c['Alamat'] ?? ''));
            $kota = trim((string) ($c['Kotakab'] ?? ''));
            $provinsi = trim((string) ($c['Provinsi'] ?? ''));
            $negara = trim((string) ($c['Negara'] ?? ''));

            $data[] = [
                'nama' => trim((string) $c['nmcabang']),
                'alamat' => $alamat,
                'kota' => $kota,
                'provinsi' => $provinsi,
                'negara' => $negara,
                'benua' => trim((string) ($c['Benua'] ?? '')),
                'telpon' => trim((string) ($c['Telpon'] ?? '')),
                'jadwal' => $ibadah,
                'tautan' => array_filter([
                    'maps' => 'https://www.google.com/maps/search/?api=1&query='.rawurlencode(implode(', ', array_filter([$c['nmcabang'], $alamat, $kota, $negara]))),
                    'instagram' => $this->url($c['Ig'] ?? null),
                    'youtube' => $this->url($c['Yutube'] ?? null),
                    'facebook' => $this->url($c['Fb'] ?? null),
                    'website' => $this->url($c['website'] ?? null),
                ]),
            ];
        }

        $cache->put(self::KUNCI_CACHE, $data, (int) config('gatsu.hmministry.cache_ttl', 1800));

        return $data;
    }

    /** 0 = tidak cocok. Makin besar makin relevan. */
    private function skor(array $g, string $kata): int
    {
        $skor = 0;
        foreach (['nama' => 6, 'kota' => 5, 'provinsi' => 3, 'negara' => 2, 'benua' => 1, 'alamat' => 1] as $kolom => $bobot) {
            $isi = $g[$kolom] ?? '';
            if ($isi === '' || ($posisi = mb_stripos($isi, $kata)) === false) {
                continue;
            }
            $skor = max($skor, $bobot * ($posisi === 0 ? 2 : 1));
        }

        return $skor;
    }

    /** Hanya tautan http(s) yang valid yang diteruskan ke browser. */
    private function url(?string $nilai): ?string
    {
        $nilai = trim((string) $nilai);
        if ($nilai === '') {
            return null;
        }
        if (! preg_match('#^https?://#i', $nilai)) {
            $nilai = 'https://'.ltrim($nilai, '/');
        }

        return filter_var($nilai, FILTER_VALIDATE_URL) ? $nilai : null;
    }
}
