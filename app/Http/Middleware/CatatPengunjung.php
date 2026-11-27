<?php

namespace App\Http\Middleware;

use App\Models\VisitorHistory;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Mencatat kunjungan halaman publik ke tabel visitor_histories (format sama dengan website lama),
 * supaya grafik pengunjung di dashboard tetap jalan setelah website pindah ke versi 2.
 *
 * Dicatat SETELAH halaman terkirim (terminate), jadi tidak memperlambat pengunjung.
 * Tidak dicatat: admin/login, selain GET, permintaan AJAX/JSON, halaman error, dan robot.
 * Data form (request_data) tidak disimpan. Matikan lewat .env: CATAT_PENGUNJUNG=false
 */
class CatatPengunjung
{
    private const LEWATI = ['dashboard', 'dashboard/*', 'admin', 'admin/*', 'auth/*', 'bahasa/*', 'gereja/cari', 'up', 'cek-database'];

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if (! config('gatsu.statistik.catat_pengunjung', true)
            || ! $request->isMethod('GET')
            || $request->ajax() || $request->expectsJson() || $request->prefetch()
            || $response->getStatusCode() !== 200
            || $request->is(...self::LEWATI)
            || VisitorHistory::bot($request->userAgent())) {
            return;
        }

        try {
            $now = now(config('gatsu.statistik.zona_waktu', 'Asia/Jakarta'))->toDateTimeString();   // WIB, sama dengan data lama
            VisitorHistory::query()->insert([
                'ip_address' => (string) $request->ip(),
                'browser' => Str::limit((string) $request->userAgent(), 1000, ''),
                'page_url' => Str::limit($request->fullUrl(), 255, ''),
                'visited_at' => $now,
                'method' => 'GET',
                'referrer' => Str::limit((string) $request->headers->get('referer'), 255, '') ?: null,
                'status_code' => 200,
                'request_data' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } catch (Throwable $e) {
            report($e);   // gagal mencatat tidak boleh mengganggu website
        }
    }
}
