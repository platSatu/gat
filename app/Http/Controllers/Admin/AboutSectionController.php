<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * CRUD blok "Tentang Kami" (tabel `about_sections`) + terjemahan English (`about_section_translations`).
 * Background baru disimpan di public/uploads/about/; yang dihapus otomatis hanya file di folder itu
 * (gambar bawaan di public/images/about tidak disentuh).
 */
class AboutSectionController extends Controller
{
    private const FOLDER = 'uploads/about';

    /** Slug yang sudah dipakai halaman lain di /tentang/... */
    private const SLUG_DIPAKAI = ['sejarah', 'dna-kami'];

    public function index(): View
    {
        $siap = Schema::hasTable('about_sections');

        $sections = $siap
            ? AboutSection::query()->urut()
                ->when(AboutSection::terjemahanSiap(), fn ($q) => $q->withExists(['translations as punya_en' => fn ($t) => $t->where('locale', 'en')]))
                ->get()
            : collect();

        return view('backend.about.index', compact('sections', 'siap'));
    }

    public function create(): View
    {
        $this->pastikanTabel();

        return view('backend.about.form', ['section' => new AboutSection([
            'status' => true,
            'tint' => true,
            'sort_order' => (int) AboutSection::max('sort_order') + 1,
        ])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->pastikanTabel();
        $data = $this->validasi($request, new AboutSection);

        $section = AboutSection::create($this->prosesBackground($request, new AboutSection, $data));
        $section->simpanTerjemahan('en', (array) $request->input('en', []));

        return redirect()->route('admin.about.index')->with('success', 'Blok Tentang Kami berhasil dibuat.');
    }

    public function edit(AboutSection $about): View
    {
        return view('backend.about.form', ['section' => $about]);
    }

    public function update(Request $request, AboutSection $about): RedirectResponse
    {
        $data = $this->validasi($request, $about);

        $about->update($this->prosesBackground($request, $about, $data));
        $about->simpanTerjemahan('en', (array) $request->input('en', []));

        return redirect()->route('admin.about.index')->with('success', 'Blok Tentang Kami berhasil diperbarui.');
    }

    public function destroy(AboutSection $about): RedirectResponse
    {
        $this->hapusFileLokal($about->background);
        $about->delete();

        return redirect()->route('admin.about.index')->with('success', 'Blok Tentang Kami berhasil dihapus.');
    }

    /** Geser urutan satu langkah ke atas/bawah (tukar sort_order dengan blok di sebelahnya). */
    public function move(AboutSection $about, string $arah): RedirectResponse
    {
        $urutan = AboutSection::query()->urut()->get(['id', 'sort_order'])->values();
        $i = $urutan->search(fn ($s) => $s->id === $about->id);
        $j = $arah === 'up' ? $i - 1 : $i + 1;

        if ($i !== false && isset($urutan[$j])) {
            // Rapikan nomor urut (1, 2, 3, ...) lalu tukar posisi.
            $ids = $urutan->pluck('id')->all();
            [$ids[$i], $ids[$j]] = [$ids[$j], $ids[$i]];
            foreach ($ids as $n => $id) {
                AboutSection::whereKey($id)->update(['sort_order' => $n + 1]);
            }
        }

        return redirect()->route('admin.about.index');
    }

    private function pastikanTabel(): void
    {
        abort_unless(Schema::hasTable('about_sections'), 503, 'Tabel about_sections belum dibuat. Jalankan migration / SQL-nya dulu.');
    }

    private function validasi(Request $request, AboutSection $section): array
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('name'))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::notIn(self::SLUG_DIPAKAI), Rule::unique('about_sections', 'slug')->ignore($section->id)],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:2000'],
            'description' => ['nullable', 'string'],
            'background' => [$section->exists ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'tint' => ['nullable', 'boolean'],
            'button_text' => ['nullable', 'string', 'max:255'],
            'link' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:0,1'],
            'en' => ['nullable', 'array'],
            'en.name' => ['nullable', 'string', 'max:255'],
            'en.subtitle' => ['nullable', 'string', 'max:255'],
            'en.excerpt' => ['nullable', 'string', 'max:2000'],
            'en.description' => ['nullable', 'string'],
            'en.button_text' => ['nullable', 'string', 'max:255'],
        ], [
            'slug.not_in' => 'Slug ini sudah dipakai halaman lain (/tentang/sejarah, /tentang/dna-kami).',
        ], [
            'name' => 'Name', 'excerpt' => 'Teks singkat', 'background' => 'Background', 'sort_order' => 'Urutan',
            'en.name' => 'Name (English)',
        ]);

        $data['tint'] = $request->boolean('tint');
        unset($data['en'], $data['background']);

        return $data;
    }

    private function prosesBackground(Request $request, AboutSection $section, array $data): array
    {
        if ($request->hasFile('background')) {
            $file = $request->file('background');
            $nama = Str::limit($data['slug'], 100, '').'-'.now()->format('YmdHis').'-'.Str::lower(Str::random(4))
                .'.'.strtolower($file->getClientOriginalExtension());
            $file->move(public_path(self::FOLDER), $nama);

            $this->hapusFileLokal($section->background);
            $data['background'] = self::FOLDER.'/'.$nama;
        }

        return $data;
    }

    /** Hapus file hanya kalau ada di public/uploads/about. */
    private function hapusFileLokal(?string $path): void
    {
        if (blank($path) || preg_match('#^https?://#i', $path)) {
            return;
        }

        $file = realpath(public_path(ltrim($path, '/'))) ?: '';
        $folder = realpath(public_path(self::FOLDER)) ?: public_path(self::FOLDER);
        if ($file !== '' && str_starts_with($file, $folder.DIRECTORY_SEPARATOR) && File::isFile($file)) {
            File::delete($file);
        }
    }
}
