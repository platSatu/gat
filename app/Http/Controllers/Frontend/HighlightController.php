<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Highlight;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** Beranda (slider highlights) & detail highlight di /highlights/{slug}. Link lama /media/{id} dialihkan. */
class HighlightController extends Controller
{
    private const KOLOM_KARTU = ['id', 'title', 'image1', 'bghighlights', 'date_post'];

    public function home(): View
    {
        return view('frontend.home', [
            'highlights' => Highlight::tampil()->terbaru()->get(self::KOLOM_KARTU),
        ]);
    }

    /**
     * Halaman Kegiatan: highlight terbaru (Kegiatan Sebelumnya) + artikel terbaru untuk kotak Gatsu News.
     * Kategori "Gatsu News" dipakai kalau ada; kalau belum ada, pakai artikel terbit terbaru yang bergambar.
     */
    public function events(): View
    {
        $artikel = fn () => Article::terbit()->whereNotNull('image')->where('image', '!=', '')->terbaru();
        $kolom = ['id', 'category_id', 'slug', 'title', 'image', 'date_post'];

        return view('frontend.pages.kegiatan.index', [
            'highlight' => Highlight::tampil()->terbaru()->first(self::KOLOM_KARTU),
            'gatsuNews' => $artikel()->whereHas('category', fn ($q) => $q->where('name', 'Gatsu News'))->first($kolom)
                ?? $artikel()->first($kolom),
        ]);
    }

    public function show(Highlight $highlight): View
    {
        abort_unless((int) $highlight->status === 1, 404);

        return view('frontend.pages.highlight.show', compact('highlight'));
    }

    /** /media/{id} dari website lama -> 301 ke /highlights/{slug}. */
    public function legacy(int $id): RedirectResponse
    {
        $highlight = Highlight::tampil()->findOrFail($id, ['id', 'title']);

        return redirect()->route('highlights.show', $highlight, 301);
    }
}
