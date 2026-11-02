<?php

namespace App\Filament\Widgets;

use App\Models\Kamar;
use App\Models\TipeKamar;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Str;

class RoomAvailabilityWidget extends ChartWidget
{
    protected static ?string $heading = 'Ketersediaan Kamar per Tipe';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $tipeKamars = TipeKamar::with('kamar')->get();

        $labels = [];
        $available = [];
        $occupied = [];

        foreach ($tipeKamars as $tipe) {
            $labels[] = Str::limit($tipe->deskripsi, 20);

            $totalKamar = $tipe->kamar->count();

            $kamarTerisi = Kamar::where('id_tipe_kamar', $tipe->id_tipe_kamar)
                ->whereHas('detailPemesanan', function ($query) {
                    $query->whereHas('pemesanan', function ($q) {
                        $q->whereIn('status_pemesanan', ['confirm', 'checkin'])
                            ->where('payment_status', 'paid')
                            ->where('tgl_check_in', '<=', now())
                            ->where('tgl_check_out', '>=', now());
                    });
                })->count();

            $available[] = $totalKamar - $kamarTerisi;
            $occupied[] = $kamarTerisi;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tersedia',
                    'data' => $available,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                ],
                [
                    'label' => 'Terisi',
                    'data' => $occupied,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'x' => [
                    'stacked' => true,
                ],
                'y' => [
                    'stacked' => true,
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
