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
    /* Slider kartu (Highlights): geser 1 kartu per klik, berputar di ujung, jalan otomatis tiap 5 detik
       (berhenti saat kursor/jari di atas slider atau tab tidak terlihat). */
    document.querySelectorAll('[data-scroller]').forEach(function (wrap) {
        var track = wrap.querySelector('[data-scroll-track]');
        if (!track) return;
        var prev = wrap.querySelector('[data-scroll-prev]');
        var next = wrap.querySelector('[data-scroll-next]');
        var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        function step(dir) {
            var card = track.firstElementChild;
            var amount = card ? card.getBoundingClientRect().width + 24 : track.clientWidth;
            var max = track.scrollWidth - track.clientWidth;
            if (max <= 2) return;
            if (dir > 0 && track.scrollLeft >= max - 2) {
                track.scrollTo({ left: 0, behavior: reduce ? 'auto' : 'smooth' });
            } else if (dir < 0 && track.scrollLeft <= 2) {
                track.scrollTo({ left: max, behavior: reduce ? 'auto' : 'smooth' });
            } else {
                track.scrollBy({ left: dir * amount, behavior: reduce ? 'auto' : 'smooth' });
            }
        }

        var timer = 0;
        function stop() { clearInterval(timer); timer = 0; }
        function start() {
            stop();
            if (!reduce && track.scrollWidth - track.clientWidth > 2) timer = setInterval(function () { step(1); }, 5000);
        }

        if (prev) prev.addEventListener('click', function () { step(-1); start(); });
        if (next) next.addEventListener('click', function () { step(1); start(); });
        wrap.addEventListener('mouseenter', stop);
        wrap.addEventListener('mouseleave', start);
        wrap.addEventListener('touchstart', stop, { passive: true });
        wrap.addEventListener('touchend', start, { passive: true });
        wrap.addEventListener('focusin', stop);
        wrap.addEventListener('focusout', start);
        document.addEventListener('visibilitychange', function () { document.hidden ? stop() : start(); });
        window.addEventListener('resize', start);
        start();
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

    /* Etalase pelayanan (halaman Pelayanan): berganti pelayanan saat digulir, atau lewat gambar kecil / tombol "berikutnya". */
    document.querySelectorAll('[data-showcase]').forEach(function (box) {
        var tabs = box.querySelectorAll('[data-showcase-tab]');
        var slides = box.querySelectorAll('[data-showcase-slide]');
        var current = 0;
        function show(i) {
            current = (i + slides.length) % slides.length;
            slides.forEach(function (s, n) { s.hidden = n !== current; s.classList.toggle('is-active', n === current); });
            tabs.forEach(function (t, n) { t.classList.toggle('is-active', n === current); t.setAttribute('aria-selected', n === current ? 'true' : 'false'); });
        }
        // Mode gulir: etalase menempel, pelayanan berganti sesuai posisi gulir di dalam bagian ini.
        var scroller = box.closest('[data-showcase-scroll]');
        function scrollMode() { return scroller && !scroller.classList.contains('is-static'); }
        function checkFit() {
            if (scroller) scroller.classList.toggle('is-static', box.offsetHeight + 110 > window.innerHeight);
        }
        function fromScroll() {
            if (!scrollMode()) return;
            var r = scroller.getBoundingClientRect();
            var distance = scroller.offsetHeight - window.innerHeight;
            var p = distance > 0 ? Math.min(.999, Math.max(0, -r.top / distance)) : 0;
            var i = Math.floor(p * slides.length);
            if (i !== current) show(i);
        }
        function go(i) {
            i = (i + slides.length) % slides.length;
            if (!scrollMode()) { show(i); return; }
            var distance = scroller.offsetHeight - window.innerHeight;
            var top = scroller.getBoundingClientRect().top + window.pageYOffset;
            window.scrollTo({ top: top + distance * (i + .5) / slides.length, behavior: 'smooth' });
        }
        if (scroller) {
            var queued = false;
            window.addEventListener('scroll', function () {
                if (queued) return;
                queued = true;
                requestAnimationFrame(function () { queued = false; fromScroll(); });
            }, { passive: true });
            window.addEventListener('resize', function () { checkFit(); fromScroll(); });
            checkFit();
            fromScroll();
        }

        tabs.forEach(function (t, n) { t.addEventListener('click', function () { go(n); }); });
        box.querySelectorAll('[data-showcase-next]').forEach(function (b) { b.addEventListener('click', function () { go(current + 1); }); });
        box.addEventListener('keydown', function (e) {
            if (e.target.closest('[data-showcase-tab]') && (e.key === 'ArrowRight' || e.key === 'ArrowLeft')) {
                var target = (current + (e.key === 'ArrowRight' ? 1 : -1) + slides.length) % slides.length;
                go(target);
                tabs[target].focus();
            }
        });
    });

    /* Slider bertitik (Pengajaran, Artikel Terpopuler): [data-dot-slider] berisi [data-dot] & [data-dot-slide]. */
    document.querySelectorAll('[data-dot-slider]').forEach(function (box) {
        var dots = box.querySelectorAll('[data-dot]');
        var slides = box.querySelectorAll('[data-dot-slide]');
        if (slides.length < 2) return;
        var index = 0, timer = null;
        function go(i) {
            index = (i + slides.length) % slides.length;
            slides.forEach(function (s, n) { s.hidden = n !== index; s.classList.toggle('is-active', n === index); });
            dots.forEach(function (d, n) { d.classList.toggle('is-active', n === index); });
        }
        function restart() { clearInterval(timer); timer = setInterval(function () { go(index + 1); }, 7000); }
        dots.forEach(function (d, n) { d.addEventListener('click', function () { go(n); restart(); }); });
        box.addEventListener('mouseenter', function () { clearInterval(timer); });
        box.addEventListener('mouseleave', restart);
        restart();
    });

    /* Geser dengan mouse: [data-drag-scroll] bisa diseret ke kiri/kanan (dengan efek meluncur), di HP pakai geser jari bawaan. */
    document.querySelectorAll('[data-drag-scroll]').forEach(function (track) {
        var down = false, moved = false, startX = 0, startLeft = 0, lastX = 0, lastT = 0, speed = 0, glide = 0;

        track.addEventListener('pointerdown', function (e) {
            if (e.pointerType !== 'mouse' || e.button !== 0) return;
            cancelAnimationFrame(glide);
            down = true; moved = false;
            startX = lastX = e.clientX; startLeft = track.scrollLeft; lastT = performance.now(); speed = 0;
        });
        window.addEventListener('pointermove', function (e) {
            if (!down) return;
            var dx = e.clientX - startX;
            if (!moved && Math.abs(dx) > 5) { moved = true; track.classList.add('is-dragging'); }
            if (!moved) return;
            e.preventDefault();
            track.scrollLeft = startLeft - dx;
            var now = performance.now();
            speed = (e.clientX - lastX) / Math.max(1, now - lastT);
            lastX = e.clientX; lastT = now;
        });
        window.addEventListener('pointerup', function () {
            if (!down) return;
            down = false;
            if (!moved) return;
            // Meluncur sebentar sesuai kecepatan seret, lalu kembali menempel ke kartu terdekat.
            var v = speed * 16;
            (function step() {
                if (Math.abs(v) < 0.5) { track.classList.remove('is-dragging'); return; }
                track.scrollLeft -= v; v *= 0.92;
                glide = requestAnimationFrame(step);
            })();
        });
        // Klik setelah menyeret tidak membuka link.
        track.addEventListener('click', function (e) { if (moved) { e.preventDefault(); e.stopPropagation(); moved = false; } }, true);
    });

    /* Video latar: berhenti kalau pengguna memilih "kurangi gerakan". */
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.querySelectorAll('video[autoplay]').forEach(function (v) { v.pause(); });
    }
})();
