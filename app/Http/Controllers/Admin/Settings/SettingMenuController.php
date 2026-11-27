<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuAkses;
use App\Models\Role;
use App\Models\SettingMenu;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Settings → Setting Menu: menu apa saja yang boleh dibuka seorang user (per role).
 * Format penyimpanan SAMA dengan website lama:
 *   - setting_menu : 1 baris per user + role (status active/inactive; menu_id = menu pertama yang dicentang)
 *   - cekrole      : 1 baris per user + role + menu, kolom list/add/edit/delete = 1/0
 */
class SettingMenuController extends Controller
{
    public function index(Request $request): View
    {
        $cari = trim((string) $request->input('search'));

        $settings = SettingMenu::query()->with(['user', 'role'])
            ->when($cari !== '', fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$cari}%")->orWhere('email', 'like', "%{$cari}%")))
            ->when(is_numeric($request->input('role')), fn ($q) => $q->where('role_id', $request->input('role')))
            ->when(in_array($request->input('status'), [SettingMenu::AKTIF, SettingMenu::NONAKTIF], true), fn ($q) => $q->where('status', $request->input('status')))
            ->orderBy('user_id')->orderBy('id')
            ->paginate(20)->withQueryString();

        // Nama menu yang dicentang (hak "list") untuk setiap baris di halaman ini.
        $namaMenu = Menu::query()->pluck('name', 'id');
        $cek = MenuAkses::query()->whereIn('user_id', $settings->pluck('user_id')->unique())->get()
            ->groupBy(fn ($c) => $c->user_id.'-'.$c->role_id);

        $menuPerBaris = [];
        foreach ($settings as $s) {
            $ids = ($cek[$s->user_id.'-'.$s->role_id] ?? collect())->where('list', 1)->pluck('menu_id');
            if ($s->menu_id && ! ($cek[$s->user_id.'-'.$s->role_id] ?? collect())->contains('menu_id', $s->menu_id)) {
                $ids->push($s->menu_id);
            }
            $menuPerBaris[$s->id] = $ids->unique()->map(fn ($id) => $namaMenu[$id] ?? "#$id")->sort()->values();
        }

        return view('backend.settings.setting-menu.index', [
            'settings' => $settings,
            'menuPerBaris' => $menuPerBaris,
            'roles' => Role::query()->orderBy('name')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('backend.settings.setting-menu.form', $this->dataForm(
            new SettingMenu(['status' => SettingMenu::AKTIF, 'user_id' => $request->integer('user') ?: null]),
            []
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        [$data, $akses] = $this->validasi($request);

        if (SettingMenu::where('user_id', $data['user_id'])->where('role_id', $data['role_id'])->exists()) {
            return back()->withInput()->with('error', 'User ini sudah punya Setting Menu dengan role tersebut. Edit yang sudah ada saja.');
        }

        DB::transaction(function () use ($data, $akses) {
            SettingMenu::create($data + ['menu_id' => array_key_first($akses)]);
            $this->simpanAkses($data['user_id'], $data['role_id'], $akses);
        });

        return redirect()->route('admin.settings.setting-menu.index')->with('success', 'Setting menu berhasil disimpan.');
    }

    public function edit(SettingMenu $settingMenu): View
    {
        return view('backend.settings.setting-menu.form', $this->dataForm($settingMenu->load('user'), $settingMenu->hakPerMenu()));
    }

    public function update(Request $request, SettingMenu $settingMenu): RedirectResponse
    {
        $request->merge(['user_id' => $settingMenu->user_id]);   // user tidak bisa diganti di form edit
        [$data, $akses] = $this->validasi($request);

        $bentrok = SettingMenu::where('user_id', $data['user_id'])->where('role_id', $data['role_id'])
            ->whereKeyNot($settingMenu->id)->exists();
        if ($bentrok) {
            return back()->withInput()->with('error', 'User ini sudah punya Setting Menu lain dengan role tersebut.');
        }

        DB::transaction(function () use ($settingMenu, $data, $akses) {
            MenuAkses::where('user_id', $settingMenu->user_id)->where('role_id', $settingMenu->role_id)->delete();
            $settingMenu->update($data + ['menu_id' => array_key_first($akses)]);
            $this->simpanAkses($data['user_id'], $data['role_id'], $akses);
        });

        return redirect()->route('admin.settings.setting-menu.index')->with('success', 'Setting menu berhasil diperbarui.');
    }

    public function destroy(SettingMenu $settingMenu): RedirectResponse
    {
        DB::transaction(function () use ($settingMenu) {
            MenuAkses::where('user_id', $settingMenu->user_id)->where('role_id', $settingMenu->role_id)->delete();
            $settingMenu->delete();
        });

        return redirect()->route('admin.settings.setting-menu.index')->with('success', 'Setting menu berhasil dihapus.');
    }

    private function dataForm(SettingMenu $setting, array $hak): array
    {
        return [
            'setting' => $setting,
            'hak' => $hak,
            'users' => User::query()->with('role')->orderBy('name')->get(['id', 'name', 'email', 'role_id']),
            'roles' => Role::query()->orderBy('id')->get(),
            'menus' => Menu::query()->orderBy('id')->get(),
        ];
    }

    /** @return array{0: array, 1: array<int, array<string, int>>} [data setting_menu, [menu_id => [list, add, edit, delete]]] */
    private function validasi(Request $request): array
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')],
            'status' => ['required', Rule::in([SettingMenu::AKTIF, SettingMenu::NONAKTIF])],
            'akses' => ['nullable', 'array'],
            'akses.*' => ['array'],
        ], [], ['user_id' => 'User', 'role_id' => 'Role']);

        $idMenu = Menu::query()->pluck('id')->all();
        $akses = [];
        foreach ((array) $request->input('akses', []) as $menuId => $centang) {
            if (! in_array((int) $menuId, $idMenu, true)) {
                continue;
            }
            $baris = collect(MenuAkses::HAK)->mapWithKeys(fn ($h) => [$h => ! empty($centang[$h]) ? 1 : 0])->all();
            // Menu hanya diberikan kalau "Lihat" (list) dicentang; tanpa Lihat, menu tidak bisa dibuka sama sekali.
            if ($baris['list'] === 1) {
                $akses[(int) $menuId] = $baris;
            }
        }

        if ($akses === []) {
            throw ValidationException::withMessages(['akses' => 'Centang minimal satu menu.']);
        }

        unset($data['akses']);

        return [$data, $akses];
    }

    private function simpanAkses(int $userId, int $roleId, array $akses): void
    {
        $now = now();
        MenuAkses::insert(collect($akses)->map(fn ($hak, $menuId) => [
            'user_id' => $userId, 'role_id' => $roleId, 'menu_id' => $menuId,
            'created_at' => $now, 'updated_at' => $now,
        ] + $hak)->values()->all());
    }
}
