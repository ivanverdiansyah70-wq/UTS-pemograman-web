// Snow Festival - dashboard.js (vanilla JS, tanpa library)
// Menggambar grafik batang + garis dengan SVG dari window.CHART_DATA.
(function () {
    'use strict';

    // ---------- 1. Menu sidebar untuk layar kecil ----------
    var sidebar = document.getElementById('sidebar');
    var toggle = document.getElementById('menu-toggle');

    toggle.addEventListener('click', function () {
        var open = sidebar.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    // Tutup sidebar setelah memilih menu (di layar kecil)
    sidebar.addEventListener('click', function (event) {
        if (event.target.closest('.menu-item')) {
            sidebar.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });

    // ---------- 2. Grafik ----------
    var data = window.CHART_DATA || [];
    var svg = document.getElementById('chart');
    var tooltip = document.getElementById('tooltip');
    if (!svg || data.length === 0) { return; }

    var W = 760, H = 340;                               // ukuran viewBox
    var M = { top: 30, right: 60, bottom: 46, left: 64 };  // margin untuk sumbu
    var innerW = W - M.left - M.right;
    var innerH = H - M.top - M.bottom;
    var TICKS = 5;

    svg.setAttribute('viewBox', '0 0 ' + W + ' ' + H);

    // Bulatkan nilai maksimum ke angka "rapi" supaya label sumbu enak dibaca
    function niceMax(value) {
        var step = Math.pow(10, Math.floor(Math.log10(value / TICKS)));
        var options = [1, 2, 2.5, 5, 10];
        for (var i = 0; i < options.length; i++) {
            if (options[i] * step * TICKS >= value) { return options[i] * step * TICKS; }
        }
        return 10 * step * TICKS;
    }

    var maxRevenue = niceMax(Math.max.apply(null, data.map(function (d) { return d.pendapatan; })));
    var maxPlayers = niceMax(Math.max.apply(null, data.map(function (d) { return d.pemain; })));

    var band = innerW / data.length;          // lebar tiap kolom periode
    var barW = Math.min(56, band * 0.5);

    function xCenter(i) { return M.left + band * i + band / 2; }
    function yRevenue(v) { return M.top + innerH - (v / maxRevenue) * innerH; }
    function yPlayers(v) { return M.top + innerH - (v / maxPlayers) * innerH; }

    // Helper membuat elemen SVG
    function el(tag, attrs, parent) {
        var node = document.createElementNS('http://www.w3.org/2000/svg', tag);
        for (var key in attrs) { node.setAttribute(key, attrs[key]); }
        if (parent) { parent.appendChild(node); }
        return node;
    }
    function text(content, attrs, parent) {
        var node = el('text', attrs, parent);
        node.textContent = content;
        return node;
    }
    function fmt(n) { return n.toLocaleString('id-ID'); }

    // Garis bantu + label sumbu kiri (pendapatan) dan kanan (pemain)
    var grid = el('g', { 'class': 'grid' }, svg);
    var axis = el('g', { 'class': 'axis' }, svg);
    for (var t = 0; t <= TICKS; t++) {
        var y = M.top + innerH - (innerH / TICKS) * t;
        el('line', { x1: M.left, x2: W - M.right, y1: y, y2: y }, grid);
        text(fmt(maxRevenue / TICKS * t), { x: M.left - 10, y: y + 4, 'text-anchor': 'end' }, axis);
        text(fmt(maxPlayers / TICKS * t), { x: W - M.right + 10, y: y + 4, 'text-anchor': 'start' }, axis);
    }
    text('Juta USD', { 'class': 'axis-title', x: 0, y: M.top - 14, 'text-anchor': 'start' }, svg);
    text('Juta pemain', { 'class': 'axis-title', x: W, y: M.top - 14, 'text-anchor': 'end' }, svg);

    // Label periode di sumbu bawah
    data.forEach(function (d, i) {
        text(d.periode, { x: xCenter(i), y: H - M.bottom + 24, 'text-anchor': 'middle' }, axis);
    });

    // Batang pendapatan
    var barGroup = el('g', { 'data-series': 'pendapatan' }, svg);
    var bars = data.map(function (d, i) {
        var top = yRevenue(d.pendapatan);
        var bar = el('rect', {
            'class': 'bar',
            x: xCenter(i) - barW / 2, y: top,
            width: barW, height: M.top + innerH - top,
            rx: 6
        }, barGroup);
        bar.style.animationDelay = (i * 0.08) + 's';
        return bar;
    });

    // Garis + titik pemain
    var lineGroup = el('g', { 'data-series': 'pemain' }, svg);
    var points = data.map(function (d, i) { return xCenter(i) + ',' + yPlayers(d.pemain); });
    el('polyline', { 'class': 'line', points: points.join(' ') }, lineGroup);
    var dots = data.map(function (d, i) {
        return el('circle', { 'class': 'dot', cx: xCenter(i), cy: yPlayers(d.pemain), r: 5 }, lineGroup);
    });

    // ---------- 3. Tooltip saat kursor di atas kolom periode ----------
    var hitGroup = el('g', {}, svg);
    data.forEach(function (d, i) {
        var hit = el('rect', {
            'class': 'hit',
            x: M.left + band * i, y: M.top,
            width: band, height: innerH
        }, hitGroup);

        hit.addEventListener('mouseenter', function () { showTip(i); });
        hit.addEventListener('mouseleave', hideTip);
        hit.addEventListener('click', function () { showTip(i); });  // untuk layar sentuh
    });
    svg.addEventListener('mouseleave', hideTip);

    function showTip(i) {
        var d = data[i];
        bars.forEach(function (b, k) { b.classList.toggle('is-hover', k === i); });
        dots.forEach(function (c, k) { c.classList.toggle('is-hover', k === i); });

        tooltip.innerHTML =
            '<strong>Periode ' + d.periode + '</strong>' +
            'Pendapatan: $' + fmt(d.pendapatan) + ' jt<br>' +
            'Pemain: ' + fmt(d.pemain) + ' juta';
        tooltip.hidden = false;

        // Ubah koordinat viewBox -> piksel layar
        var scale = svg.getBoundingClientRect().width / W;
        var topY = Math.min(yRevenue(d.pendapatan), yPlayers(d.pemain));
        var left = xCenter(i) * scale;
        var half = tooltip.offsetWidth / 2;
        var maxLeft = svg.clientWidth - half;
        tooltip.style.left = Math.max(half, Math.min(left, maxLeft)) + 'px';
        tooltip.style.top = Math.max(topY * scale, tooltip.offsetHeight + 12) + 'px';
    }

    function hideTip() {
        tooltip.hidden = true;
        bars.forEach(function (b) { b.classList.remove('is-hover'); });
        dots.forEach(function (c) { c.classList.remove('is-hover'); });
    }

    // ---------- 4. Legend: klik untuk sembunyikan / tampilkan seri ----------
    document.querySelectorAll('.legend-item').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var on = btn.getAttribute('aria-pressed') !== 'true';
            btn.setAttribute('aria-pressed', on ? 'true' : 'false');
            var group = svg.querySelector('g[data-series="' + btn.dataset.series + '"]');
            Array.prototype.forEach.call(group.children, function (node) {
                node.classList.toggle('is-hidden', !on);
            });
        });
    });
})();
