<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuAkses;
use App\Models\SettingMenu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Settings → Menu (tabel `menus`, sama dengan website lama): daftar nama menu yang bisa diberikan ke user.
 * Menu yang dipakai admin v2 tercatat di config/admin-menu.php; tombol "sync" menambahkan yang belum ada.
 */
class MenuController extends Controller
{
    public function index(): View
    {
        $menus = Menu::query()->orderBy('id')->get();

        // Berapa user yang boleh membuka tiap menu (cekrole, hak list).
        $jumlahUser = MenuAkses::query()->where('list', 1)
            ->selectRaw('menu_id, count(distinct user_id) as jumlah')->groupBy('menu_id')->pluck('jumlah', 'menu_id');

        return view('backend.settings.menus.index', [
            'menus' => $menus,
            'jumlahUser' => $jumlahUser,
            'belumAda' => Menu::belumAda(),
        ]);
    }

    public function create(): View
    {
        return view('backend.settings.menus.form', ['menu' => new Menu]);
    }

    public function store(Request $request): RedirectResponse
    {
        $menu = Menu::create($this->validasi($request, new Menu));

        return redirect()->route('admin.settings.menus.index')->with('success', "Menu {$menu->name} berhasil dibuat.");
    }

    public function edit(Menu $menu): View
    {
        return view('backend.settings.menus.form', compact('menu'));
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        if ($menu->dipakaiV2() && strcasecmp(trim($request->input('name', '')), trim($menu->name)) !== 0) {
            return back()->withInput()->with('error', 'Menu ini dipakai admin v2 (config/admin-menu.php). Ganti namanya di config dulu, baru di sini.');
        }

        $menu->update($this->validasi($request, $menu));

        return redirect()->route('admin.settings.menus.index')->with('success', "Menu {$menu->name} berhasil diperbarui.");
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $dipakai = MenuAkses::where('menu_id', $menu->id)->exists() || SettingMenu::where('menu_id', $menu->id)->exists();

        if ($dipakai) {
            return back()->with('error', "Menu {$menu->name} masih dipakai di Setting Menu. Hilangkan centangnya dari semua user dulu.");
        }

        $menu->delete();

        return redirect()->route('admin.settings.menus.index')->with('success', "Menu {$menu->name} berhasil dihapus.");
    }

    /** Tambahkan nama menu dari config/admin-menu.php yang belum ada di tabel menus. */
    public function sync(): RedirectResponse
    {
        $baru = Menu::belumAda();

        foreach ($baru as $nama) {
            Menu::create(['name' => $nama]);
        }

        return redirect()->route('admin.settings.menus.index')->with('success', $baru->isEmpty()
            ? 'Semua menu admin v2 sudah ada.'
            : 'Menu ditambahkan: '.$baru->implode(', ').'.');
    }

    private function validasi(Request $request, Menu $menu): array
    {
        $request->merge(['name' => trim((string) $request->input('name'))]);

        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('menus', 'name')->ignore($menu->id)],
        ], [], ['name' => 'Nama menu']);
    }
}
