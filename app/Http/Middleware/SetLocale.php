<?php

namespace App\Http\Middleware;

use App\Models\Bahasa;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bahasa website publik (tombol bahasa di header).
 * Pilihan pengunjung disimpan di session; bawaannya Bahasa Indonesia.
 * Daftar bahasa dari tabel `bahasa` (admin Settings → Bahasa), teks dari tabel `terjemahan`.
 */
class SetLocale
{
    public const DEFAULT = Bahasa::UTAMA;

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale', self::DEFAULT);

        app()->setLocale(in_array($locale, Bahasa::kodeAktif(), true) ? $locale : self::DEFAULT);

        return $next($request);
    }
}
