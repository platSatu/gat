/* GBI Gatsu v2 — interaksi website publik (tanpa library). */
(function () {
    'use strict';

    /* Loading screen: tampil sekali per sesi (kunjungan berikutnya sudah dihapus di partials/loader).
       Urutan (lihat frontend.css): tulisan diam 0,9 dtk -> bergeser ke kiri (1,9 dtk) -> lingkaran mengecil (2 dtk setelah mulai bergerak). */
    var loader = document.querySelector('[data-loader]');
    if (loader) {
        document.body.classList.add('is-locked');
        window.addEventListener('load', function () {
            var hold = 900; // tulisan diam dulu sebelum mulai bergerak
            setTimeout(function () { loader.classList.add('is-done'); }, hold);
            setTimeout(function () { document.body.classList.remove('is-locked'); }, hold + 2200);
            setTimeout(function () { loader.remove(); }, hold + 3000);
            try { sessionStorage.setItem('gatsu-loaded', '1'); } catch (e) {}
        });
    }

    /* Menu layar penuh */
    var menu = document.querySelector('[data-menu]');
    var openers = document.querySelectorAll('[data-menu-open]');
    var closers = document.querySelectorAll('[data-menu-close]');
    var lastFocus = null;

    function setMenu(open) {
        if (!menu) return;
        // Lingkaran menu membesar/mengecil dari tengah tombol Menu.
        var origin = document.querySelector('[data-menu-open]');
        if (origin) {
            var r = origin.getBoundingClientRect();
            menu.style.setProperty('--mx', (r.left + r.width / 2) + 'px');
            menu.style.setProperty('--my', (r.top + r.height / 2) + 'px');
        }
        menu.classList.toggle('is-open', open);
        menu.setAttribute('aria-hidden', open ? 'false' : 'true');
        document.body.classList.toggle('is-locked', open);
        openers.forEach(function (b) { b.setAttribute('aria-expanded', open ? 'true' : 'false'); });
        if (open) {
            lastFocus = document.activeElement;
            var first = menu.querySelector('a, button');
            if (first) first.focus();
        } else if (lastFocus) {
            lastFocus.focus();
        }
    }
    openers.forEach(function (b) { b.addEventListener('click', function () { setMenu(true); }); });
    closers.forEach(function (b) { b.addEventListener('click', function () { setMenu(false); }); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && menu && menu.classList.contains('is-open')) setMenu(false);
    });

    /* Slider kartu (hero): [data-slider] berisi [data-slide], tombol [data-prev]/[data-next], titik [data-dots]. */
    document.querySelectorAll('[data-slider]').forEach(function (slider) {
        var slides = slider.querySelectorAll('[data-slide]');
        var dotsWrap = slider.querySelector('[data-dots]');
        if (slides.length < 2) {
            slider.querySelectorAll('[data-prev], [data-next], [data-dots]').forEach(function (el) { el.hidden = true; });
            return;
        }
        var index = 0, timer = null;
        var dots = [];

        slides.forEach(function (_, i) {
            var dot = document.createElement('button');
            dot.type = 'button';
            dot.setAttribute('aria-label', 'Slide ' + (i + 1));
            dot.addEventListener('click', function () { go(i); restart(); });
            dotsWrap.appendChild(dot);
            dots.push(dot);
        });

        function go(i) {
            index = (i + slides.length) % slides.length;
            slides.forEach(function (s, n) { s.classList.toggle('is-active', n === index); });
            dots.forEach(function (d, n) { d.classList.toggle('is-active', n === index); });
        }
        function restart() {
            clearInterval(timer);
            timer = setInterval(function () { go(index + 1); }, 7000);
        }

        slider.querySelector('[data-prev]').addEventListener('click', function () { go(index - 1); restart(); });
        slider.querySelector('[data-next]').addEventListener('click', function () { go(index + 1); restart(); });
        go(0);
        restart();
    });

    /* Deret kartu yang bisa digeser: tombol [data-scroll-prev]/[data-scroll-next] menggeser [data-scroll-track]. */
    document.querySelectorAll('[data-scroller]').forEach(function (wrap) {
        var track = wrap.querySelector('[data-scroll-track]');
        function step(dir) {
            var card = track.firstElementChild;
            var amount = card ? card.getBoundingClientRect().width + 24 : track.clientWidth;
            track.scrollBy({ left: dir * amount, behavior: 'smooth' });
        }
        wrap.querySelector('[data-scroll-prev]').addEventListener('click', function () { step(-1); });
        wrap.querySelector('[data-scroll-next]').addEventListener('click', function () { step(1); });
    });

    /* Kartu yang bisa dibalik (DNA Kami) */
    document.querySelectorAll('[data-flip]').forEach(function (card) {
        card.addEventListener('click', function () {
            var flipped = card.classList.toggle('is-flipped');
            card.setAttribute('aria-pressed', flipped ? 'true' : 'false');
        });
    });

    /* Hero beranda: kartu "masuk" ke dalam hero saat digulir (nilai --p 0..1, dihaluskan). */
    var hero = document.querySelector('[data-hero-scroll]');
    if (hero && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        var target = 0, current = 0, frame = 0;
        var tick = function () {
            current += (target - current) * 0.18;
            if (Math.abs(target - current) < 0.001) current = target;
            hero.style.setProperty('--p', current.toFixed(4));
            frame = current === target ? 0 : requestAnimationFrame(tick);
        };
        var onScroll = function () {
            target = Math.min(1, Math.max(0, window.scrollY / hero.offsetHeight));
            if (!frame) frame = requestAnimationFrame(tick);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    /* Cari gereja (beranda, "Gereja Terdekat"): tombol Cari -> kolom ketik, hasil dari /gereja/cari tampil di dropdown.
       Teks pesan diambil dari data-msg-* (sudah diterjemahkan di Blade). */
    document.querySelectorAll('[data-church-search]').forEach(function (box) {
        var openBtn = box.querySelector('[data-cs-open]');
        var panel = box.querySelector('[data-cs-panel]');
        var input = box.querySelector('[data-cs-input]');
        var closeBtn = box.querySelector('[data-cs-close]');
        var statusEl = box.querySelector('[data-cs-status]');
        var chips = box.querySelector('[data-cs-chips]');
        var list = box.querySelector('[data-cs-list]');
        var url = box.getAttribute('data-url');
        var timer = null, controller = null, lastQuery = '', active = -1;

        function msg(name, vars) {
            var text = box.getAttribute('data-msg-' + name) || '';
            Object.keys(vars || {}).forEach(function (k) { text = text.split(':' + k).join(vars[k]); });
            return text;
        }
        function esc(text) {
            return String(text == null ? '' : text).replace(/[&<>"']/g, function (c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
            });
        }
        function mark(text, q) {
            var safe = esc(text);
            if (!q) return safe;
            var pattern = esc(q).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            return safe.replace(new RegExp('(' + pattern + ')', 'ig'), '<mark>$1</mark>');
        }
        var icon = {
            pin: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 21s-7-6.2-7-12a7 7 0 0 1 14 0c0 5.8-7 12-7 12z"/><circle cx="12" cy="9" r="2.5"/></svg>',
            clock: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>',
            map: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 4L3 6v14l6-2 6 2 6-2V4l-6 2-6-2z"/><path d="M9 4v14M15 6v14"/></svg>',
            phone: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/></svg>'
        };
        var linkNames = { instagram: 'Instagram', youtube: 'YouTube', facebook: 'Facebook', website: 'Website' };

        function setOpen(open) {
            panel.hidden = !open;
            openBtn.hidden = open;
            openBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (open) {
                input.focus();
                if (!input.value) showHint();
            } else {
                if (controller) controller.abort();
                openBtn.focus();
            }
        }
        function showHint() {
            statusEl.textContent = msg('hint');
            chips.hidden = false;
            list.innerHTML = '';
            input.setAttribute('aria-expanded', 'false');
        }
        function showLoading() {
            statusEl.textContent = msg('loading');
            chips.hidden = true;
            list.innerHTML = '<li class="cs-skeleton"></li><li class="cs-skeleton"></li><li class="cs-skeleton"></li>';
        }
        function render(res, q) {
            active = -1;
            chips.hidden = true;
            if (!res.data || !res.data.length) {
                statusEl.textContent = '';
                list.innerHTML = '<li class="cs-empty"><strong>🙏</strong>' + esc(msg('empty', { q: q })) + '</li>';
                input.setAttribute('aria-expanded', 'false');
                return;
            }
            var info = msg('found', { count: res.total, q: q });
            if (res.total > res.data.length) info += ' · ' + msg('more', { shown: res.data.length, count: res.total });
            statusEl.textContent = info;

            list.innerHTML = res.data.map(function (g, i) {
                var place = [g.kota, g.provinsi, g.negara].filter(Boolean).join(', ');
                var times = (g.jadwal || []).map(function (j) {
                    return '<span class="cs-time" title="' + esc(j.nama) + '">' + icon.clock + esc(j.jam || j.nama) + '</span>';
                }).join('') || '<span class="cs-time cs-time--muted">' + esc(msg('noschedule')) + '</span>';
                var links = '';
                var t = g.tautan || {};
                if (t.maps) links += '<a class="cs-link" href="' + esc(t.maps) + '" target="_blank" rel="noopener">' + icon.map + 'Maps</a>';
                if (g.telpon) links += '<a class="cs-link cs-link--ghost" href="tel:' + esc(g.telpon.replace(/[^+\d]/g, '')) + '">' + icon.phone + esc(msg('call')) + '</a>';
                Object.keys(linkNames).forEach(function (k) {
                    if (t[k]) links += '<a class="cs-link cs-link--ghost" href="' + esc(t[k]) + '" target="_blank" rel="noopener">' + linkNames[k] + '</a>';
                });
                return '<li role="option" id="cs-opt-' + i + '" aria-selected="false"><article class="cs-item">' +
                    '<div class="cs-item__head"><span class="cs-item__pin">' + icon.pin + '</span><div>' +
                    '<h4>' + mark(g.nama, q) + '</h4>' +
                    (place ? '<p class="cs-item__place">' + mark(place, q) + '</p>' : '') +
                    '</div></div>' +
                    (g.alamat ? '<p class="cs-item__addr">' + mark(g.alamat, q) + '</p>' : '') +
                    '<div class="cs-item__times">' + times + '</div>' +
                    (links ? '<div class="cs-item__links">' + links + '</div>' : '') +
                    '</article></li>';
            }).join('');
            input.setAttribute('aria-expanded', 'true');
        }
        function search(q) {
            if (controller) controller.abort();
            if (q.length < 2) { lastQuery = ''; showHint(); return; }
            if (q === lastQuery) return;
            lastQuery = q;
            showLoading();
            controller = 'AbortController' in window ? new AbortController() : null;
            fetch(url + '?q=' + encodeURIComponent(q), { headers: { Accept: 'application/json' }, signal: controller ? controller.signal : undefined })
                .then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); })
                .then(function (res) { if (q === lastQuery) render(res, q); })
                .catch(function (e) {
                    if (e.name === 'AbortError') return;
                    lastQuery = '';
                    statusEl.textContent = '';
                    list.innerHTML = '<li class="cs-empty"><strong>😔</strong>' + esc(msg('error')) + '</li>';
                });
        }
        function move(step) {
            var items = list.querySelectorAll('[role="option"]');
            if (!items.length) return;
            if (items[active]) { items[active].firstChild.classList.remove('is-active'); items[active].setAttribute('aria-selected', 'false'); }
            active = (active + step + items.length) % items.length;
            items[active].firstChild.classList.add('is-active');
            items[active].setAttribute('aria-selected', 'true');
            input.setAttribute('aria-activedescendant', items[active].id);
            items[active].scrollIntoView({ block: 'nearest' });
        }

        openBtn.addEventListener('click', function () { setOpen(true); });
        closeBtn.addEventListener('click', function () { input.value = ''; lastQuery = ''; setOpen(false); });
        input.addEventListener('input', function () {
            clearTimeout(timer);
            var q = input.value.trim();
            timer = setTimeout(function () { search(q); }, 300);
        });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowDown') { e.preventDefault(); move(1); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); move(-1); }
            else if (e.key === 'Enter') {
                e.preventDefault();
                var item = list.querySelectorAll('[role="option"]')[active];
                var maps = item && item.querySelector('.cs-link');
                if (maps) window.open(maps.href, '_blank', 'noopener');
                else search(input.value.trim());
            } else if (e.key === 'Escape') { input.value = ''; lastQuery = ''; setOpen(false); }
        });
        chips.addEventListener('click', function (e) {
            var b = e.target.closest('[data-cs-suggest]');
            if (!b) return;
            input.value = b.textContent.trim();
            input.focus();
            search(input.value);
        });
        document.addEventListener('click', function (e) {
            if (!panel.hidden && !box.contains(e.target) && !input.value) setOpen(false);
        });
    });

    /* Etalase pelayanan (halaman Pelayanan): gambar kecil / tombol "berikutnya" mengganti pelayanan yang tampil. */
    document.querySelectorAll('[data-showcase]').forEach(function (box) {
        var tabs = box.querySelectorAll('[data-showcase-tab]');
        var slides = box.querySelectorAll('[data-showcase-slide]');
        var current = 0;
        function show(i) {
            current = (i + slides.length) % slides.length;
            slides.forEach(function (s, n) { s.hidden = n !== current; s.classList.toggle('is-active', n === current); });
            tabs.forEach(function (t, n) { t.classList.toggle('is-active', n === current); t.setAttribute('aria-selected', n === current ? 'true' : 'false'); });
        }
        tabs.forEach(function (t, n) { t.addEventListener('click', function () { show(n); }); });
        box.querySelectorAll('[data-showcase-next]').forEach(function (b) { b.addEventListener('click', function () { show(current + 1); }); });
        box.addEventListener('keydown', function (e) {
            if (e.target.closest('[data-showcase-tab]') && (e.key === 'ArrowRight' || e.key === 'ArrowLeft')) {
                show(current + (e.key === 'ArrowRight' ? 1 : -1));
                tabs[current].focus();
            }
        });
    });

    /* Video latar: berhenti kalau pengguna memilih "kurangi gerakan". */
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.querySelectorAll('video[autoplay]').forEach(function (v) { v.pause(); });
    }
})();
