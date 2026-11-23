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

    /* Header tetap di atas: beri latar setelah halaman digulung sedikit. */
    var siteHeader = document.querySelector('[data-site-header]');
    if (siteHeader) {
        var onHeaderScroll = function () { siteHeader.classList.toggle('is-scrolled', window.scrollY > 40); };
        window.addEventListener('scroll', onHeaderScroll, { passive: true });
        onHeaderScroll();
    }

    /* Video latar: berhenti kalau pengguna memilih "kurangi gerakan". */
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.querySelectorAll('video[autoplay]').forEach(function (v) { v.pause(); });
    }
})();
