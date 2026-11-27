<?php

namespace App\Models\Concerns;

use App\Models\Menu;
use App\Models\MenuAkses;
use App\Models\Role;
use App\Models\SettingMenu;
use Illuminate\Support\Str;

/**
 * Hak akses menu admin untuk user (dipakai App\Models\User).
 *
 * Aturan (sama dengan website lama, tabel yang sama):
 *   - Role user (users.role_id) = superadmin → boleh semua menu.
 *   - Selain itu: ambil baris setting_menu milik user yang status-nya 'active',
 *     lalu hak per menu dari tabel cekrole (user_id + role_id yang sama).
 *     Kalau user punya beberapa role aktif, haknya digabung.
 *
 * Pemakaian: $user->bolehMenu('Page-Article')  atau  $user->bolehMenu('Page-Article', 'delete')
 */
trait PunyaAksesMenu
{
    /** Cache per request: [nama menu (huruf kecil) => ['list' => bool, ...]] */
    private ?array $hakMenuCache = null;

    public function isSuperadmin(): bool
    {
        return $this->role?->name === Role::SUPERADMIN;
    }

    /** @param  string  $hak  list | add | edit | delete */
    public function bolehMenu(?string $menu, string $hak = 'list'): bool
    {
        if ($this->isSuperadmin()) {
            return true;
        }

        if (blank($menu)) {
            return false;
        }

        return (bool) ($this->hakMenu()[mb_strtolower(trim($menu))][$hak] ?? false);
    }

    /** Ada minimal satu menu yang boleh dibuka? */
    public function punyaAksesAdmin(): bool
    {
        return $this->isSuperadmin() || collect($this->hakMenu())->contains(fn ($h) => $h['list']);
    }

    public function hakMenu(): array
    {
        if ($this->hakMenuCache !== null) {
            return $this->hakMenuCache;
        }

        $perId = [];
        foreach (SettingMenu::query()->aktif()->where('user_id', $this->getKey())->get() as $setting) {
            foreach ($setting->hakPerMenu() as $menuId => $hak) {
                foreach (MenuAkses::HAK as $h) {
                    $perId[$menuId][$h] = ($perId[$menuId][$h] ?? false) || $hak[$h];
                }
            }
        }

        $nama = Menu::query()->whereKey(array_keys($perId))->pluck('name', 'id');

        $hasil = [];
        foreach ($perId as $menuId => $hak) {
            if (isset($nama[$menuId])) {
                $hasil[Str::lower(trim($nama[$menuId]))] = $hak;
            }
        }

        return $this->hakMenuCache = $hasil;
    }
}
