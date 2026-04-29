<?php

namespace App\Services;

use App\Models\Laundry;
use App\Models\Tenant;
use Carbon\CarbonImmutable;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MonthlyLaundryReportExporter
{
    public function download(Tenant $tenant, int $month, int $year): StreamedResponse
    {
        $period = CarbonImmutable::create($year, $month, 1);

        $orders = Laundry::query()
            ->forTenant($tenant->id)
            ->whereYear('tanggal_masuk', $year)
            ->whereMonth('tanggal_masuk', $month)
            ->orderBy('tanggal_masuk')
            ->orderBy('created_at')
            ->get();

        $filename = sprintf(
            'laporan-laundry-%s-%s.csv',
            str($tenant->nama_laundry)->slug()->toString(),
            $period->format('Y-m'),
        );

        return response()->streamDownload(function () use ($tenant, $period, $orders): void {
            $handle = fopen('php://output', 'wb');

            if ($handle === false) {
                return;
            }

            fwrite($handle, "\xEF\xBB\xBF");
            fwrite($handle, "sep=;\r\n");

            fputcsv($handle, ['Nama Tenant', $this->sanitize($tenant->nama_laundry)], ';');
            fputcsv($handle, ['Periode', $period->format('m/Y')], ';');
            fputcsv($handle, ['Total Order', (string) $orders->count()], ';');
            fputcsv($handle, [], ';');
            fputcsv($handle, [
                'No',
                'Nama Pelanggan',
                'No HP',
                'Alamat',
                'Layanan',
                'Tanggal Masuk',
                'Estimasi Selesai',
                'Status',
                'Kode Tracking',
                'Dibuat Pada',
                'Diupdate Pada',
            ], ';');

            foreach ($orders as $index => $order) {
                fputcsv($handle, [
                    $index + 1,
                    $this->sanitize($order->nama_pelanggan),
                    $this->sanitize($order->no_hp),
                    $this->sanitize($order->alamat),
                    $this->sanitize(Laundry::LAYANAN_OPTIONS[$order->layanan] ?? $order->layanan),
                    optional($order->tanggal_masuk)->format('Y-m-d'),
                    optional($order->estimasi_selesai)->format('Y-m-d'),
                    $this->sanitize(Laundry::STATUS_OPTIONS[$order->status] ?? $order->status),
                    $this->sanitize($order->kode_tracking),
                    optional($order->created_at)->format('Y-m-d H:i:s'),
                    optional($order->updated_at)->format('Y-m-d H:i:s'),
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function sanitize(null|string $value): string
    {
        if ($value === null) {
            return '';
        }

        $normalized = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $value);

        return preg_replace('/\s+/', ' ', trim($normalized)) ?? '';
    }
}
