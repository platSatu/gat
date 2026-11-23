<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bahasa website publik (tombol bendera di header).
 * Pilihan pengunjung disimpan di session; bawaannya Bahasa Indonesia.
 * Teks Inggris ada di lang/en.json (kuncinya = teks Indonesia di __('...')).
 */
class SetLocale
{
    public const SUPPORTED = ['id', 'en'];

    public const DEFAULT = 'id';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale', self::DEFAULT);

        app()->setLocale(in_array($locale, self::SUPPORTED, true) ? $locale : self::DEFAULT);

        return $next($request);
    }
}
