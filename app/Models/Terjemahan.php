<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Terjemahan teks tampilan (tabel `terjemahan`). Di kode teks ditulis __('Teks Indonesia');
 * kunci = teks Indonesia itu, isi = ['en' => '...', 'id' => 'pengganti teks Indonesia (opsional)', ...].
 * Dibaca oleh App\Support\TerjemahanLoader (dengan cache) setiap kali __() dipanggil.
 */
class Terjemahan extends Model
{
    protected $table = 'terjemahan';

    protected $fillable = ['hash', 'kunci', 'isi', 'sumber'];

    protected $casts = ['isi' => 'array'];

    private static array $petaCache = [];

    protected static function booted(): void
    {
        static::saving(function (Terjemahan $t) {
            $t->hash = md5((string) $t->kunci);
            // Buang isian kosong supaya "belum diterjemahkan" bisa dicari.
            $t->isi = collect($t->isi ?? [])->map(fn ($v) => is_string($v) ? trim($v) : $v)->filter(fn ($v) => filled($v))->all();
        });
        static::saved(fn () => static::lupakan());
        static::deleted(fn () => static::lupakan());
    }

    public static function lupakan(): void
    {
        static::$petaCache = [];
        foreach (array_unique([...Bahasa::kodeAktif(), Bahasa::UTAMA]) as $kode) {
            Cache::forget('terjemahan.peta.'.$kode);
        }
    }

    /** [kunci => teks] untuk satu bahasa (yang terisi saja). */
    public static function peta(string $kode): array
    {
        return static::$petaCache[$kode] ??= Cache::rememberForever('terjemahan.peta.'.$kode, function () use ($kode) {
            try {
                if (! Schema::hasTable('terjemahan')) {
                    return [];
                }

                $hasil = [];
                foreach (static::query()->whereNotNull('isi')->cursor() as $t) {
                    if (filled($t->isi[$kode] ?? null)) {
                        $hasil[$t->kunci] = $t->isi[$kode];
                    }
                }

                return $hasil;
            } catch (Throwable $e) {
                report($e);

                return [];
            }
        });
    }

    /** Teks untuk satu bahasa (atau null kalau kosong). */
    public function teks(string $kode): ?string
    {
        return filled($this->isi[$kode] ?? null) ? $this->isi[$kode] : null;
    }

    /**
     * Cari teks __('...') / @lang('...') / trans('...') di semua file tampilan dan kode,
     * lalu daftarkan yang belum ada. Mengembalikan jumlah teks baru.
     */
    public static function pindai(): int
    {
        $ditemukan = [];
        $folder = [resource_path('views'), app_path()];

        foreach ($folder as $dir) {
            foreach (File::allFiles($dir) as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }
                // Halaman admin tidak ikut diterjemahkan.
                $relatif = str_replace('\\', '/', $file->getRelativePathname());
                if (str_starts_with($relatif, 'backend/') || str_starts_with($relatif, 'Http/Controllers/Admin/')) {
                    continue;
                }

                preg_match_all('/(?:__|@lang|trans)\(\s*([\'"])((?:\\\\.|(?!\1).)*?)\1\s*[,)]/s', $file->getContents(), $m, PREG_SET_ORDER);
                foreach ($m as $cocok) {
                    $teks = stripcslashes($cocok[2]);
                    // Lewati kunci file bahasa Laravel (mis. "validation.required", "pagination.next").
                    if ($teks === '' || preg_match('/^[a-z_]+\.[a-z_.]+$/', $teks)) {
                        continue;
                    }
                    $ditemukan[$teks] ??= str_replace('\\', '/', str_replace(base_path().DIRECTORY_SEPARATOR, '', $file->getPathname()));
                }
            }
        }

        $ada = static::query()->pluck('hash')->flip();
        $baru = 0;
        foreach ($ditemukan as $kunci => $sumber) {
            if (! isset($ada[md5($kunci)])) {
                static::create(['kunci' => $kunci, 'isi' => [], 'sumber' => $sumber]);
                $baru++;
            }
        }

        return $baru;
    }
}
