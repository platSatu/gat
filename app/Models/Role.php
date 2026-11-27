<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Role user (tabel `roles`, sama dengan website lama). Hanya punya kolom name (unik).
 * Role bernama "superadmin" boleh membuka semua menu admin.
 */
class Role extends Model
{
    public const SUPERADMIN = 'superadmin';

    protected $fillable = ['name'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function settingMenus(): HasMany
    {
        return $this->hasMany(SettingMenu::class);
    }

    public function isSuperadmin(): bool
    {
        return $this->name === self::SUPERADMIN;
    }
}
