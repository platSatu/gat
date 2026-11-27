{{--
    Tab bahasa untuk form admin konten (Artikel, Highlights, Tentang Kami, Hero).
    Tab pertama = Bahasa Indonesia (kolom di tabel utama, wajib). Tab lain = bahasa dari admin Settings → Bahasa,
    disimpan di tabel terjemahan (boleh kosong -> website memakai versi Indonesia). Input: tr[kode][kolom].

    Parameter:
      $model      model yang memakai trait PunyaTerjemahan
      $kolom      (opsional) daftar isian: [kolom, label, jenis (text|textarea|editor), keterangan, wajib]
                  bawaan: Title (text, wajib) + Description (editor)
      $titleHelp  (opsional) keterangan di bawah isian pertama (Indonesia)
    Editor: textarea jenis "editor" diberi kelas js-editor (TinyMCE di form masing-masing memakai selector textarea.js-editor).
--}}
@php
    $kolom ??= [
        ['title', 'Title', 'text', $titleHelp ?? null, true],
        ['description', 'Description', 'editor', null, false],
    ];
    $siap = $model::terjemahanSiap();
    $bahasaLain = \App\Models\Bahasa::lain();
    $tabError = $bahasaLain->first(fn ($b) => $errors->has('tr.'.$b['kode'].'.*'))['kode'] ?? null;
    $aktif = $tabError ?? 'id';
@endphp

<ul class="nav nav-tabs lang-tabs" role="tablist">
    <li class="nav-item">
        <a @class(['nav-link', 'active' => $aktif === 'id']) data-toggle="tab" href="#tab-id" role="tab">Indonesia <small class="text-muted">(utama)</small></a>
    </li>
    @foreach ($bahasaLain as $b)
        @php $tr = $model->terjemahan($b['kode']); @endphp
        <li class="nav-item">
            <a @class(['nav-link', 'active' => $aktif === $b['kode']]) data-toggle="tab" href="#tab-{{ $b['kode'] }}" role="tab">
                {{ $b['nama'] }}
                @if ($tr->exists)<span class="badge badge-info ml-1">{{ strtoupper($b['kode']) }}</span>@endif
            </a>
        </li>
    @endforeach
</ul>

<div class="tab-content lang-tab-content">
    @foreach ([['kode' => 'id', 'nama' => 'Indonesia'], ...$bahasaLain->all()] as $b)
        @php
            $utama = $b['kode'] === 'id';
            $tr = $utama ? $model : $model->terjemahan($b['kode']);
        @endphp
        <div @class(['tab-pane', 'fade', 'show active' => $aktif === $b['kode']]) id="tab-{{ $b['kode'] }}" role="tabpanel">
            @if (! $utama && ! $siap)
                <div class="alert alert-warning mb-0">Tabel terjemahan untuk konten ini belum ada di database.</div>
            @else
                @unless ($utama)
                    <p class="text-muted">Boleh dikosongkan. Bagian yang kosong tampil dalam Bahasa Indonesia saat pengunjung memilih {{ $b['nama'] }}.</p>
                @endunless

                @foreach ($kolom as [$nama, $label, $jenis, $bantuan, $wajib])
                    @php
                        $field = $utama ? $nama : "tr[{$b['kode']}][$nama]";
                        $kunci = $utama ? $nama : "tr.{$b['kode']}.$nama";
                        $id = $utama ? $nama : "{$nama}_{$b['kode']}";
                        $nilai = old($kunci, $tr->{$nama});
                    @endphp
                    <div @class(['form-group', 'mb-0' => $loop->last])>
                        <label for="{{ $id }}">{{ $label }}@unless ($utama) ({{ $b['nama'] }})@endunless @if ($utama && $wajib)<span class="text-danger">*</span>@endif</label>
                        @if ($jenis === 'text')
                            <input id="{{ $id }}" type="text" name="{{ $field }}" value="{{ $nilai }}" maxlength="255" @if ($utama && $wajib) required @endif
                                @class(['form-control', 'is-invalid' => $errors->has($kunci)])>
                        @else
                            <textarea id="{{ $id }}" name="{{ $field }}" rows="{{ $jenis === 'editor' ? 14 : 4 }}"
                                @class(['form-control', 'js-editor' => $jenis === 'editor', 'is-invalid' => $errors->has($kunci)])>{{ $nilai }}</textarea>
                        @endif
                        @error($kunci)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        @if ($utama && $bantuan)<small class="form-text text-muted">{{ $bantuan }}</small>@endif
                    </div>
                @endforeach
            @endif
        </div>
    @endforeach
</div>

@once
    @push('styles')
        <style>
            .lang-tabs { margin-bottom: 0; }
            .lang-tab-content { padding: 20px; margin-bottom: 20px; border: 1px solid #dee2e6; border-top: 0; border-radius: 0 0 6px 6px; }
        </style>
    @endpush
@endonce
