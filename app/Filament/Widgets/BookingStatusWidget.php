<?php

namespace App\Filament\Widgets;

use App\Models\Pemesanan;
use Filament\Widgets\ChartWidget;

class BookingStatusWidget extends ChartWidget
{
    protected static ?string $heading = 'Status Pemesanan';

    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $pending = Pemesanan::where('status_pemesanan', 'pending')->count();
        $confirm = Pemesanan::where('status_pemesanan', 'confirm')->count();
        $checkin = Pemesanan::where('status_pemesanan', 'checkin')->count();
        $checkout = Pemesanan::where('status_pemesanan', 'checkout')->count();
        $cancel = Pemesanan::where('status_pemesanan', 'cancel')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Pemesanan',
                    'data' => [$pending, $confirm, $checkin, $checkout, $cancel],
                    'backgroundColor' => [
                        'rgba(251, 191, 36, 0.8)',  // pending - yellow
                        'rgba(34, 197, 94, 0.8)',   // confirm - green
                        'rgba(59, 130, 246, 0.8)',  // checkin - blue
                        'rgba(156, 163, 175, 0.8)', // checkout - gray
                        'rgba(239, 68, 68, 0.8)',   // cancel - red
                    ],
                    'borderColor' => [
                        'rgb(251, 191, 36)',
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(156, 163, 175)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Pending', 'Confirm', 'Check In', 'Check Out', 'Cancel'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
