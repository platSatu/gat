<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * CRUD "Pelayanan dan Mitra Kami" (tabel `partners`): logo di beranda.
 * Logo baru disimpan di public/uploads/partners/; yang dihapus otomatis hanya file di folder itu
 * (logo bawaan di public/images/partners tidak disentuh).
 */
class PartnerController extends Controller
{
    private const FOLDER = 'uploads/partners';

    public function index(): View
    {
        $siap = Schema::hasTable('partners');
        $partners = $siap ? Partner::query()->orderBy('id')->get() : collect();

        return view('backend.partners.index', compact('partners', 'siap'));
    }

    public function create(): View
    {
        $this->pastikanTabel();

        return view('backend.partners.form', ['partner' => new Partner(['status' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->pastikanTabel();
        $data = $this->validasi($request, new Partner);

        Partner::create($this->prosesLogo($request, new Partner, $data));

        return redirect()->route('admin.partners.index')->with('success', 'Mitra berhasil ditambahkan.');
    }

    public function edit(Partner $partner): View
    {
        return view('backend.partners.form', compact('partner'));
    }

    public function update(Request $request, Partner $partner): RedirectResponse
    {
        $data = $this->validasi($request, $partner);

        $partner->update($this->prosesLogo($request, $partner, $data));

        return redirect()->route('admin.partners.index')->with('success', 'Mitra berhasil diperbarui.');
    }

    public function destroy(Partner $partner): RedirectResponse
    {
        $this->hapusFileLokal($partner->logo);
        $partner->delete();

        return redirect()->route('admin.partners.index')->with('success', 'Mitra berhasil dihapus.');
    }

    private function pastikanTabel(): void
    {
        abort_unless(Schema::hasTable('partners'), 503, 'Tabel partners belum dibuat. Jalankan migration / SQL-nya dulu.');
    }

    private function validasi(Request $request, Partner $partner): array
    {
        // "gbi.or.id" → "https://gbi.or.id" supaya admin tidak wajib mengetik https://
        $website = trim((string) $request->input('website'));
        if ($website !== '' && ! preg_match('#^https?://#i', $website)) {
            $website = 'https://'.ltrim($website, '/');
        }
        $request->merge(['website' => $website === '' ? null : $website]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => [$partner->exists ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'description' => ['nullable', 'string', 'max:2000'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'status' => ['required', 'in:0,1'],
        ], [
            'website.url' => 'Website harus berupa alamat yang benar, mis. https://gbi.or.id',
        ], [
            'name' => 'Name', 'logo' => 'Logo', 'description' => 'Description', 'website' => 'Website',
        ]);

        unset($data['logo']);

        return $data;
    }

    private function prosesLogo(Request $request, Partner $partner, array $data): array
    {
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $nama = Str::limit(Str::slug($data['name']) ?: 'mitra', 80, '').'-'.now()->format('YmdHis').'-'.Str::lower(Str::random(4))
                .'.'.strtolower($file->getClientOriginalExtension());
            $file->move(public_path(self::FOLDER), $nama);

            $this->hapusFileLokal($partner->logo);
            $data['logo'] = self::FOLDER.'/'.$nama;
        }

        return $data;
    }

    /** Hapus file hanya kalau ada di public/uploads/partners. */
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
