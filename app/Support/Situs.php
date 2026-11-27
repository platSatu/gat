<?php

namespace App\Support;

use App\Models\Bahasa;
use App\Models\Pengaturan;
use Illuminate\Support\Str;

/**
 * Pengaturan Situs & SEO untuk halaman publik (diisi di admin Settings → Situs & SEO, tabel `pengaturan`).
 * Semua bacaan punya nilai bawaan, jadi website tetap jalan walau pengaturan belum diisi.
 */
class Situs
{
    /** Kunci pengaturan media sosial => label. */
    public const SOSIAL = [
        'sosial_facebook' => 'Facebook',
        'sosial_instagram' => 'Instagram',
        'sosial_youtube' => 'Youtube',
        'sosial_tiktok' => 'TikTok',
        'sosial_whatsapp' => 'WhatsApp',
    ];

    public static function nama(): string
    {
        return Pengaturan::ambil('situs_nama') ?: (string) config('app.name');
    }

    /** Judul tab browser untuk beranda (per bahasa, cadangan: Indonesia, lalu nama website). */
    public static function judulBeranda(): string
    {
        return self::perBahasa('seo_judul') ?: self::nama();
    }

    public static function deskripsi(): string
    {
        return self::perBahasa('seo_deskripsi')
            ?: __('GBI Jl. Jend. Gatot Subroto — gereja lokal di bawah naungan Sinode Gereja Bethel Indonesia.');
    }

    public static function kataKunci(): ?string
    {
        return Pengaturan::ambil('seo_kata_kunci');
    }

    /** URL lengkap gambar share (OG image). */
    public static function ogGambar(): ?string
    {
        $path = Pengaturan::ambil('og_gambar');

        return $path ? asset($path) : null;
    }

    public static function favicon(): string
    {
        return asset(Pengaturan::ambil('favicon') ?: 'images/logo/96.png');
    }

    /**
     * Link media sosial yang diisi: [label => url]. Kalau belum ada yang diisi sama sekali, dipakai link bawaan lama.
     */
    public static function sosial(): array
    {
        $hasil = [];
        foreach (self::SOSIAL as $kunci => $label) {
            if ($url = Pengaturan::ambil($kunci)) {
                $hasil[$label] = $kunci === 'sosial_whatsapp' && ! Str::startsWith($url, 'http')
                    ? 'https://wa.me/'.preg_replace('/\D/', '', preg_replace('/^0/', '62', $url))
                    : $url;
            }
        }

        return $hasil ?: [
            'Facebook' => 'https://www.facebook.com/',
            'Instagram' => 'https://www.instagram.com/',
            'Youtube' => 'https://www.youtube.com/',
        ];
    }

    /**
     * Apakah ini domain utama (gbigatsu.id / www.gbigatsu.id)?
     * Di domain lain (demo, localhost) halaman otomatis noindex dan skrip analytics tidak dimuat,
     * supaya Google tidak mengindeks demo dan data analytics tidak tercampur.
     */
    public static function domainUtama(): bool
    {
        $domain = strtolower((string) config('gatsu.statistik.domain', 'gbigatsu.id'));
        $host = strtolower(request()->getHost());

        return $host === $domain || $host === 'www.'.$domain;
    }

    public static function noindex(): bool
    {
        return Pengaturan::ambil('noindex') === '1' || ! self::domainUtama();
    }

    public static function pakaiTracking(): bool
    {
        return self::domainUtama();
    }

    public static function ambil(string $kunci): ?string
    {
        return Pengaturan::ambil($kunci);
    }

    /** Nilai per bahasa (kunci_{kode}), cadangan: Indonesia. */
    private static function perBahasa(string $kunci): ?string
    {
        return Pengaturan::ambil($kunci.'_'.app()->getLocale()) ?: Pengaturan::ambil($kunci.'_'.Bahasa::UTAMA);
    }
}
