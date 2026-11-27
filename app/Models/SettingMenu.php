<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Pengaturan akses user (tabel `setting_menu`, sama dengan website lama).
 * Satu baris = satu user dengan satu role, plus status 'active' / 'inactive'.
 * Menu apa saja yang boleh dibuka (dan hak tambah/edit/hapus) ada di tabel `cekrole` (model MenuAkses),
 * dengan user_id + role_id yang sama. Kolom menu_id di sini wajib diisi; diisi menu pertama yang dicentang.
 */
class SettingMenu extends Model
{
    public const AKTIF = 'active';

    public const NONAKTIF = 'inactive';

    protected $table = 'setting_menu';

    protected $fillable = ['user_id', 'role_id', 'menu_id', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', self::AKTIF);
    }

    public function aktif(): bool
    {
        return $this->status === self::AKTIF;
    }

    /**
     * Hak per menu untuk baris ini: [menu_id => ['list' => bool, 'add' => bool, 'edit' => bool, 'delete' => bool]].
     * Data lama: menu_id di setting_menu yang tidak punya baris cekrole dianggap boleh semua.
     */
    public function hakPerMenu(): array
    {
        $hasil = [];

        $baris = MenuAkses::query()->where('user_id', $this->user_id)->where('role_id', $this->role_id)->get();
        foreach ($baris as $b) {
            $hasil[$b->menu_id] = $b->hak();
        }

        if ($this->menu_id && ! isset($hasil[$this->menu_id])) {
            $hasil[$this->menu_id] = array_fill_keys(MenuAkses::HAK, true);
        }

        return $hasil;
    }
}
