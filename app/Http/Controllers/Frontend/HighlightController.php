<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
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

    public function show(Highlight $highlight): View
    {
        abort_unless((int) $highlight->status === 1, 404);

        $lainnya = Highlight::tampil()
            ->whereKeyNot($highlight->getKey())
            ->terbaru()
            ->limit(6)
            ->get(self::KOLOM_KARTU);

        return view('frontend.pages.highlight.show', compact('highlight', 'lainnya'));
    }

    /** /media/{id} dari website lama -> 301 ke /highlights/{slug}. */
    public function legacy(int $id): RedirectResponse
    {
        $highlight = Highlight::tampil()->findOrFail($id, ['id', 'title']);

        return redirect()->route('highlights.show', $highlight, 301);
    }
}
