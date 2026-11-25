<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AboutSection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * Halaman Tentang Kami: blok dari tabel `about_sections` (aktif, urut sort_order).
 * Kalau tabelnya belum dibuat, halaman memakai isi lama yang tertulis di view (tidak error).
 */
class AboutController extends Controller
{
    public function index(): View
    {
        $sections = Schema::hasTable('about_sections')
            ? AboutSection::aktif()->urut()->denganTerjemahan()->get()
            : null;

        return view('frontend.pages.tentang.index', compact('sections'));
    }

    public function show(string $slug): View
    {
        abort_unless(Schema::hasTable('about_sections'), 404);

        $section = AboutSection::aktif()->where('slug', $slug)->denganTerjemahan()->firstOrFail();

        $lainnya = AboutSection::aktif()->urut()->whereKeyNot($section->getKey())
            ->denganTerjemahan()->limit(3)->get();

        return view('frontend.pages.tentang.show', compact('section', 'lainnya'));
    }
}
