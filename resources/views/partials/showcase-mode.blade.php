{{--
    MODE PERAGA (SHOWCASE) — khusus akun konten/marketing.

    Di-include HANYA saat auth()->user()->is_showcase = true, jadi tidak ada
    satu baris pun dari berkas ini yang dijalankan user biasa.

    Efek: saat halaman dibuka, kartu muncul bertahap, angka menghitung naik
    dari nol, dan bilah/cincin progres terisi pelan. Cocok untuk rekaman layar.

    Matikan sementara dengan menambahkan ?noanim=1 pada URL.
--}}
<style>
    .sc-hidden { opacity: 0 !important; transform: translateY(16px); }
    .sc-show {
        opacity: 1 !important;
        transform: none;
        transition: opacity .55s cubic-bezier(.22,1,.36,1), transform .55s cubic-bezier(.22,1,.36,1);
    }
</style>

<script>
(function () {
    'use strict';

    if (new URLSearchParams(location.search).get('noanim') === '1') return;

    var STAGGER   = 70,    // jeda antar kartu (ms)
        REVEAL    = 550,   // durasi muncul kartu
        COUNT_DUR = 1100,  // durasi angka menghitung naik
        BAR_DUR   = 950;   // durasi bilah progres terisi

    function ready(fn) {
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn);
        else fn();
    }

    /* ── Angka: baca & tulis ulang dengan format aslinya ── */
    var NUM_RE = /^(\D*?)(\d[\d.,]*)(\D*)$/;

    // Titik bisa berarti desimal (Inggris: "37.8") atau pemisah ribuan
    // (Indonesia: "12.500.000"). Kelompok ribuan selalu 3 digit, jadi titik
    // yang diikuti 1-2 digit di akhir pasti desimal.
    function dotDecimals(s) {
        var m = s.match(/^\d+\.(\d{1,2})$/);
        return m ? m[1].length : 0;
    }
    function commaDecimals(s) {
        var m = s.match(/,(\d{1,2})$/);
        return m ? m[1].length : 0;
    }

    function parseNumber(str) {
        if (commaDecimals(str)) return parseFloat(str.replace(/\./g, '').replace(',', '.'));
        if (dotDecimals(str)) return parseFloat(str);
        return parseFloat(str.replace(/[.,]/g, ''));
    }

    function formatLike(value, sample) {
        var cd = commaDecimals(sample);
        if (cd) {
            var head = Math.floor(Math.abs(value)).toLocaleString('id-ID');
            var tail = Math.abs(value).toFixed(cd).split('.')[1];
            return (value < 0 ? '-' : '') + head + ',' + tail;
        }
        var dd = dotDecimals(sample);
        if (dd) return value.toFixed(dd);

        var rounded = Math.round(value);
        if (sample.indexOf('.') !== -1) return rounded.toLocaleString('id-ID');
        return String(rounded);
    }

    /** Elemen daun yang isinya sebuah angka dan layak dianimasikan. */
    function numberTargets(root) {
        var out = [];
        var walker = document.createTreeWalker(root, NodeFilter.SHOW_ELEMENT, null);
        var el = root.nodeType === 1 && root.children.length === 0 ? root : walker.nextNode();
        while (el) {
            if (el.children.length === 0) {
                var raw = (el.textContent || '').trim();
                if (raw && raw.length <= 24 && raw.indexOf('/') === -1 && raw.indexOf(':') === -1) {
                    var m = raw.match(NUM_RE);
                    if (m) {
                        var val = parseNumber(m[2]);
                        var plainYear = !m[1] && !m[3] && m[2].length === 4 && val >= 1900 && val <= 2100;
                        if (isFinite(val) && val > 0 && !plainYear) {
                            out.push({ el: el, prefix: m[1], suffix: m[3], sample: m[2], value: val });
                        }
                    }
                }
            }
            el = walker.nextNode();
        }
        return out;
    }

    /* Semua penyelesai angka yang masih berjalan — dipakai jaring pengaman. */
    var pendingFinishers = [];

    function finishAllCounters() {
        pendingFinishers.splice(0).forEach(function (fn) { fn(); });
    }

    // requestAnimationFrame berhenti saat tab tidak digambar. Begitu halaman
    // disembunyikan, langsung kunci semua angka ke nilai akhirnya supaya tidak
    // ada angka yang macet di nol.
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) finishAllCounters();
    });

    function countUp(t) {
        var start = null, done = false;
        var finalText = t.prefix + formatLike(t.value, t.sample) + t.suffix;

        function finish() {
            if (done) return;
            done = true;
            t.el.textContent = finalText;
        }
        pendingFinishers.push(finish);

        t.el.textContent = t.prefix + formatLike(0, t.sample) + t.suffix;

        function step(ts) {
            if (done) return;
            if (start === null) start = ts;
            var p = Math.min(1, (ts - start) / COUNT_DUR);
            var eased = 1 - Math.pow(1 - p, 3);
            t.el.textContent = t.prefix + formatLike(t.value * eased, t.sample) + t.suffix;
            if (p < 1) requestAnimationFrame(step);
            else finish();
        }
        requestAnimationFrame(step);

        // Jaring pengaman: apa pun yang terjadi pada rAF, nilai akhir tetap tampil.
        setTimeout(finish, COUNT_DUR + 500);
    }

    /* ── Bilah & cincin progres ── */
    function fillBars(root) {
        root.querySelectorAll('[style*="width"]').forEach(function (el) {
            var w = el.style.width;
            if (!w || w.indexOf('%') === -1) return;
            var pct = parseFloat(w);
            if (!isFinite(pct) || pct <= 0) return;
            el.style.transition = 'none';
            el.style.width = '0%';
            requestAnimationFrame(function () {
                el.style.transition = 'width ' + BAR_DUR + 'ms cubic-bezier(.22,1,.36,1)';
                el.style.width = pct + '%';
            });
        });

        root.querySelectorAll('circle[stroke-dasharray]').forEach(function (c) {
            var off = c.getAttribute('stroke-dashoffset');
            var dash = c.getAttribute('stroke-dasharray');
            if (off === null) return;
            var full = parseFloat(dash);
            if (!isFinite(full)) return;
            c.style.transition = 'none';
            c.setAttribute('stroke-dashoffset', String(full));
            requestAnimationFrame(function () {
                c.style.transition = 'stroke-dashoffset ' + BAR_DUR + 'ms cubic-bezier(.22,1,.36,1)';
                c.setAttribute('stroke-dashoffset', off);
            });
        });
    }

    /* ── Pilih kartu terluar yang pantas dianimasikan ── */
    function collectBlocks(root) {
        var candidates = Array.prototype.slice.call(
            root.querySelectorAll('.rounded-2xl, .rounded-3xl, table, .bg-white')
        ).filter(function (el) {
            return el.offsetHeight >= 48 && el.offsetWidth >= 48;
        });

        var set = new Set(candidates);
        var outer = candidates.filter(function (el) {
            for (var p = el.parentElement; p && p !== root; p = p.parentElement) {
                if (set.has(p)) return false;
            }
            return true;
        });

        outer.sort(function (a, b) {
            var ra = a.getBoundingClientRect(), rb = b.getBoundingClientRect();
            return (ra.top - rb.top) || (ra.left - rb.left);
        });
        return outer;
    }

    /**
     * Kembalikan elemen ke keadaan normal SECARA PASTI.
     * Transisi CSS ikut dipaksa selesai, karena transisi membeku saat tab
     * tidak digambar (pindah tab / jendela tersembunyi) dan bisa membuat
     * kartu tertahan transparan selamanya.
     */
    function finalizeBlock(el) {
        el.classList.remove('sc-hidden', 'sc-show');
        el.style.transition = '';
        el.style.opacity = '';
        el.style.transform = '';
        if (el.getAnimations) {
            el.getAnimations().forEach(function (a) {
                try { a.finish(); } catch (e) { try { a.cancel(); } catch (e2) {} }
            });
        }
    }

    ready(function () {
        var root = document.getElementById('pageContent') || document.querySelector('main');
        if (!root) return;

        var blocks = [];

        function finalizeAll() {
            blocks.forEach(finalizeBlock);
            root.querySelectorAll('.sc-hidden, .sc-show').forEach(finalizeBlock);
            finishAllCounters();
        }

        // Begitu halaman disembunyikan, hentikan semua efek di posisi akhir.
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) finalizeAll();
        });

        try {
            blocks = collectBlocks(root);
            if (!blocks.length) { fillBars(root); return; }

            blocks.forEach(function (b) { b.classList.add('sc-hidden'); });

            blocks.forEach(function (b, i) {
                setTimeout(function () {
                    b.classList.add('sc-show');
                    numberTargets(b).forEach(countUp);
                    fillBars(b);
                    setTimeout(function () { finalizeBlock(b); }, REVEAL + 80);
                }, i * STAGGER);
            });

            // Jaring pengaman terakhir: pastikan tidak ada yang tertinggal tersembunyi.
            setTimeout(finalizeAll, blocks.length * STAGGER + REVEAL + 2000);
        } catch (e) {
            finalizeAll();
        }
    });
})();
</script>
