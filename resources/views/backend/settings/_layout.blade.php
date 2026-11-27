{{--
    Bagian atas halaman Settings: judul, keterangan, tombol aksi, dan tab Daftar User | Roles | Menu | Setting Menu.
    Pakai: @include('backend.settings._layout', ['judul' => '...', 'keterangan' => '...', 'aksi' => [['label', 'url', 'icon']]])
--}}
@php
    $tabs = [
        ['Daftar User', 'admin.settings.users.index', 'admin.settings.users.*', 'mdi-account-box'],
        ['Roles', 'admin.settings.roles.index', 'admin.settings.roles.*', 'mdi-security'],
        ['Menu', 'admin.settings.menus.index', 'admin.settings.menus.*', 'mdi-format-list-bulleted'],
        ['Setting Menu', 'admin.settings.setting-menu.index', 'admin.settings.setting-menu.*', 'mdi-key-variant'],
        ['Situs & SEO', 'admin.settings.situs.index', 'admin.settings.situs.*', 'mdi-web'],
        ['Bahasa', 'admin.settings.bahasa.index', 'admin.settings.bahasa.*', 'mdi-earth'],
        ['Terjemahan', 'admin.settings.terjemahan.index', 'admin.settings.terjemahan.*', 'mdi-translate'],
    ];
@endphp

<div class="set-hero">
    <div class="set-hero__text">
        <span class="set-hero__eyebrow"><i class="mdi mdi-settings"></i> Settings</span>
        <h1 class="set-hero__title">{{ $judul }}</h1>
        @isset($keterangan)<p class="set-hero__desc">{{ $keterangan }}</p>@endisset
    </div>
    @if (! empty($aksi))
        <div class="set-hero__actions">
            @foreach ($aksi as [$label, $url, $icon])
                <a href="{{ $url }}" class="btn set-btn set-btn--primary"><i class="mdi {{ $icon }}"></i> {{ $label }}</a>
            @endforeach
        </div>
    @endif
</div>

<nav class="set-tabs">
    @foreach ($tabs as [$label, $route, $pola, $icon])
        <a href="{{ route($route) }}" @class(['set-tabs__item', 'is-active' => request()->routeIs($pola)])>
            <i class="mdi {{ $icon }}"></i> {{ $label }}
        </a>
    @endforeach
</nav>

