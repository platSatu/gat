<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Highlights (tabel `highlights` di database lama): slider "Kegiatan" di beranda + halaman /media/{id}.
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
