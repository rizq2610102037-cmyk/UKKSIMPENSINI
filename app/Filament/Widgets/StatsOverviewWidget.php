<?php

namespace App\Filament\Widgets;

use App\Models\Kamar;
use App\Models\Pemesanan;
use App\Models\User;
use App\Models\Voucher;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Total Pemesanan Bulan Ini
        $pemesananBulanIni = Pemesanan::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $pemesananBulanLalu = Pemesanan::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $pemesananTrend = $pemesananBulanLalu > 0
            ? (($pemesananBulanIni - $pemesananBulanLalu) / $pemesananBulanLalu) * 100
            : 0;

        // Total Revenue Bulan Ini
        $revenueBulanIni = Pemesanan::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('payment_status', 'paid')
            ->sum('total_harga');

        $revenueBulanLalu = Pemesanan::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->where('payment_status', 'paid')
            ->sum('total_harga');

        $revenueTrend = $revenueBulanLalu > 0
            ? (($revenueBulanIni - $revenueBulanLalu) / $revenueBulanLalu) * 100
            : 0;

        // Kamar Tersedia vs Terisi
        $totalKamar = Kamar::count();
        $kamarTerisi = Kamar::whereHas('detailPemesanan', function ($query) {
            $query->whereHas('pemesanan', function ($q) {
                $q->whereIn('status_pemesanan', ['confirm', 'checkin'])
                    ->where('payment_status', 'paid')
                    ->where('tgl_check_in', '<=', now())
                    ->where('tgl_check_out', '>=', now());
            });
        })->count();

        $kamarTersedia = $totalKamar - $kamarTerisi;
        $occupancyRate = $totalKamar > 0 ? ($kamarTerisi / $totalKamar) * 100 : 0;

        // Total User Aktif
        $totalUsers = User::where('role', 'user')->count();
        $newUsersThisMonth = User::where('role', 'user')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            Stat::make('Pemesanan Bulan Ini', $pemesananBulanIni)
                ->description(
                    $pemesananTrend >= 0
                        ? sprintf('+%.1f%% dari bulan lalu', abs($pemesananTrend))
                        : sprintf('-%.1f%% dari bulan lalu', abs($pemesananTrend))
                )
                ->descriptionIcon($pemesananTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($pemesananTrend >= 0 ? 'success' : 'danger')
                ->chart([7, 12, 8, 15, 10, 18, 20]),

            Stat::make('Revenue Bulan Ini', 'Rp ' . number_format($revenueBulanIni, 0, ',', '.'))
                ->description(
                    $revenueTrend >= 0
                        ? sprintf('+%.1f%% dari bulan lalu', abs($revenueTrend))
                        : sprintf('-%.1f%% dari bulan lalu', abs($revenueTrend))
                )
                ->descriptionIcon($revenueTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueTrend >= 0 ? 'success' : 'danger')
                ->chart([65, 70, 55, 80, 75, 90, 100]),

            Stat::make('Occupancy Rate', sprintf('%.1f%%', $occupancyRate))
                ->description(sprintf('%d dari %d kamar terisi', $kamarTerisi, $totalKamar))
                ->descriptionIcon('heroicon-m-home')
                ->color($occupancyRate >= 70 ? 'success' : ($occupancyRate >= 50 ? 'warning' : 'danger'))
                ->chart([40, 50, 60, 70, 65, 75, $occupancyRate]),

            Stat::make('Total Users', number_format($totalUsers))
                ->description(sprintf('+%d user baru bulan ini', $newUsersThisMonth))
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info')
                ->chart([10, 15, 20, 25, 30, 35, $totalUsers]),
        ];
    }
}
