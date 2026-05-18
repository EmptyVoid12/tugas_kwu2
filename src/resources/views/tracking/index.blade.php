<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Tracking</title>
    <style>
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 110px;
        }

        :root {
            color-scheme: light;
            --bg: #f2f7f6;
            --panel: rgba(255, 255, 255, 0.92);
            --text: #11243d;
            --muted: #5f7188;
            --line: rgba(17, 36, 61, 0.12);
            --primary: #0f766e;
            --primary-dark: #115e59;
            --primary-soft: #ccfbf1;
            --shadow: 0 30px 80px rgba(15, 23, 42, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Montserrat, system-ui, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 10% 10%, rgba(15, 118, 110, 0.22), transparent 20rem),
                radial-gradient(circle at 90% 20%, rgba(14, 165, 233, 0.12), transparent 18rem),
                linear-gradient(180deg, #f8fcfb 0%, var(--bg) 100%);
        }

        .page {
            width: min(1120px, calc(100% - 32px));
            margin: 0 auto;
            padding: 28px 0 40px;
            display: grid;
            gap: 24px;
        }

        .site-header {
            position: sticky;
            top: 16px;
            z-index: 30;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 16px 22px;
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.65);
            border-radius: 24px;
            box-shadow: 0 18px 44px rgba(15, 23, 42, 0.1);
        }

        .brand {
            color: var(--text);
            text-decoration: none;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .nav-links a {
            color: var(--muted);
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 999px;
            border: 1px solid rgba(17, 36, 61, 0.08);
            background: rgba(255, 255, 255, 0.75);
            font-size: 0.92rem;
            font-weight: 700;
            transition: 180ms ease;
        }

        .nav-links a:hover {
            color: var(--primary-dark);
            border-color: rgba(15, 118, 110, 0.22);
            transform: translateY(-1px);
        }

        .actions a,
        button {
            text-decoration: none;
            border-radius: 999px;
            border: 1px solid var(--line);
            padding: 12px 18px;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            transition: 180ms ease;
        }

        .actions a:hover {
            transform: translateY(-1px);
            border-color: rgba(15, 118, 110, 0.28);
        }

        .hero {
            display: block;
        }

        .hero-card,
        .info-card {
            background: var(--panel);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.55);
            border-radius: 32px;
            box-shadow: var(--shadow);
        }

        .hero-card {
            padding: 34px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: var(--primary-soft);
            color: var(--primary);
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        h1 {
            margin: 18px 0 14px;
            font-size: clamp(2.2rem, 5vw, 4.3rem);
            line-height: 1.02;
        }

        .lead {
            margin: 0 0 28px;
            color: var(--muted);
            font-size: 1.02rem;
            line-height: 1.7;
            max-width: 56ch;
        }

        .tracking-form {
            display: grid;
            gap: 14px;
        }

        .input-shell {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
            padding: 12px;
            border: 1px solid var(--line);
            border-radius: 24px;
            background: #fff;
        }

        input {
            border: 0;
            outline: none;
            font: inherit;
            font-size: 1rem;
            padding: 14px 16px;
            background: transparent;
            color: var(--text);
        }

        button {
            color: #fff;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-color: transparent;
            min-width: 156px;
        }

        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(15, 118, 110, 0.2);
        }

        .hint,
        .error {
            font-size: 0.92rem;
        }

        .hint {
            color: var(--muted);
        }

        .error {
            color: #b91c1c;
            font-weight: 600;
        }

        .info-card {
            padding: 28px;
            display: grid;
            gap: 16px;
        }

        .steps-list {
            display: grid;
            gap: 16px;
        }

        .feature {
            padding: 18px 18px 20px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(17, 36, 61, 0.08);
            display: grid;
            gap: 8px;
        }

        .feature small {
            display: block;
            margin-bottom: 8px;
            color: var(--primary);
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .feature strong {
            display: block;
            margin-bottom: 8px;
            font-size: 1.1rem;
        }

        .feature p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .section-card {
            background: var(--panel);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.55);
            border-radius: 32px;
            box-shadow: var(--shadow);
            padding: 32px;
        }

        .section-head {
            display: grid;
            gap: 10px;
            margin-bottom: 24px;
        }

        .section-head h2 {
            margin: 0;
            font-size: clamp(1.7rem, 3vw, 2.4rem);
        }

        .section-head p {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
            max-width: 64ch;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .service-card {
            padding: 24px;
            border-radius: 24px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(236, 253, 245, 0.88) 100%);
            border: 1px solid rgba(15, 118, 110, 0.12);
        }

        .service-card strong {
            display: block;
            margin-bottom: 10px;
            font-size: 1.2rem;
        }

        .service-time {
            display: inline-flex;
            align-items: center;
            margin-bottom: 14px;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(15, 118, 110, 0.1);
            color: var(--primary-dark);
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.03em;
        }

        .service-card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .site-footer {
            background: linear-gradient(135deg, rgba(17, 36, 61, 0.98) 0%, rgba(15, 118, 110, 0.95) 100%);
            color: rgba(255, 255, 255, 0.92);
            border-radius: 32px;
            padding: 32px;
            box-shadow: var(--shadow);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr 0.9fr;
            gap: 24px;
        }

        .footer-brand {
            display: grid;
            gap: 14px;
        }

        .footer-brand strong,
        .footer-column strong {
            font-size: 1.05rem;
        }

        .footer-brand p,
        .footer-note,
        .footer-list li {
            margin: 0;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.7;
        }

        .footer-column {
            display: grid;
            gap: 14px;
        }

        .footer-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 12px;
        }

        .footer-list a {
            color: inherit;
            text-decoration: none;
        }

        .footer-note {
            padding-top: 8px;
            border-top: 1px solid rgba(255, 255, 255, 0.14);
        }

        .scan-divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 6px 0 2px;
            color: var(--muted);
            font-size: 0.88rem;
            font-weight: 600;
        }

        .scan-divider::before,
        .scan-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--line);
        }

        .btn-scan {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 16px 20px;
            border-radius: 24px;
            border: 2px dashed rgba(15, 118, 110, 0.35);
            background: rgba(204, 251, 241, 0.35);
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: 200ms ease;
        }

        .btn-scan:hover {
            background: rgba(204, 251, 241, 0.7);
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(15, 118, 110, 0.15);
        }

        .btn-scan svg {
            width: 22px;
            height: 22px;
            flex-shrink: 0;
        }

        /* QR Scanner Modal Overlay */
        .qr-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 100;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(6px);
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: fadeIn 200ms ease;
        }

        .qr-modal-overlay.active {
            display: flex;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .qr-modal {
            background: var(--panel);
            border-radius: 28px;
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.3);
            width: min(440px, 100%);
            overflow: hidden;
            animation: slideUp 300ms ease;
        }

        .qr-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            border-bottom: 1px solid var(--line);
        }

        .qr-modal-header h3 {
            margin: 0;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .qr-modal-close {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid var(--line);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 150ms ease;
            color: var(--muted);
            font-size: 1.1rem;
            padding: 0;
            min-width: unset;
        }

        .qr-modal-close:hover {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #b91c1c;
            transform: none;
            box-shadow: none;
        }

        .qr-modal-body {
            padding: 20px 24px 24px;
        }

        #qr-reader {
            width: 100%;
            border-radius: 16px;
            overflow: hidden;
            border: 2px solid var(--line);
        }

        #qr-reader video {
            border-radius: 14px;
        }

        .qr-status {
            margin-top: 14px;
            text-align: center;
            font-size: 0.9rem;
            color: var(--muted);
            font-weight: 600;
        }

        .qr-status.success {
            color: var(--primary);
        }

        .qr-status.error {
            color: #b91c1c;
        }

        /* Hide html5-qrcode built-in UI elements we don't need */
        #qr-reader__dashboard_section_swaplink,
        #qr-reader__header_message {
            display: none !important;
        }

        @media (max-width: 900px) {
            .services-grid,
            .footer-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            html {
                scroll-padding-top: 130px;
            }

            .page {
                width: min(100% - 20px, 1120px);
                padding-top: 20px;
            }

            .site-header {
                top: 10px;
                padding: 14px 16px;
                border-radius: 20px;
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-links {
                width: 100%;
            }

            .hero-card,
            .info-card,
            .section-card,
            .site-footer {
                border-radius: 24px;
            }

            .hero-card,
            .section-card,
            .site-footer {
                padding: 24px;
            }

            .input-shell {
                grid-template-columns: 1fr;
            }

            button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <header class="site-header">
            <a class="brand" href="#beranda">Laundry Tracking</a>
            <nav class="nav-links" aria-label="Navigasi halaman">
                <a href="#beranda">Beranda</a>
                <a href="#cara-kerja">Cara Kerja</a>
                <a href="#layanan-kami">Layanan</a>
            </nav>
        </header>

        <section class="hero" id="beranda">
            <div class="hero-card">
                <span class="eyebrow">Cek Status Cucian</span>
                <h1>Masukkan kode tracking dan lihat progres laundry Anda.</h1>
                <p class="lead">
                    Pelanggan cukup masukkan kode yang diberikan admin laundry,
                    lalu status order akan muncul.
                </p>

                <form class="tracking-form" action="{{ route('tracking.search') }}" method="GET">
                    <div class="input-shell">
                        <input
                            type="text"
                            name="code"
                            placeholder="Contoh: LDR-AB12CD34"
                            value="{{ old('code', request('code')) }}"
                            autocomplete="off"
                        >
                        <button type="submit">Lacak Sekarang</button>
                    </div>

                    @error('code')
                        <div class="error">{{ $message }}</div>
                    @enderror

                    @if (session('tracking_error'))
                        <div class="error">{{ session('tracking_error') }}</div>
                    @endif

                    <div class="hint">
                        Kode tracking diberikan oleh laundry saat order dibuat.
                    </div>
                </form>

                <div class="scan-divider">atau</div>

                <button type="button" class="btn-scan" id="btn-scan-qr">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 7V5a2 2 0 0 1 2-2h2"/>
                        <path d="M17 3h2a2 2 0 0 1 2 2v2"/>
                        <path d="M21 17v2a2 2 0 0 1-2 2h-2"/>
                        <path d="M7 21H5a2 2 0 0 1-2-2v-2"/>
                        <rect x="7" y="7" width="10" height="10" rx="1"/>
                    </svg>
                    Scan QR Code
                </button>
            </div>
        </section>

        <section class="section-card" id="cara-kerja" aria-labelledby="cara-kerja-title">
            <div class="section-head">
                <span class="eyebrow">Cara Kerja</span>
                <h2 id="cara-kerja-title">Langkah mudah untuk melacak laundry Anda dari awal sampai selesai.</h2>
                <p>Semua proses dibuat sederhana supaya pelanggan bisa langsung tahu posisi cucian tanpa perlu bertanya manual ke admin.</p>
            </div>

            <div class="steps-list">
                <div class="feature">
                    <small>Langkah 1</small>
                    <strong>Dapatkan kode dari laundry</strong>
                    <p>Admin laundry membuat order dan sistem otomatis menghasilkan kode tracking unik untuk pelanggan.</p>
                </div>
                <div class="feature">
                    <small>Langkah 2</small>
                    <strong>Masukkan kode di halaman ini</strong>
                    <p>Tidak perlu mencari URL order. Cukup buka domain utama lalu isi kode tracking.</p>
                </div>
                <div class="feature">
                    <small>Langkah 3</small>
                    <strong>Lihat status real-time</strong>
                    <p>Status seperti diterima, diproses, selesai, dan diambil akan terlihat langsung pada halaman tracking.</p>
                </div>
            </div>
        </section>

        <section class="section-card" aria-labelledby="layanan-kami">
            <div class="section-head">
                <span class="eyebrow">Pilihan Layanan</span>
                <h2 id="layanan-kami">Layanan laundry yang bisa disesuaikan dengan kebutuhan Anda.</h2>
                <p>Pilih kecepatan pengerjaan yang paling cocok, mulai dari hemat untuk harian hingga prioritas tinggi untuk kebutuhan mendadak.</p>
            </div>

            <div class="services-grid">
                <article class="service-card">
                    <strong>Reguler</strong>
                    <div class="service-time">Estimasi 2&ndash;3 hari</div>
                    <p>Cocok untuk pakaian harian dengan harga yang lebih hemat dan tetap bersih maksimal.</p>
                </article>

                <article class="service-card">
                    <strong>Express</strong>
                    <div class="service-time">Estimasi 1 hari (24 jam)</div>
                    <p>Pilihan tepat untuk Anda yang sibuk dan butuh pakaian segar untuk aktivitas esok hari.</p>
                </article>

                <article class="service-card">
                    <strong>Kilat</strong>
                    <div class="service-time">Selesai dalam beberapa jam</div>
                    <p>Layanan ultra-cepat! Cuci &amp; setrika prioritas utama untuk keperluan mendadak Anda.</p>
                </article>
            </div>
        </section>

        <footer class="site-footer">
            <div class="footer-grid">
                <div class="footer-brand">
                    <strong>Laundry Tracking</strong>
                    <p>Solusi cerdas untuk pakaian bersih, wangi, dan rapi seketika. Kami berkomitmen memberikan layanan laundry terbaik dengan pelacakan modern.</p>
                </div>

                <div class="footer-column">
                    <strong>Hubungi Kami</strong>
                    <ul class="footer-list">
                        <li>&#128205; Jl. Kampus Mahasiswa No. 12, Kota Pendidikan</li>
                        <li>&#128222; WhatsApp: <a href="https://wa.me/6281234567890">0812-3456-7890</a></li>
                        <li>&#9993;&#65039; Email: <a href="mailto:halo@laundry.com">halo@laundry.com</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <strong>Jam Operasional</strong>
                    <ul class="footer-list">
                        <li>&#128338; Senin - Jumat: 07.00 - 20.00 WIB</li>
                        <li>&#128338; Sabtu - Minggu: 08.00 - 18.00 WIB</li>
                    </ul>
                    <p class="footer-note">Tanggal merah tetap buka (Setengah hari).</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- QR Scanner Modal -->
    <div class="qr-modal-overlay" id="qr-modal-overlay">
        <div class="qr-modal">
            <div class="qr-modal-header">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 7V5a2 2 0 0 1 2-2h2"/>
                        <path d="M17 3h2a2 2 0 0 1 2 2v2"/>
                        <path d="M21 17v2a2 2 0 0 1-2 2h-2"/>
                        <path d="M7 21H5a2 2 0 0 1-2-2v-2"/>
                    </svg>
                    Scan QR Code
                </h3>
                <button class="qr-modal-close" id="btn-close-scan" title="Tutup">&times;</button>
            </div>
            <div class="qr-modal-body">
                <div id="qr-reader"></div>
                <div class="qr-status" id="qr-status">Arahkan kamera ke QR Code dari tenant...</div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const btnScan    = document.getElementById('btn-scan-qr');
        const btnClose   = document.getElementById('btn-close-scan');
        const overlay    = document.getElementById('qr-modal-overlay');
        const statusEl   = document.getElementById('qr-status');
        let html5QrCode  = null;
        let scanning     = false;

        function resetStatus() {
            statusEl.textContent = 'Arahkan kamera ke QR Code dari tenant...';
            statusEl.className  = 'qr-status';
        }

        function stopScanner() {
            if (html5QrCode && scanning) {
                html5QrCode.stop().then(function () {
                    scanning = false;
                    html5QrCode.clear();
                }).catch(function () {
                    scanning = false;
                });
            }
        }

        function openScanner() {
            overlay.classList.add('active');
            resetStatus();

            html5QrCode = new Html5Qrcode('qr-reader');

            html5QrCode.start(
                { facingMode: 'environment' },
                {
                    fps: 10,
                    qrbox: { width: 220, height: 220 },
                    aspectRatio: 1,
                },
                function onSuccess(decodedText) {
                    // QR berhasil terbaca
                    scanning = false;
                    html5QrCode.stop().then(function () {
                        html5QrCode.clear();

                        if (decodedText.startsWith('http://') || decodedText.startsWith('https://')) {
                            // Ambil path dari URL di QR (misal: /tracking/code/LDR-XXXXX)
                            // lalu gabungkan dengan domain yang sedang aktif sekarang,
                            // agar tetap bekerja di domain manapun (lokal / cloudflare / production)
                            try {
                                var scannedUrl  = new URL(decodedText);
                                var targetPath  = scannedUrl.pathname + scannedUrl.search + scannedUrl.hash;
                                var redirectUrl = window.location.origin + targetPath;

                                statusEl.textContent = '✓ QR Code terbaca! Mengalihkan...';
                                statusEl.className   = 'qr-status success';

                                setTimeout(function () {
                                    window.location.href = redirectUrl;
                                }, 400);
                            } catch (e) {
                                statusEl.textContent = '✗ Format URL dalam QR tidak valid.';
                                statusEl.className   = 'qr-status error';
                            }
                        } else {
                            statusEl.textContent = '✗ QR Code tidak dikenali. Pastikan QR berasal dari sistem kami.';
                            statusEl.className   = 'qr-status error';
                        }
                    });
                },
                function onError() {
                    // frame tanpa QR — diabaikan
                }
            ).then(function () {
                scanning = true;
            }).catch(function (err) {
                statusEl.textContent = 'Gagal mengakses kamera. Pastikan izin kamera telah diberikan.';
                statusEl.className   = 'qr-status error';
            });
        }

        function closeScanner() {
            stopScanner();
            overlay.classList.remove('active');
        }

        btnScan.addEventListener('click', openScanner);
        btnClose.addEventListener('click', closeScanner);

        // Tutup modal jika klik di luar area modal
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) {
                closeScanner();
            }
        });

        // Tutup modal dengan tombol Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && overlay.classList.contains('active')) {
                closeScanner();
            }
        });
    });
    </script>
</body>
</html>
