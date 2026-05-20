<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Laundry</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f4f8fb;
            --panel: #ffffff;
            --line: #d9e3ee;
            --text: #10223a;
            --muted: #5e7086;
            --primary: #0f766e;
            --primary-soft: #d7faf3;
            --warning-soft: #fef3c7;
            --success-soft: #dcfce7;
            --info-soft: #dbeafe;
            --shadow: 0 28px 80px rgba(15, 23, 42, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Montserrat, system-ui, sans-serif;
            background:
                radial-gradient(circle at 12% 14%, rgba(15, 118, 110, 0.15), transparent 22rem),
                linear-gradient(180deg, #fafdff 0%, var(--bg) 100%);
            color: var(--text);
            padding: 24px;
        }

        .page {
            width: min(1040px, 100%);
            margin: 0 auto;
            display: grid;
            gap: 20px;
        }

        .hero,
        .details-card {
            border-radius: 30px;
            overflow: hidden;
            background: var(--panel);
            box-shadow: var(--shadow);
            border: 1px solid rgba(217, 227, 238, 0.8);
        }

        .hero {
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.18), transparent 12rem),
                linear-gradient(135deg, #0f766e 0%, #115e59 100%);
            color: #fff;
            padding: 32px;
        }

        .hero-top {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .back-link {
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            opacity: 0.92;
        }

        .hero h1 {
            margin: 18px 0 10px;
            font-size: clamp(2rem, 4vw, 3rem);
            line-height: 1;
        }

        .hero p {
            margin: 0;
            opacity: 0.88;
            max-width: 60ch;
            line-height: 1.7;
        }

        .hero-meta {
            margin-top: 24px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(10px);
            font-weight: 700;
        }

        .details-card {
            padding: 28px;
            display: grid;
            gap: 24px;
        }

        .section-title {
            margin: 0 0 12px;
            font-size: 1.1rem;
        }

        .section-copy {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .timeline {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .step {
            position: relative;
            padding: 18px;
            border-radius: 22px;
            border: 1px solid var(--line);
            background: #fbfdff;
            min-height: 132px;
        }

        .step.active {
            border-color: rgba(15, 118, 110, 0.22);
            background: linear-gradient(180deg, rgba(215, 250, 243, 0.9), #fff);
        }

        .step.done {
            border-color: rgba(15, 118, 110, 0.14);
            background: rgba(215, 250, 243, 0.54);
        }

        .step-number {
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: #e8eef5;
            color: var(--muted);
            font-weight: 800;
            margin-bottom: 16px;
        }

        .step.active .step-number,
        .step.done .step-number {
            background: var(--primary);
            color: #fff;
        }

        .step strong {
            display: block;
            margin-bottom: 6px;
            font-size: 1rem;
        }

        .step p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
            font-size: 0.9rem;
        }

        .status-banner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            padding: 20px;
            border-radius: 24px;
            background: linear-gradient(135deg, rgba(15, 118, 110, 0.08), rgba(14, 165, 233, 0.08));
            border: 1px solid rgba(15, 118, 110, 0.08);
        }

        .status-banner small {
            display: block;
            margin-bottom: 6px;
            color: var(--muted);
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            font-size: 0.72rem;
        }

        .status-banner strong {
            font-size: clamp(1.2rem, 3vw, 1.8rem);
        }

        .status-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            border-radius: 999px;
            font-weight: 800;
        }

        .status-diterima {
            background: #eef2f7;
            color: #475569;
        }

        .status-diproses {
            background: var(--warning-soft);
            color: #a16207;
        }

        .status-selesai {
            background: var(--success-soft);
            color: #15803d;
        }

        .status-diambil {
            background: var(--info-soft);
            color: #1d4ed8;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
        }

        .item {
            padding: 18px;
            border-radius: 22px;
            border: 1px solid var(--line);
            background: #fcfdff;
        }

        .item small {
            display: block;
            margin-bottom: 8px;
            color: var(--muted);
            font-weight: 800;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            font-size: 0.72rem;
        }

        .item strong {
            display: block;
            font-size: 1rem;
            line-height: 1.6;
        }

        .footer-note {
            color: var(--muted);
            line-height: 1.7;
            font-size: 0.94rem;
        }

        @media (max-width: 860px) {
            .timeline {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            body {
                padding: 16px;
            }

            .hero,
            .details-card {
                border-radius: 24px;
            }

            .hero,
            .details-card {
                padding-left: 22px;
                padding-right: 22px;
            }

            .timeline {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    @php
        $steps = [
            \App\Models\Laundry::STATUS_DITERIMA => [
                'label' => 'Diterima',
                'description' => 'Order sudah masuk dan sedang menunggu diproses oleh laundry.',
            ],
            \App\Models\Laundry::STATUS_DIPROSES => [
                'label' => 'Diproses',
                'description' => 'Cucian Anda sedang dikerjakan oleh tim laundry.',
            ],
            \App\Models\Laundry::STATUS_SELESAI => [
                'label' => 'Selesai',
                'description' => 'Cucian telah selesai dan siap untuk diambil.',
            ],
            \App\Models\Laundry::STATUS_DIAMBIL => [
                'label' => 'Diambil',
                'description' => 'Order telah diambil oleh pelanggan dan proses selesai.',
            ],
        ];

        $statusOrder = array_keys($steps);
        $currentIndex = array_search($laundry->status, $statusOrder, true);
        $statusClass = match ($laundry->status) {
            \App\Models\Laundry::STATUS_DIPROSES => 'status-diproses',
            \App\Models\Laundry::STATUS_SELESAI => 'status-selesai',
            \App\Models\Laundry::STATUS_DIAMBIL => 'status-diambil',
            default => 'status-diterima',
        };
    @endphp

    <main class="page">
        <section class="hero">
            <div class="hero-top">
                <a class="back-link" href="{{ route('home') }}">← Lacak order lain</a>
                <div class="meta-pill">{{ $laundry->tenant->nama_laundry }}</div>
            </div>

            <h1>Order Anda sedang dalam perjalanan yang jelas.</h1>
            <p>
                Pantau status laundry secara real-time tanpa perlu menanyakan ulang ke admin. Semua informasi penting order ditampilkan
                ringkas dan mudah dibaca di satu halaman ini.
            </p>

            <div class="hero-meta">
                <div class="meta-pill">Kode: {{ $laundry->kode_tracking }}</div>
                <div class="meta-pill">Layanan: {{ \App\Models\Laundry::LAYANAN_OPTIONS[$laundry->layanan] ?? $laundry->layanan }}</div>
            </div>
        </section>

        <section class="details-card">
            <div>
                <h2 class="section-title">Progres Order</h2>
                <p class="section-copy">Tahap aktif menunjukkan posisi cucian Anda saat ini. Tahap sebelumnya menandakan proses yang sudah dilewati.</p>
            </div>

            <div class="timeline">
                @foreach ($steps as $status => $step)
                    @php
                        $stepIndex = array_search($status, $statusOrder, true);
                        $stepClass = '';

                        if ($stepIndex < $currentIndex) {
                            $stepClass = 'done';
                        } elseif ($stepIndex === $currentIndex) {
                            $stepClass = 'active';
                        }
                    @endphp

                    <div class="step {{ $stepClass }}">
                        <div class="step-number">{{ $stepIndex + 1 }}</div>
                        <strong>{{ $step['label'] }}</strong>
                        @if ($stepClass === 'done' || $stepClass === 'active')
                            <p>{{ $step['description'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="status-banner">
                <div>
                    <small>Status Saat Ini</small>
                    <strong>{{ $steps[$laundry->status]['label'] ?? ucfirst($laundry->status) }}</strong>
                </div>
                <div class="status-chip {{ $statusClass }}">
                    {{ $steps[$laundry->status]['label'] ?? ucfirst($laundry->status) }}
                </div>
            </div>

            <div class="grid">
                <div class="item">
                    <small>Nama Pelanggan</small>
                    <strong>{{ $laundry->nama_pelanggan }}</strong>
                </div>
                <div class="item">
                    <small>No HP</small>
                    <strong>{{ $laundry->no_hp }}</strong>
                </div>
                <div class="item">
                    <small>Tanggal Masuk</small>
                    <strong>{{ $laundry->tanggal_masuk?->translatedFormat('d F Y') }}</strong>
                </div>
                <div class="item">
                    <small>Estimasi Selesai</small>
                    <strong>{{ $laundry->estimasi_selesai?->translatedFormat('d F Y') ?? '-' }}</strong>
                </div>
                <div class="item">
                    <small>Alamat</small>
                    <strong>{{ $laundry->alamat ?: '-' }}</strong>
                </div>
                <div class="item">
                    <small>Kode Tracking</small>
                    <strong>{{ $laundry->kode_tracking }}</strong>
                </div>
            </div>

            <div class="footer-note">
                Jika status masih belum berubah sesuai ekspektasi, Anda dapat menghubungi laundry terkait dengan menyebutkan kode tracking di atas.
            </div>
        </section>
    </main>
</body>
</html>
