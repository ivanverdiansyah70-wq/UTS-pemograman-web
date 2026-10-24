// Snow Festival - main.js (vanilla JS, tanpa library)
(function () {
    'use strict';

    var DESIGN_W = 1440;
    var DESIGN_H = 850;

    var stage = document.getElementById('stage');
    var overlay = document.getElementById('login-overlay');
    var faqPopup = document.getElementById('faq-popup');
    var loggedIn = stage.getAttribute('data-logged-in') === '1';

    // ---------- 1. Skala stage supaya memenuhi layar (seperti background-size: cover) ----------
    function fitStage() {
        var s = Math.max(window.innerWidth / DESIGN_W, window.innerHeight / DESIGN_H);
        stage.style.left = (window.innerWidth - DESIGN_W) / 2 + 'px';
        stage.style.top = (window.innerHeight - DESIGN_H) / 2 + 'px';
        stage.style.transform = 'scale(' + s + ')';
    }
    fitStage();
    window.addEventListener('resize', fitStage);

    // ---------- 2. Splash (Snow 1) -> Snow 2 ----------
    var splashTimer = null;

    function endSplash() {
        if (!stage.classList.contains('is-splash')) { return; }
        clearTimeout(splashTimer);
        // Menghapus kelas membuat CSS transition menggerakkan semua layer ke posisi Snow 2
        stage.classList.remove('is-splash');
    }

    if (stage.classList.contains('is-splash')) {
        splashTimer = setTimeout(endSplash, 2500);   // otomatis setelah 2.5 detik
        stage.addEventListener('click', endSplash);  // atau saat diklik
    }

    // ---------- 3. Overlay login (Snow 3) ----------
    function openLogin() {
        faqPopup.hidden = true;
        overlay.classList.add('is-open');
        var first = overlay.querySelector('input[name="username"]');
        if (first) { first.focus(); }
    }

    function closeLogin() {
        overlay.classList.remove('is-open');
    }

    document.getElementById('login-close').addEventListener('click', closeLogin);

    // Klik di luar panel (di area blur) menutup overlay
    overlay.addEventListener('click', function (event) {
        if (event.target === overlay) { closeLogin(); }
    });

    // Tombol Esc menutup overlay / popup
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeLogin();
            faqPopup.hidden = true;
        }
    });

    // Buka overlay bila sudah ada kelas is-open dari PHP: fokuskan input
    if (overlay.classList.contains('is-open')) {
        var firstInput = overlay.querySelector('input[name="username"]');
        if (firstInput) { firstInput.focus(); }
    }

    // ---------- 4. Tombol Start, FAQ, dan ikon user ----------
    function showPopupMessage(html) {
        faqPopup.innerHTML = html + '<button type="button" id="faq-close">Tutup</button>';
        faqPopup.hidden = false;
        document.getElementById('faq-close').addEventListener('click', function () {
            faqPopup.hidden = true;
        });
    }

    document.getElementById('btn-start').addEventListener('click', function () {
        if (loggedIn) {
            window.location.href = 'dashboard.php';
        } else {
            openLogin();
        }
    });

    document.getElementById('btn-faq').addEventListener('click', function () {
        showPopupMessage(
            '<strong>FAQ</strong>' +
            '<p>Apa itu Snow Festival? Perayaan musim dingin tahunan.</p>' +
            '<p>Perlu akun? Ya, login lewat tombol Start.</p>' +
            '<p>Gratis? Ya, tanpa biaya masuk.</p>'
        );
    });

    document.getElementById('open-user').addEventListener('click', function () {
        // Sudah login -> buka dashboard; belum -> buka form login
        if (loggedIn) { window.location.href = 'dashboard.php'; }
        else { openLogin(); }
    });

    // Tombol Tutup di popup bawaan HTML
    document.getElementById('faq-close').addEventListener('click', function () {
        faqPopup.hidden = true;
    });
})();