@once
    @push('styles')
        <style>
            :root { --set-primary: #2b3a8c; --set-primary-soft: #eef0fb; --set-ink: #1f2433; --set-muted: #7a8194; --set-line: #e9ebf2; --set-radius: 14px; }

            .set-hero { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 16px;
                padding: 24px 28px; margin-bottom: 16px; border-radius: var(--set-radius); color: #fff;
                background: linear-gradient(120deg, #24306f 0%, #2b3a8c 55%, #4458c4 100%); box-shadow: 0 10px 30px rgba(36, 48, 111, .18); }
            .set-hero__eyebrow { display: inline-flex; align-items: center; gap: 6px; padding: 3px 10px; border-radius: 999px; background: rgba(255,255,255,.15); font-size: 12px; letter-spacing: .04em; text-transform: uppercase; }
            .set-hero__title { margin: 10px 0 4px; font-size: 28px; font-weight: 700; color: #fff; }
            .set-hero__desc { margin: 0; max-width: 640px; color: rgba(255,255,255,.8); font-size: 14px; }
            .set-hero__actions { display: flex; gap: 8px; flex-wrap: wrap; }
            .set-hero .set-btn--primary { background: #fff; color: var(--set-primary); }
            .set-hero .set-btn--primary:hover { background: #f1f3ff; color: var(--set-primary); }

            .set-tabs { display: flex; gap: 6px; flex-wrap: wrap; padding: 6px; margin-bottom: 20px; background: #fff; border: 1px solid var(--set-line); border-radius: 12px; }
            .set-tabs__item { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; color: var(--set-muted); font-weight: 500; transition: background .15s, color .15s; }
            .set-tabs__item:hover { background: var(--set-primary-soft); color: var(--set-primary); text-decoration: none; }
            .set-tabs__item.is-active { background: var(--set-primary); color: #fff; }

            .set-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border: 0; border-radius: 10px; font-weight: 600; }
            .set-btn--primary { background: var(--set-primary); color: #fff; }
            .set-btn--primary:hover { background: #24306f; color: #fff; }
            .set-btn--ghost { background: transparent; color: var(--set-muted); border: 1px solid var(--set-line); }
            .set-btn--ghost:hover { background: #f6f7fb; color: var(--set-ink); }

            .set-card { background: #fff; border: 1px solid var(--set-line); border-radius: var(--set-radius); box-shadow: 0 2px 10px rgba(20, 30, 70, .04); margin-bottom: 20px; }
            .set-card__head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; padding: 16px 20px; border-bottom: 1px solid var(--set-line); }
            .set-card__head h2 { margin: 0; font-size: 16px; font-weight: 700; color: var(--set-ink); }
            .set-card__body { padding: 20px; }

            .set-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-bottom: 20px; }
            .set-stat { display: flex; align-items: center; gap: 14px; padding: 16px 18px; background: #fff; border: 1px solid var(--set-line); border-radius: var(--set-radius); }
            .set-stat__icon { display: grid; place-items: center; width: 44px; height: 44px; border-radius: 12px; font-size: 22px; background: var(--set-primary-soft); color: var(--set-primary); }
            .set-stat__icon--green { background: #e7f8ef; color: #1e9e5a; }
            .set-stat__icon--amber { background: #fff4e0; color: #c98200; }
            .set-stat__icon--gray { background: #f0f1f5; color: #6c7385; }
            .set-stat__num { font-size: 22px; font-weight: 700; line-height: 1; color: var(--set-ink); }
            .set-stat__label { margin-top: 4px; font-size: 12px; color: var(--set-muted); }

            .set-filter { display: flex; flex-wrap: wrap; gap: 8px; }
            .set-filter { align-items: center; }
            .set-filter .form-control { width: auto; min-width: 160px; height: 38px; border-radius: 10px; border-color: var(--set-line); }
            .set-filter .set-search { position: relative; }
            .set-filter .set-search .form-control { padding-left: 34px; min-width: 240px; }
            .set-filter .set-search i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--set-muted); }

            .set-table { width: 100%; margin: 0; }
            .set-table th { padding: 12px 20px; font-size: 12px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; color: var(--set-muted); background: #fafbfd; border-bottom: 1px solid var(--set-line); white-space: nowrap; }
            .set-table td { padding: 14px 20px; vertical-align: middle; border-top: 1px solid var(--set-line); color: var(--set-ink); }
            .set-table tbody tr:hover { background: #fafbff; }
            .set-table .set-actions { white-space: nowrap; text-align: right; }

            .set-user { display: flex; align-items: center; gap: 12px; min-width: 200px; }
            .set-avatar { display: grid; place-items: center; flex: 0 0 38px; width: 38px; height: 38px; border-radius: 50%; font-size: 14px; font-weight: 700; color: #fff; }
            .set-user__name { font-weight: 600; line-height: 1.2; }
            .set-user__sub { font-size: 12px; color: var(--set-muted); }

            .set-chip { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; margin: 2px 4px 2px 0; border-radius: 999px; font-size: 12px; font-weight: 500; background: var(--set-primary-soft); color: var(--set-primary); white-space: nowrap; }
            .set-chip--green { background: #e7f8ef; color: #1e9e5a; }
            .set-chip--amber { background: #fff4e0; color: #b57500; }
            .set-chip--gray { background: #f0f1f5; color: #6c7385; }
            .set-chip--red { background: #fdeceb; color: #c8382b; }
            .set-chip--dark { background: #24306f; color: #fff; }

            .set-icon-btn { display: inline-grid; place-items: center; width: 34px; height: 34px; margin-left: 4px; border: 1px solid var(--set-line); border-radius: 10px; background: #fff; color: var(--set-muted); transition: all .15s; }
            .set-icon-btn:hover { color: var(--set-primary); border-color: var(--set-primary); text-decoration: none; }
            .set-icon-btn--danger:hover { color: #c8382b; border-color: #c8382b; }
            .set-icon-btn--success:hover { color: #1e9e5a; border-color: #1e9e5a; }
            .set-icon-btn[disabled] { opacity: .4; pointer-events: none; }

            .set-empty { padding: 48px 20px; text-align: center; color: var(--set-muted); }
            .set-empty i { display: block; margin-bottom: 8px; font-size: 40px; color: #c5cada; }

            .set-form .form-group label { font-weight: 600; color: var(--set-ink); }
            .set-form .form-control { height: 42px; border-radius: 10px; border-color: var(--set-line); }
            .set-form .form-control:focus { border-color: var(--set-primary); box-shadow: 0 0 0 3px rgba(43, 58, 140, .12); }
            .set-form-actions { display: flex; gap: 8px; padding-top: 8px; }

            .set-pagination .pagination { margin: 0; }
            .set-pagination { padding: 14px 20px; border-top: 1px solid var(--set-line); }
        </style>
    @endpush
@endonce

@once
    @push('scripts')
        <script>
            // Warna avatar dari nama (selalu sama untuk nama yang sama).
            document.querySelectorAll('[data-avatar]').forEach(function (el) {
                var nama = el.getAttribute('data-avatar') || '?', h = 0;
                for (var i = 0; i < nama.length; i++) { h = (h * 31 + nama.charCodeAt(i)) % 360; }
                el.style.background = 'hsl(' + h + ', 55%, 48%)';
            });
        </script>
    @endpush
@endonce
