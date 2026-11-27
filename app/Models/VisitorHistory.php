<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Catatan kunjungan halaman (tabel `visitor_histories`, dipakai bersama website lama; ±2 juta baris).
 * Satu baris = satu halaman yang dibuka. page_url berisi alamat lengkap (https://gbigatsu.id/...).
 * Tabel ini TIDAK punya index di visited_at, jadi query statistik memakai rentang id (lihat App\Support\StatistikDashboard).
 */
class VisitorHistory extends Model
{
    protected $table = 'visitor_histories';

    protected $fillable = [
        'ip_address', 'browser', 'page_url', 'visited_at', 'method', 'referrer', 'status_code', 'request_data',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'status_code' => 'integer',
    ];

    /** Pola user agent robot/crawler (tidak dihitung sebagai pengunjung). Dipakai di PHP dan di REGEXP MySQL. */
    public const POLA_BOT = 'bot|crawl|spider|slurp|semrush|ahrefs|bytespider|petalbot|mj12|dataforseo|python|curl|wget|httpclient|okhttp|go-http|java/|headless|lighthouse|facebookexternalhit|preview|monitor|uptime|scanner';

    public static function bot(?string $userAgent): bool
    {
        return blank($userAgent) || (bool) preg_match('#'.self::POLA_BOT.'#i', $userAgent);
    }
}
