<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Kategori artikel (tabel `categories`). */
class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = ['name', 'slug'];

    /** Keterangan singkat kategori (dari desain Figma). Kategori lain belum punya keterangan. */
    public const DESKRIPSI = [
        'Mengenal Yesus' => 'Langkah awal untuk mengenal Yesus dan memahami iman Kristen',
        'Gatsu News' => 'Berita dan liputan untuk mengikuti perkembangan kegiatan serta pelayanan gereja-gereja di bawah naungan GBI Gatsu',
        'Renungan' => 'Bacaan harian untuk merenungkan firman Tuhan dan menemukan maknanya dalam keseharian',
        'Pesan Gembala' => 'Pesan dan arahan gembala sidang untuk menuntun langkah iman kita',
        'Teologi' => 'Kajian untuk mendalami Alkitab, teologi, dan doktrin Kristen',
        'Kehidupan Kristen' => 'Panduan untuk menerapkan iman dalam keluarga, pekerjaan, relasi, dan pilihan sehari-hari',
        'Kisah Inspiratif' => 'Kesaksian dan cerita kehidupan untuk menguatkan iman dan menumbuhkan pengharapan',
        'Komunitas' => 'Bahan belajar dan diskusi untuk bertumbuh bersama dalam komunitas',
    ];

    public function getDeskripsiAttribute(): ?string
    {
        return isset(self::DESKRIPSI[$this->name]) ? __(self::DESKRIPSI[$this->name]) : null;
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    /** Kategori yang punya artikel terbit, beserta jumlahnya (artikel_count), urut nama. */
    public static function untukArtikel()
    {
        return static::query()
            ->withCount(['articles as artikel_count' => fn ($q) => $q->terbit()])
            ->orderBy('name')
            ->get(['id', 'name'])
            ->where('artikel_count', '>', 0)
            ->values();
    }
}
