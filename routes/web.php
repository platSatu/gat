<?php

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\AboutSectionController as AdminAboutSectionController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\HighlightController as AdminHighlightController;
use App\Http\Controllers\Admin\PartnerController as AdminPartnerController;
use App\Http\Controllers\Admin\Settings\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\Settings\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\Settings\SettingMenuController as AdminSettingMenuController;
use App\Http\Controllers\Admin\Settings\UserController as AdminUserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\GerejaController;
use App\Http\Controllers\Frontend\HighlightController;
use Illuminate\Support\Facades\Route;

// Website publik. Beranda: slider highlights diambil dari tabel `highlights`.
Route::get('/', [HighlightController::class, 'home'])->name('home');

// Detail highlight: /highlights/{slug-judul}, mis. /highlights/everyone-asia-2024.
Route::get('highlights/{highlight}', [HighlightController::class, 'show'])->name('highlights.show');
// Link lama /media/{id} (website lama) dialihkan permanen ke /highlights/{slug}.
Route::get('media/{id}', [HighlightController::class, 'legacy'])->whereNumber('id')->name('highlights.legacy');

// Cari gereja & jadwal ibadah (bagian "Gereja Terdekat" di beranda). Data dari HMMinistry, lihat Frontend\GerejaController.
Route::get('gereja/cari', [GerejaController::class, 'cari'])->middleware('throttle:60,1')->name('gereja.cari');

// Ganti bahasa (tombol bendera di header): /bahasa/id atau /bahasa/en, lalu kembali ke halaman sebelumnya.
Route::get('bahasa/{locale}', function (string $locale) {
    session(['locale' => $locale]);

    return redirect()->back(fallback: route('home'));
})->whereIn('locale', \App\Http\Middleware\SetLocale::SUPPORTED)->name('locale');

Route::prefix('tentang')->name('about')->group(function () {
    Route::get('/', [AboutController::class, 'index']);   // blok dari tabel about_sections (admin: Tentang Kami)
    Route::view('sejarah', 'frontend.pages.tentang.sejarah')->name('.history');
    Route::view('dna-kami', 'frontend.pages.tentang.dna')->name('.dna');
    Route::get('{slug}', [AboutController::class, 'show'])->where('slug', '[a-z0-9-]+')->name('.show');
});

Route::view('pelayanan', 'frontend.pages.pelayanan.index')->name('ministry');
Route::view('pelayanan/graha-bina-asuh', 'frontend.pages.pelayanan.show')->name('ministry.show');

Route::prefix('kegiatan')->name('events')->group(function () {
    Route::get('/', [HighlightController::class, 'events']);   // highlight terbaru + Gatsu News dari database
    Route::view('galeri', 'frontend.pages.kegiatan.galeri')->name('.gallery');
    Route::view('galeri/foto', 'frontend.pages.kegiatan.foto')->name('.photo');
    Route::view('penghapusan-foto', 'frontend.pages.kegiatan.penghapusan')->name('.photo-request');
});

Route::get('pengajaran', [ArticleController::class, 'teaching'])->name('teaching');

Route::prefix('artikel')->name('articles')->group(function () {
    Route::get('/', [ArticleController::class, 'index']);                // data dari database
    Route::view('cari', 'frontend.pages.artikel.cari')->name('.search');
    Route::get('semua', [ArticleController::class, 'all'])->name('.all');                 // ?urut=populer
    Route::get('kategori', [ArticleController::class, 'categories'])->name('.categories');
    // /artikel/kategori/{id}: artikel dikelompokkan per kategori. Tanpa id = halaman semua kategori.
    Route::get('kategori/{category?}', [ArticleController::class, 'category'])->whereNumber('category')->name('.category');
    // Detail: /artikel/{slug}. Tanpa slug (link lama yang belum diganti) = artikel terbaru. Harus paling bawah.
    Route::get('{article?}', [ArticleController::class, 'show'])->name('.show');
});

Route::view('komunitas', 'frontend.pages.komunitas')->name('community');
Route::view('kontak', 'frontend.pages.kontak')->name('contact');
Route::view('memberi', 'frontend.pages.memberi')->name('giving');
Route::view('give', 'frontend.pages.give')->name('giving.en');

// Login admin: /auth/login
Route::prefix('auth')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'create'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->middleware('throttle:10,1');
    });

    Route::post('logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');
});

// Admin: /admin/...  Hak akses tiap menu diatur di Settings → Setting Menu (middleware akses.menu, config/admin-menu.php).
Route::prefix('admin')->name('admin.')->middleware(['auth', 'akses.menu'])->group(function () {
    Route::view('/', 'backend.dashboard')->name('dashboard');
    // Admin memakai ID di URL (slug artikel lama bisa kosong/ganda); halaman publik tetap memakai slug.
    Route::resource('articles', AdminArticleController::class)->except('show')->scoped(['article' => 'id']);
    Route::resource('categories', AdminCategoryController::class)->except('show');
    // Highlights: memakai ID di URL admin (slug publik dibuat dari judul).
    Route::resource('highlights', AdminHighlightController::class)->except('show')->scoped(['highlight' => 'id']);
    // Tentang Kami: blok halaman /tentang (ID di URL admin), plus tombol naik/turun urutan.
    Route::resource('about', AdminAboutSectionController::class)->except('show')->scoped(['about' => 'id']);
    Route::post('about/{about:id}/move/{arah}', [AdminAboutSectionController::class, 'move'])->whereIn('arah', ['up', 'down'])->name('about.move');
    // Pelayanan & Mitra: logo di beranda (ID di URL admin).
    Route::resource('partners', AdminPartnerController::class)->except('show');

    // Settings (hanya superadmin): user, role, menu, dan hak menu per user. Memakai tabel lama (users, roles, menus, setting_menu, cekrole).
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::resource('users', AdminUserController::class)->except('show');
        Route::post('users/{user}/verify', [AdminUserController::class, 'verify'])->name('users.verify');
        Route::resource('roles', AdminRoleController::class)->except('show');
        Route::post('menus/sync', [AdminMenuController::class, 'sync'])->name('menus.sync');
        Route::resource('menus', AdminMenuController::class)->except('show');
        Route::resource('setting-menu', AdminSettingMenuController::class)->except('show')->parameters(['setting-menu' => 'settingMenu']);
    });
});

// Uji koneksi database lama + gambar (HANYA di local, APP_ENV=local). Boleh dihapus setelah selesai.
if (app()->isLocal()) {
    Route::get('cek-database', function () {
        return view('cek-database', [
            'jumlah' => [
                'Artikel' => \App\Models\Article::count(),
                'Kategori' => \App\Models\Category::count(),
                'Galeri' => \App\Models\Gallery::count(),
                'Media' => \App\Models\Media::count(),
                'Banner' => \App\Models\Banner::count(),
            ],
            'artikel' => \App\Models\Article::with('category')->terbaru()->limit(5)->get(),
            'galeri' => \App\Models\Gallery::latest('id')->limit(8)->get(),
        ]);
    });
}
