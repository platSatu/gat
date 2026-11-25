<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Highlights (tabel `highlights` di database lama): slider "Kegiatan" di beranda + halaman /highlights/{slug}.
 * Tabel tidak punya kolom slug, jadi slug dibuat dari judul (mis. "Everyone Asia 2024" -> everyone-asia-2024).
 * Kolom gambar/video berupa path relatif dari folder public website lama, mis. "uploads/ps1.jpg".
 * image1..image8 = foto dokumentasi, bghighlights = gambar latar/banner, video = video rekap.
 */
class Highlight extends Model
{
    protected $table = 'highlights';

    protected $fillable = [
        'title', 'description', 'video',
        'image1', 'image2', 'image3', 'image4', 'image5', 'image6', 'image7', 'image8',
        'bghighlights', 'date_post', 'status',
    ];

    protected $casts = [
        'status' => 'integer',
        'date_post' => 'date',
    ];

    public const KOLOM_FOTO = ['image1', 'image2', 'image3', 'image4', 'image5', 'image6', 'image7', 'image8'];

    /** Slug dari judul; kalau judul kosong/tidak bisa dijadikan slug, pakai ID. */
    public function getSlugAttribute(): string
    {
        return Str::slug((string) $this->title) ?: (string) $this->getKey();
    }

    /** route('highlights.show', $highlight) menghasilkan /highlights/{slug}. */
    public function getRouteKey(): mixed
    {
        return $this->slug;
    }

    /** Halaman publik: cari highlight aktif dari slug di URL. Jumlah highlight sedikit, jadi dicocokkan langsung dari judul. */
    public function resolveRouteBinding($value, $field = null): ?self
    {
        // Admin memakai {highlight:id} -> cari langsung berdasarkan kolom itu (termasuk yang tidak aktif).
        if ($field) {
            return static::where($field, $value)->first();
        }

        $cocok = static::tampil()->terbaru()->get(['id', 'title'])
            ->first(fn (self $h) => $h->slug === (string) $value);

        return $cocok ? static::find($cocok->getKey()) : null;
    }

    /** Hanya yang aktif (status = 1). */
    public function scopeTampil(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function scopeTerbaru(Builder $query): Builder
    {
        return $query->orderByDesc('date_post')->orderByDesc('id');
    }

    /** Gambar sampul kartu: foto pertama, kalau kosong pakai gambar latar. */
    public function getImageUrlAttribute(): ?string
    {
        return Article::urlPublik($this->image1 ?: $this->bghighlights);
    }

    public function getBannerUrlAttribute(): ?string
    {
        return Article::urlPublik($this->bghighlights ?: $this->image1);
    }

    public function getVideoUrlAttribute(): ?string
    {
        return Article::urlPublik($this->video);
    }

    /** URL semua foto dokumentasi yang terisi (image1..image8). */
    public function getFotoAttribute(): array
    {
        return collect(self::KOLOM_FOTO)
            ->map(fn ($kolom) => Article::urlPublik($this->{$kolom}))
            ->filter()
            ->values()
            ->all();
    }

    /** Isi (HTML dari admin lama), dibersihkan seperti isi artikel. */
    public function getIsiAttribute(): string
    {
        return (new Article(['description' => $this->description]))->isi;
    }
}
