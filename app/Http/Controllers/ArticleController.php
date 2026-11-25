<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Halaman Artikel publik, data dari tabel `articles` & `categories` (database lama). Hanya artikel terbit (status 1).
 * denganTerjemahan(): judul/isi Inggris ikut dimuat kalau pengunjung memilih English (cadangan: Indonesia).
 */
class ArticleController extends Controller
{
    /** Kolom yang cukup untuk kartu daftar (isi lengkap tidak perlu). */
    private const KOLOM_KARTU = ['id', 'category_id', 'slug', 'title', 'description', 'image', 'date_post', 'read_count'];

    private const PER_HALAMAN = 10;

    public function index(): View
    {
        return view('frontend.pages.artikel.index', [
            'kategoriArtikel' => Category::untukArtikel(),
            'unggulan' => Article::terbit()->denganTerjemahan()->terbaru()->first(self::KOLOM_KARTU),
            'terbaru' => Article::terbit()->denganTerjemahan()->terbaru()->skip(1)->first(self::KOLOM_KARTU),
            'terpopuler' => Article::terbit()->denganTerjemahan()->terpopuler()->first(self::KOLOM_KARTU),
        ]);
    }

    /** Halaman Pengajaran (Figma "Artikel"): cari, KOM Everyone, kategori, artikel terpopuler. */
    public function teaching(): View
    {
        return view('frontend.pages.pengajaran', [
            'kategori' => $this->kategoriDenganSampul(),
            'terpopuler' => Article::terbit()->denganTerjemahan()->terpopuler()->with('category:id,name')->limit(4)->get(self::KOLOM_KARTU),
        ]);
    }

    /** Semua artikel (terbaru, atau ?urut=populer). */
    public function all(Request $request): View
    {
        $populer = $request->query('urut') === 'populer';
        $query = Article::terbit()->denganTerjemahan()->with('category:id,name');

        return view('frontend.pages.artikel.daftar', [
            'judul' => $populer ? __('Artikel Terpopuler') : __('Artikel Terbaru'),
            'kategori' => null,
            'kategoriArtikel' => Category::untukArtikel(),
            'artikel' => ($populer ? $query->terpopuler() : $query->terbaru())
                ->simplePaginate(self::PER_HALAMAN, self::KOLOM_KARTU)->withQueryString(),
        ]);
    }

    /** Semua kategori (kartu), gambar = artikel terbaru di kategori itu. */
    public function categories(): View
    {
        return view('frontend.pages.artikel.kategori', ['kategori' => $this->kategoriDenganSampul()]);
    }

    /** Kategori yang punya artikel terbit + gambar sampul (artikel terbaru di kategori itu). */
    private function kategoriDenganSampul()
    {
        return Category::untukArtikel()->each(function (Category $c) {
            $c->setRelation('sampul', $c->articles()->terbit()->whereNotNull('image')->terbaru()->first(['id', 'category_id', 'image']));
        });
    }

    /** Artikel dalam satu kategori. */
    public function category(?Category $category = null): View|RedirectResponse
    {
        if (! $category) {
            return redirect()->route('articles.categories');
        }

        return view('frontend.pages.artikel.daftar', [
            'judul' => $category->name,
            'kategori' => $category,
            'kategoriArtikel' => Category::untukArtikel(),
            'artikel' => $category->articles()->terbit()->denganTerjemahan()->terbaru()->with('category:id,name')
                ->simplePaginate(self::PER_HALAMAN, self::KOLOM_KARTU),
        ]);
    }

    public function show(?Article $article = null): View
    {
        $article ??= Article::terbit()->denganTerjemahan()->terbaru()->firstOrFail();
        abort_unless((int) $article->status === 1, 404);
        $article->loadMissing('category:id,name');
        if (! Article::bahasaUtama() && Article::terjemahanSiap()) {
            $article->loadMissing('terjemahanAktif');
        }

        $bacaJuga = Article::terbit()
            ->whereKeyNot($article->getKey())
            ->when($article->category_id, fn ($q) => $q->where('category_id', $article->category_id))
            ->terbaru()
            ->limit(2)
            ->get(['id', 'slug', 'title', 'description', 'image']);

        return view('frontend.pages.artikel.show', compact('article', 'bacaJuga'));
    }
}
