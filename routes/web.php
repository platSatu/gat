<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Website publik (tahap tampilan: isi masih statis, nanti diambil dari admin)
Route::view('/', 'frontend.home')->name('home');

Route::prefix('tentang')->name('about')->group(function () {
    Route::view('/', 'frontend.pages.tentang.index');
    Route::view('sejarah', 'frontend.pages.tentang.sejarah')->name('.history');
    Route::view('dna-kami', 'frontend.pages.tentang.dna')->name('.dna');
});

Route::view('pelayanan', 'frontend.pages.pelayanan.index')->name('ministry');
Route::view('pelayanan/graha-bina-asuh', 'frontend.pages.pelayanan.show')->name('ministry.show');

Route::prefix('kegiatan')->name('events')->group(function () {
    Route::view('/', 'frontend.pages.kegiatan.index');
    Route::view('galeri', 'frontend.pages.kegiatan.galeri')->name('.gallery');
    Route::view('galeri/foto', 'frontend.pages.kegiatan.foto')->name('.photo');
    Route::view('penghapusan-foto', 'frontend.pages.kegiatan.penghapusan')->name('.photo-request');
});

Route::view('pengajaran', 'frontend.pages.pengajaran')->name('teaching');

Route::prefix('artikel')->name('articles')->group(function () {
    Route::view('/', 'frontend.pages.artikel.index');
    Route::view('cari', 'frontend.pages.artikel.cari')->name('.search');
    Route::view('kategori', 'frontend.pages.artikel.kategori')->name('.categories');
    Route::view('kategori/gatsu-news', 'frontend.pages.artikel.daftar')->name('.category');
    Route::view('contoh-artikel', 'frontend.pages.artikel.show')->name('.show');
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

// Admin: /admin/...
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::view('/', 'backend.dashboard')->name('dashboard');
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
