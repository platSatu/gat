<?php

namespace App\Support;

use App\Models\Terjemahan;
use Illuminate\Contracts\Translation\Loader;

/**
 * Pembungkus loader bahasa bawaan Laravel: teks __('...') juga dibaca dari tabel `terjemahan`.
 * Urutan: file lang/*.json (kalau masih ada) lalu ditimpa isi database (database menang).
 * File bahasa Laravel lainnya (validation, pagination, ...) tetap dari file seperti biasa.
 */
class TerjemahanLoader implements Loader
{
    public function __construct(private Loader $asli)
    {
    }

    public function load($locale, $group, $namespace = null)
    {
        $hasil = $this->asli->load($locale, $group, $namespace);

        if ($group === '*' && $namespace === '*') {
            $hasil = array_merge($hasil, Terjemahan::peta($locale));
        }

        return $hasil;
    }

    public function addNamespace($namespace, $hint)
    {
        $this->asli->addNamespace($namespace, $hint);
    }

    public function addJsonPath($path)
    {
        $this->asli->addJsonPath($path);
    }

    public function namespaces()
    {
        return $this->asli->namespaces();
    }

    public function __call($method, $args)
    {
        return $this->asli->{$method}(...$args);
    }
}
