{{-- Custom Select mandiri (CSS + JS) — dropdown ber-style Molife, mengganti
     daftar opsi bawaan OS. Dipakai di layout admin (dan bisa di layout lain). --}}
<style>
    .cs-wrapper { position: relative; display: block; }
    .cs-trigger {
        display: flex; align-items: center; justify-content: space-between; gap: 8px;
        width: 100%; text-align: left;
        background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px;
        padding: 10px 12px; font-size: 14px; font-family: inherit;
        color: #111827; cursor: pointer; transition: border-color .15s, background .15s;
    }
    .cs-trigger:hover { border-color: #9ca3af; }
    .cs-trigger.cs-white { background: #fff; }
    .cs-trigger.open, .cs-trigger:focus { border-color: #000; background: #fff; outline: none; box-shadow: 0 0 0 3px rgba(0,0,0,0.06); }
    .cs-trigger-label { flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cs-chevron { flex-shrink: 0; width: 14px; height: 14px; color: #9ca3af; transition: transform .2s, color .15s; }
    .cs-trigger.open .cs-chevron { transform: rotate(180deg); color: #111; }
    .cs-dropdown {
        background: #fff; border-radius: 16px;
        box-shadow: 0 12px 40px rgba(0,0,0,.13), 0 2px 8px rgba(0,0,0,.07);
        z-index: 99999; padding: 6px;
        max-height: 280px; overflow-y: auto;
    }
    .cs-option {
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        padding: 10px 14px; border-radius: 10px;
        cursor: pointer; font-size: 14px; font-family: inherit;
        color: #374151; transition: background .1s; white-space: nowrap;
    }
    .cs-option:hover { background: #f9fafb; }
    .cs-option.cs-selected { font-weight: 700; color: #111827; }
    .cs-check { flex-shrink: 0; color: #6366f1; width: 15px; height: 15px; stroke-width: 2.5; }
</style>

<script>
/* Custom Select — mengubah semua <select> jadi dropdown ber-style kita. */
(function () {
    var openTrigger = null, openDropdown = null;

    function closeAll() {
        if (openDropdown) { openDropdown.remove(); openDropdown = null; }
        if (openTrigger)  { openTrigger.classList.remove('open'); openTrigger = null; }
    }

    document.addEventListener('click', closeAll);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeAll(); });

    function syncLabel(select, trigger) {
        var opt = select.options[select.selectedIndex];
        trigger.querySelector('.cs-trigger-label').textContent = opt ? opt.text : '';
    }

    function initCustomSelects() {
        document.querySelectorAll('select:not([data-cs])').forEach(function (select) {
            select.dataset.cs = '1';

            var wrapper = document.createElement('div');
            wrapper.className = 'cs-wrapper';

            var trigger = document.createElement('button');
            trigger.type = 'button';
            trigger.className = 'cs-trigger';
            if (select.classList.contains('bg-white')) trigger.classList.add('cs-white');
            trigger.innerHTML =
                '<span class="cs-trigger-label"></span>' +
                '<svg class="cs-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>';

            syncLabel(select, trigger);

            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                if (openTrigger === trigger) { closeAll(); return; }
                closeAll();

                var dd = document.createElement('div');
                dd.className = 'cs-dropdown';
                dd.style.position = 'fixed';

                Array.from(select.options).forEach(function (opt) {
                    var isSelected = opt.value === select.value;
                    var item = document.createElement('div');
                    item.className = 'cs-option' + (isSelected ? ' cs-selected' : '');
                    item.innerHTML =
                        '<span>' + opt.text + '</span>' +
                        '<svg class="cs-check" fill="none" stroke="currentColor" viewBox="0 0 24 24"' +
                        ' style="visibility:' + (isSelected ? 'visible' : 'hidden') + '">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';

                    item.addEventListener('click', function (e2) {
                        e2.stopPropagation();
                        select.value = opt.value;
                        syncLabel(select, trigger);
                        select.dispatchEvent(new Event('change'));
                        closeAll();
                    });
                    dd.appendChild(item);
                });

                var rect = trigger.getBoundingClientRect();
                dd.style.left  = rect.left + 'px';
                dd.style.width = rect.width + 'px';
                dd.style.top   = (rect.bottom + 6) + 'px';
                document.body.appendChild(dd);

                requestAnimationFrame(function () {
                    var gap = 6, margin = 8;
                    var spaceBelow = window.innerHeight - rect.bottom - gap - margin;
                    var spaceAbove = rect.top - gap - margin;
                    var dh = dd.offsetHeight;

                    if (dh <= spaceBelow || spaceBelow >= spaceAbove) {
                        dd.style.top = (rect.bottom + gap) + 'px';
                        dd.style.maxHeight = Math.min(280, Math.max(120, spaceBelow)) + 'px';
                    } else {
                        var h = Math.min(280, Math.max(120, spaceAbove));
                        dd.style.maxHeight = h + 'px';
                        dd.style.top = (rect.top - Math.min(dd.offsetHeight, h) - gap) + 'px';
                    }
                    var selEl = dd.querySelector('.cs-selected');
                    if (selEl) selEl.scrollIntoView({ block: 'nearest' });
                });

                openDropdown = dd;
                openTrigger  = trigger;
                trigger.classList.add('open');
            });

            select.style.display = 'none';
            select.parentNode.insertBefore(wrapper, select);
            wrapper.appendChild(trigger);
            wrapper.appendChild(select);

            select._csRefresh = function () { syncLabel(select, trigger); };
        });
    }

    window.initCustomSelects = initCustomSelects;
    window.refreshSelect = function (el) {
        if (typeof el === 'string') el = document.getElementById(el);
        if (el && el._csRefresh) el._csRefresh();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCustomSelects);
    } else {
        initCustomSelects();
    }
})();
</script>
