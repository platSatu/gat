<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** CRUD Kategori Artikel di admin (tampilan meniru gbigatsu.id/admins/category-gallery). */
class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $kategori = Category::query()
            ->withCount('articles')
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->string('search')->trim().'%'))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('backend.categories.index', ['kategori' => $kategori, 'adaSlug' => $this->adaKolom('slug')]);
    }

    public function create(): View
    {
        return view('backend.categories.form', ['category' => new Category()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Category::create($this->data($request));

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dibuat.');
    }

    public function edit(Category $category): View
    {
        return view('backend.categories.form', ['category' => $category]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($this->data($request, $category));

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        // Jangan sampai artikel kehilangan kategorinya.
        if ($category->articles()->exists()) {
            return back()->with('error', 'Kategori "'.$category->name.'" masih dipakai '.$category->articles()->count().' artikel, jadi tidak bisa dihapus.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }

    private function data(Request $request, ?Category $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($category?->id)],
        ], [], ['name' => 'Name']);

        // Kolom slug hanya diisi kalau memang ada di tabel `categories`.
        if ($this->adaKolom('slug')) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $data;
    }

    private function adaKolom(string $kolom): bool
    {
        static $cache = [];

        return $cache[$kolom] ??= Schema::hasColumn('categories', $kolom);
    }
}
