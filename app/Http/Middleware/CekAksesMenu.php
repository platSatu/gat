<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Batasi halaman admin sesuai Settings → Setting Menu (tabel setting_menu + cekrole).
 *
 * - Dashboard admin: semua user yang login.
 * - Route yang terdaftar di config/admin-menu.php: dicek hak menunya.
 *     index/show = list, create/store = add, edit/update/move/latar = edit, destroy = delete.
 * - Route admin lain yang TIDAK terdaftar: hanya superadmin (aman secara default).
 */
class CekAksesMenu
{
    private const HAK_PER_AKSI = [
        'index' => 'list', 'show' => 'list',
        'create' => 'add', 'store' => 'add',
        'edit' => 'edit', 'update' => 'edit', 'move' => 'edit', 'latar' => 'edit',
        'destroy' => 'delete',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $route = (string) $request->route()?->getName();

        if ($route === 'admin.dashboard' || $user?->isSuperadmin()) {
            return $next($request);
        }

        $item = self::itemUntukRoute($route);

        if ($item === null || ! empty($item['superadmin'])) {
            abort(403, 'Halaman ini hanya untuk superadmin.');
        }

        $hak = self::HAK_PER_AKSI[Str::afterLast($route, '.')] ?? 'list';

        abort_unless($user->bolehMenu($item['menu'] ?? null, $hak), 403,
            'Anda tidak punya akses '.($hak === 'list' ? '' : "\"$hak\" ").'ke menu ini. Hubungi superadmin.');

        return $next($request);
    }

    /** Item config/admin-menu.php yang pola 'active'-nya cocok dengan nama route. */
    public static function itemUntukRoute(string $route): ?array
    {
        foreach (config('admin-menu', []) as $items) {
            foreach ($items as $item) {
                if (Str::is($item['active'], $route)) {
                    return $item;
                }
            }
        }

        return null;
    }
}
