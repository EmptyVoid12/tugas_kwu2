<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Order Laundry</title>
    <style>
        :root {
            color-scheme: light;
            --text: #111111;
            --muted: #555555;
            --line: #cfcfcf;
            --panel: #ffffff;
            --bg: #f4f4f4;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 16px;
            background: var(--bg);
            color: var(--text);
            font-family: "Courier New", Courier, monospace;
        }

        .toolbar {
            width: min(100%, 360px);
            margin: 0 auto 12px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .toolbar a,
        .toolbar button {
            appearance: none;
            border: 1px solid #111111;
            background: #ffffff;
            color: #111111;
            border-radius: 999px;
            padding: 10px 14px;
            font: inherit;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }

        .receipt {
            width: min(100%, 360px);
            margin: 0 auto;
            background: var(--panel);
            border: 1px solid var(--line);
            padding: 18px 16px 20px;
        }

        .center {
            text-align: center;
        }

        .title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .subtitle {
            margin: 6px 0 0;
            font-size: 0.85rem;
            color: var(--muted);
            line-height: 1.5;
        }

        .divider {
            margin: 14px 0;
            border-top: 1px dashed #888888;
        }

        .section {
            display: grid;
            gap: 8px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
            font-size: 0.84rem;
            line-height: 1.5;
        }

        .row .label {
            color: var(--muted);
            white-space: nowrap;
        }

        .row .value {
            text-align: right;
            font-weight: 700;
            word-break: break-word;
        }

        .tracking-box {
            padding: 10px 12px;
            border: 1px dashed #111111;
            text-align: center;
        }

        .tracking-box strong {
            display: block;
            font-size: 1rem;
            letter-spacing: 0.08em;
            margin-bottom: 4px;
        }

        .tracking-box span {
            font-size: 0.82rem;
            color: var(--muted);
        }

        .qr-wrap {
            text-align: center;
        }

        .qr-wrap img {
            width: 128px;
            height: 128px;
            object-fit: contain;
            display: inline-block;
        }

        .note {
            font-size: 0.78rem;
            line-height: 1.6;
            color: var(--muted);
            text-align: center;
        }

        .note strong {
            color: #111111;
        }

        @page {
            size: 80mm auto;
            margin: 6mm;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .toolbar {
                display: none;
            }

            .receipt {
                width: 100%;
                border: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a href="{{ url()->previous() }}">Kembali</a>
        <button type="button" onclick="window.print()">Print</button>
    </div>

    <main class="receipt">
        <div class="center">
            <h1 class="title">{{ $laundry->tenant->nama_laundry }}</h1>
            <p class="subtitle">Bukti order dan tracking pelanggan</p>
        </div>

        <div class="divider"></div>

        <section class="section">
            <div class="row">
                <span class="label">Pelanggan</span>
                <span class="value">{{ $laundry->nama_pelanggan }}</span>
            </div>
            <div class="row">
                <span class="label">No HP</span>
                <span class="value">{{ $laundry->no_hp }}</span>
            </div>
            <div class="row">
                <span class="label">Layanan</span>
                <span class="value">{{ \App\Models\Laundry::LAYANAN_OPTIONS[$laundry->layanan] ?? $laundry->layanan }}</span>
            </div>
            <div class="row">
                <span class="label">Masuk</span>
                <span class="value">{{ $laundry->tanggal_masuk?->translatedFormat('d/m/Y') }}</span>
            </div>
            <div class="row">
                <span class="label">Estimasi</span>
                <span class="value">{{ $laundry->estimasi_selesai?->translatedFormat('d/m/Y') ?? '-' }}</span>
            </div>
            <div class="row">
                <span class="label">Status</span>
                <span class="value">{{ \App\Models\Laundry::STATUS_OPTIONS[$laundry->status] ?? ucfirst($laundry->status) }}</span>
            </div>
        </section>

        <div class="divider"></div>

        <div class="tracking-box">
            <strong>{{ $laundry->kode_tracking }}</strong>
            <span>Kode tracking pelanggan</span>
        </div>

        <div class="divider"></div>

        <div class="qr-wrap">
            @if ($laundry->qr_code_url)
                <img src="{{ $laundry->qr_code_url }}" alt="QR Code Tracking">
            @endif
        </div>

        <div class="divider"></div>

        <p class="note">
            Untuk cek status, buka <strong>tugas_kwu2.test</strong><br>
            lalu masukkan kode <strong>{{ $laundry->kode_tracking }}</strong>
        </p>
    </main>
</body>
</html>
