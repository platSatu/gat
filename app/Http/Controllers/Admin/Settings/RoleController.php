<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\MenuAkses;
use App\Models\Role;
use App\Models\SettingMenu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Settings → Roles (tabel `roles`, sama dengan website lama). Role "superadmin" dikunci (tidak bisa diganti nama/dihapus).
 */
class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()
            ->withCount(['users', 'settingMenus as jumlah_setting'])
            ->orderBy('id')->get();

        return view('backend.settings.roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('backend.settings.roles.form', ['role' => new Role]);
    }

    public function store(Request $request): RedirectResponse
    {
        $role = Role::create($this->validasi($request, new Role));

        return redirect()->route('admin.settings.roles.index')->with('success', "Role {$role->name} berhasil dibuat.");
    }

    public function edit(Role $role): View
    {
        return view('backend.settings.roles.form', compact('role'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        if ($role->isSuperadmin()) {
            return back()->with('error', 'Role superadmin tidak bisa diganti namanya.');
        }

        $role->update($this->validasi($request, $role));

        return redirect()->route('admin.settings.roles.index')->with('success', "Role {$role->name} berhasil diperbarui.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->isSuperadmin()) {
            return back()->with('error', 'Role superadmin tidak bisa dihapus.');
        }

        DB::transaction(function () use ($role) {
            // users.role_id otomatis jadi kosong (foreign key ON DELETE SET NULL). cekrole tidak punya foreign key → hapus manual.
            MenuAkses::where('role_id', $role->id)->delete();
            SettingMenu::where('role_id', $role->id)->delete();
            $role->delete();
        });

        return redirect()->route('admin.settings.roles.index')->with('success', "Role {$role->name} berhasil dihapus.");
    }

    private function validasi(Request $request, Role $role): array
    {
        $request->merge(['name' => trim((string) $request->input('name'))]);

        return $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9_\-]+$/', Rule::unique('roles', 'name')->ignore($role->id)],
        ], [
            'name.regex' => 'Nama role hanya huruf, angka, garis bawah (_) atau strip (-), tanpa spasi. Contoh: admin_media',
        ], ['name' => 'Nama role']);
    }
}
