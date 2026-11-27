<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\MenuAkses;
use App\Models\Role;
use App\Models\SettingMenu;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Settings → Daftar User (tabel `users`, sama dengan website lama).
 * Password disimpan ter-hash (bcrypt), sama seperti website lama, jadi user tetap bisa login di keduanya.
 */
class UserController extends Controller
{
    public function index(Request $request): View
    {
        $cari = trim((string) $request->input('search'));
        $roleId = $request->input('role');

        $users = User::query()->with('role')
            ->withCount(['settingMenus as jumlah_akses' => fn ($q) => $q->where('status', SettingMenu::AKTIF)])
            ->when($cari !== '', fn ($q) => $q->where(fn ($w) => $w->where('name', 'like', "%{$cari}%")->orWhere('email', 'like', "%{$cari}%")))
            ->when($roleId === 'none', fn ($q) => $q->whereNull('role_id'))
            ->when(is_numeric($roleId), fn ($q) => $q->where('role_id', $roleId))
            ->orderBy('id')
            ->paginate(15)->withQueryString();

        return view('backend.settings.users.index', [
            'users' => $users,
            'roles' => Role::query()->orderBy('name')->get(),
            'ringkasan' => [
                'total' => User::count(),
                'superadmin' => User::whereHas('role', fn ($q) => $q->where('name', Role::SUPERADMIN))->count(),
                'belum_verifikasi' => User::whereNull('email_verified_at')->count(),
                'tanpa_role' => User::whereNull('role_id')->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('backend.settings.users.form', ['user' => new User, 'roles' => Role::query()->orderBy('id')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request, new User);

        $user = User::create($data);
        if ($request->boolean('verified')) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        return redirect()->route('admin.settings.users.index')->with('success', "User {$user->name} berhasil dibuat.");
    }

    public function edit(User $user): View
    {
        return view('backend.settings.users.form', ['user' => $user, 'roles' => Role::query()->orderBy('id')->get()]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validasi($request, $user);

        if ($this->superadminTerakhir($user) && (int) $data['role_id'] !== (int) $user->role_id) {
            return back()->withInput()->with('error', 'Ini superadmin terakhir. Role-nya tidak bisa diganti.');
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.settings.users.index')->with('success', "User {$user->name} berhasil diperbarui.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        if ($this->superadminTerakhir($user)) {
            return back()->with('error', 'Ini superadmin terakhir, tidak bisa dihapus.');
        }

        DB::transaction(function () use ($user) {
            // cekrole tidak punya foreign key, jadi dihapus manual. setting_menu ikut terhapus (foreign key), dihapus juga untuk jaga-jaga.
            MenuAkses::where('user_id', $user->id)->delete();
            SettingMenu::where('user_id', $user->id)->delete();
            $user->delete();
        });

        return redirect()->route('admin.settings.users.index')->with('success', "User {$user->name} berhasil dihapus.");
    }

    /** Tandai email sudah diverifikasi (sama seperti tombol "Sudah Diverifikasi" di website lama). */
    public function verify(User $user): RedirectResponse
    {
        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        return back()->with('success', "Email {$user->email} ditandai sudah diverifikasi.");
    }

    private function validasi(Request $request, User $user): array
    {
        $request->merge(['email' => mb_strtolower(trim((string) $request->input('email')))]);

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => [$user->exists ? 'nullable' : 'required', 'confirmed', Password::min(8)],
            'role_id' => ['nullable', 'integer', Rule::exists('roles', 'id')],
        ], [], [
            'name' => 'Nama', 'email' => 'Email', 'password' => 'Password', 'role_id' => 'Role',
        ]);
    }

    private function superadminTerakhir(User $user): bool
    {
        return $user->isSuperadmin()
            && User::whereHas('role', fn ($q) => $q->where('name', Role::SUPERADMIN))->count() <= 1;
    }
}
