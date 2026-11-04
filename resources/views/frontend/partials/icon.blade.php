{{-- Ikon SVG kecil yang dipakai berulang. Pemakaian: @include('frontend.partials.icon', ['name' => 'arrow-right']) --}}
@switch($name)
    @case('arrow-left')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>@break
    @case('arrow-right')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>@break
    @case('arrow-circle')<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="12" fill="#fff"/><path d="M7 12h10M13 8l4 4-4 4" fill="none" stroke="#1d1d1d" stroke-width="2"/></svg>@break
    @case('play')<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6 4l15 8-15 8z"/></svg>@break
    @case('search')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="11" cy="11" r="7.5"/><path d="M16.5 16.5L22 22"/></svg>@break
    @case('download')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 4v11M7 10l5 5 5-5M5 20h14"/></svg>@break
    @case('share')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4z"/></svg>@break
    @case('chevron-down')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true" width="14" height="14"><path d="M6 9l6 6 6-6"/></svg>@break
    @case('phone')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="10.5"/><path d="M9 7.5l1.5 3-1.2 1.2a7 7 0 003 3l1.2-1.2 3 1.5-.6 2a2 2 0 01-2 .9A9 9 0 017.5 9.6 2 2 0 018.4 7.5z"/></svg>@break
    @case('mail')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="10.5"/><rect x="6.5" y="8" width="11" height="8" rx="1"/><path d="M6.5 8.5L12 12.5l5.5-4"/></svg>@break
    @case('pin')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="10.5"/><path d="M12 18s4.5-4.3 4.5-7.5a4.5 4.5 0 00-9 0C7.5 13.7 12 18 12 18z"/><circle cx="12" cy="10.5" r="1.6"/></svg>@break
    @case('give')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="10.5"/><path d="M12 10.5c-1.2-2.2-4-1.4-4 .6 0 2 4 4.4 4 4.4s4-2.4 4-4.4c0-2-2.8-2.8-4-.6z"/><path d="M6 17.5c2-.2 3.5.6 5 .6h4"/></svg>@break
@endswitch
