<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * CRUD Artikel di admin (meniru gbigatsu.id/admins/article).
 * Gambar disimpan di public/images/ (path relatif di kolom `image`, sama seperti website lama).
 * Judul & isi English (tab English di form) disimpan di `article_translations`.
 */
class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $artikel = Article::query()
            ->with('category:id,name')
            ->when(Article::terjemahanSiap(), fn ($q) => $q->withCount('translations as punya_en'))
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%'.$request->string('search')->trim().'%'))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')))
            ->terbaru()
            ->paginate(10, ['id', 'category_id', 'slug', 'title', 'image', 'date_post', 'status'])
            ->withQueryString();

        return view('backend.articles.index', [
            'artikel' => $artikel,
            'kategori' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): View
    {
        return view('backend.articles.form', [
            'article' => new Article(['status' => 0, 'date_post' => now()]),
            'kategori' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);
        unset($data['tr']);
        $data['slug'] = $this->slugUnik($data['title']);
        $data['read_count'] = 0;

        if ($request->hasFile('image')) {
            $data['image'] = $this->simpanGambar($request->file('image'), $data['slug']);
        }

        $article = Article::create($data);
        $article->simpanSemuaTerjemahan((array) $request->input('tr', []));

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil dibuat.');
    }

    public function edit(Article $article): View
    {
        return view('backend.articles.form', [
            'article' => $article,
            'kategori' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $data = $this->validasi($request);
        unset($data['tr']);

        if ($request->hasFile('image')) {
            $lama = $article->image;
            $data['image'] = $this->simpanGambar($request->file('image'), $article->slug ?: Str::slug($data['title']));
            $this->hapusGambarLokal($lama);
        }

        $article->update($data);
        $article->simpanSemuaTerjemahan((array) $request->input('tr', []));

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $this->hapusGambarLokal($article->image);
        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'date_post' => ['required', 'date'],
            'status' => ['required', 'in:0,1'],
            ...Article::aturanTerjemahan(['title' => 255]),
        ], [], [
            'tr.*.title' => 'Title (terjemahan)',
            'category_id' => 'Category', 'title' => 'Title', 'image' => 'Image', 'date_post' => 'Date Post',
        ]);
    }

    /** Slug dari judul, ditambah -2, -3, ... kalau sudah dipakai artikel lain. */
    private function slugUnik(string $judul): string
    {
        $dasar = Str::slug($judul) ?: 'artikel';
        $slug = $dasar;

        for ($i = 2; Article::where('slug', $slug)->exists(); $i++) {
            $slug = $dasar.'-'.$i;
        }

        return $slug;
    }

    private function simpanGambar(UploadedFile $file, string $nama): string
    {
        $namaFile = Str::limit($nama, 120, '').'-'.now()->format('YmdHis').'.'.strtolower($file->getClientOriginalExtension());
        $file->move(public_path('images'), $namaFile);

        return 'images/'.$namaFile;
    }

    /** Hapus file gambar hanya kalau memang ada di server ini (gambar lama di cPanel tidak disentuh). */
    private function hapusGambarLokal(?string $path): void
    {
        if (blank($path) || preg_match('#^https?://#i', $path)) {
            return;
        }

        $file = public_path(ltrim($path, '/'));
        if (str_starts_with(realpath($file) ?: '', public_path('images')) && File::isFile($file)) {
            File::delete($file);
        }
    }
}
