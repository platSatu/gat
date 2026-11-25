<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Highlight;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * CRUD Highlights di admin (tabel `highlights`): judul, deskripsi, video, 8 foto, gambar latar, tanggal, status.
 * Judul & isi English (tab English di form) disimpan di `highlight_translations`.
 * File baru disimpan di public/uploads/highlights/ (path relatif di kolom, sama seperti data lama "uploads/...").
 * Yang dihapus otomatis hanya file di folder itu; file lama di uploads/ tidak disentuh.
 */
class HighlightController extends Controller
{
    private const FOLDER = 'uploads/highlights';

    public function index(Request $request): View
    {
        $highlights = Highlight::query()
            ->when(Highlight::terjemahanSiap(), fn ($q) => $q->withExists(['translations as punya_en' => fn ($t) => $t->where('locale', 'en')]))
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%'.$request->string('search')->trim().'%'))
            ->terbaru()
            ->paginate(10, ['id', 'title', 'image1', 'bghighlights', 'video', 'date_post', 'status'])
            ->withQueryString();

        return view('backend.highlights.index', compact('highlights'));
    }

    public function create(): View
    {
        return view('backend.highlights.form', [
            'highlight' => new Highlight(['status' => 1, 'date_post' => now()]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request, baru: true);
        $data = $this->prosesFile($request, new Highlight, $data);

        $highlight = Highlight::create($data);
        $highlight->simpanTerjemahan('en', (array) $request->input('en', []));

        return redirect()->route('admin.highlights.index')->with('success', 'Highlight berhasil dibuat.');
    }

    public function edit(Highlight $highlight): View
    {
        return view('backend.highlights.form', compact('highlight'));
    }

    public function update(Request $request, Highlight $highlight): RedirectResponse
    {
        $data = $this->validasi($request, baru: false);
        $data = $this->prosesFile($request, $highlight, $data);

        $highlight->update($data);
        $highlight->simpanTerjemahan('en', (array) $request->input('en', []));

        return redirect()->route('admin.highlights.index')->with('success', 'Highlight berhasil diperbarui.');
    }

    public function destroy(Highlight $highlight): RedirectResponse
    {
        foreach ($this->kolomFile() as $kolom) {
            $this->hapusFileLokal($highlight->{$kolom});
        }
        $highlight->delete();

        return redirect()->route('admin.highlights.index')->with('success', 'Highlight berhasil dihapus.');
    }

    /** image1..image8, bghighlights, video */
    private function kolomFile(): array
    {
        return [...Highlight::KOLOM_FOTO, 'bghighlights', 'video'];
    }

    private function validasi(Request $request, bool $baru): array
    {
        $gambar = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];

        $aturan = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date_post' => ['required', 'date'],
            'status' => ['required', 'in:0,1'],
            'video' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/quicktime', 'max:102400'],
            'bghighlights' => $gambar,
            'en' => ['nullable', 'array'],
            'en.title' => ['nullable', 'string', 'max:255'],
            'en.description' => ['nullable', 'string'],
            'hapus' => ['nullable', 'array'],
            'hapus.*' => ['in:'.implode(',', $this->kolomFile())],
        ];
        foreach (Highlight::KOLOM_FOTO as $kolom) {
            $aturan[$kolom] = $gambar;
        }
        // Kartu di beranda memakai Foto 1, jadi wajib diisi saat membuat highlight baru.
        if ($baru) {
            $aturan['image1'] = ['required', ...array_slice($gambar, 1)];
        }

        $label = ['en.title' => 'Title (English)', 'title' => 'Title', 'date_post' => 'Date Post', 'video' => 'Video', 'bghighlights' => 'Background'];
        foreach (Highlight::KOLOM_FOTO as $i => $kolom) {
            $label[$kolom] = 'Foto '.($i + 1);
        }

        $data = $request->validate($aturan, [], $label);

        return collect($data)->only(['title', 'description', 'date_post', 'status'])->all();
    }

    /** Simpan file yang diunggah, kosongkan kolom yang dicentang "hapus", hapus file lokal yang diganti. */
    private function prosesFile(Request $request, Highlight $highlight, array $data): array
    {
        $hapus = (array) $request->input('hapus', []);
        $dasar = Str::slug($data['title']) ?: 'highlight';

        foreach ($this->kolomFile() as $kolom) {
            if ($request->hasFile($kolom)) {
                $data[$kolom] = $this->simpanFile($request->file($kolom), $dasar.'-'.$kolom);
                $this->hapusFileLokal($highlight->{$kolom});
            } elseif (in_array($kolom, $hapus, true) && $kolom !== 'image1') {
                $this->hapusFileLokal($highlight->{$kolom});
                $data[$kolom] = null;
            }
        }

        return $data;
    }

    private function simpanFile(UploadedFile $file, string $nama): string
    {
        $namaFile = Str::limit($nama, 120, '').'-'.now()->format('YmdHis').'-'.Str::lower(Str::random(4))
            .'.'.strtolower($file->getClientOriginalExtension());
        $file->move(public_path(self::FOLDER), $namaFile);

        return self::FOLDER.'/'.$namaFile;
    }

    /** Hapus file hanya kalau ada di public/uploads/highlights (file dari website lama tidak disentuh). */
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
