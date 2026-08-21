// ==========================================================================
// AgenceImmo — interactions
// ==========================================================================
(function () {
    'use strict';

    var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

    // --- Galerie : cliquer une miniature change l'image principale ---
    document.addEventListener('click', function (e) {
        var thumb = e.target.closest('.thumb');
        if (!thumb) return;
        var main = document.getElementById('gallery-main-img');
        if (main && thumb.dataset.src) {
            main.src = thumb.dataset.src;
            document.querySelectorAll('.thumb').forEach(function (t) { t.classList.remove('active'); });
            thumb.classList.add('active');
        }
    });

    // --- Ombre du header au défilement ---
    var header = document.querySelector('.site-header');
    if (header) {
        var onScroll = function () { header.classList.toggle('scrolled', window.scrollY > 8); };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    var hasIO = 'IntersectionObserver' in window;

    // --- Compteurs animés (tableau de bord) ---
    function countUp(el) {
        var target = parseInt(el.textContent, 10);
        if (isNaN(target)) return;
        if (reduce) { el.textContent = target; return; }
        var start = performance.now(), dur = 1100;
        function tick(now) {
            var p = Math.min((now - start) / dur, 1);
            var eased = 1 - Math.pow(1 - p, 3); // easeOutCubic
            el.textContent = Math.round(target * eased);
            if (p < 1) requestAnimationFrame(tick);
            else el.textContent = target;
        }
        requestAnimationFrame(tick);
    }

    var kpis = document.querySelectorAll('.kpi-num');
    if (kpis.length) {
        if (!hasIO || reduce) { kpis.forEach(countUp); }
        else {
            var kio = new IntersectionObserver(function (entries) {
                entries.forEach(function (en) {
                    if (en.isIntersecting) { countUp(en.target); kio.unobserve(en.target); }
                });
            }, { threshold: 0.4 });
            kpis.forEach(function (el) { kio.observe(el); });
        }
    }

    // --- Révélation au scroll ---
    var targets = document.querySelectorAll('.reveal, .stagger');
    if (reduce || !hasIO) {
        targets.forEach(function (el) { el.classList.add('is-visible'); });
        return;
    }
    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
    targets.forEach(function (el) { io.observe(el); });
}());
