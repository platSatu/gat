<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Nama menu/halaman admin yang bisa diberikan ke user (tabel `menus`, sama dengan website lama),
 * mis. "Page-Article". Menu v2 didaftarkan di config/admin-menu.php.
 */
class Menu extends Model
{
    protected $fillable = ['name'];

    /** Nama menu yang dipakai admin v2 (dari config/admin-menu.php), tanpa duplikat. */
    public static function namaDipakaiV2(): Collection
    {
        return collect(config('admin-menu', []))->flatten(1)
            ->pluck('menu')->filter()->unique()->values();
    }

    /** Nama menu v2 yang belum ada di tabel `menus`. */
    public static function belumAda(): Collection
    {
        $ada = static::query()->pluck('name')->map(fn ($n) => mb_strtolower(trim($n)))->all();

        return static::namaDipakaiV2()->reject(fn ($n) => in_array(mb_strtolower($n), $ada, true))->values();
    }

    public function dipakaiV2(): bool
    {
        return static::namaDipakaiV2()->contains(fn ($n) => strcasecmp($n, trim($this->name)) === 0);
    }
}
