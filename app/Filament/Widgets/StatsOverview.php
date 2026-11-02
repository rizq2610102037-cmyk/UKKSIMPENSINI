<?php

namespace App\Filament\Widgets;

use App\Models\Kamar;
use App\Models\Pemesanan;
use App\Models\TipeKamar;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Total Kamar
        $totalKamar = Kamar::count();

        // Kamar Terisi (hari ini)
        $kamarTerisi = Kamar::whereHas('detailPemesanan', function ($q) {
            $q->whereHas('pemesanan', function ($query) {
                $query->whereIn('status_pemesanan', ['confirm', 'checkin'])
                    ->where('payment_status', 'paid')
                    ->where('tgl_check_in', '<=', now())
                    ->where('tgl_check_out', '>=', now());
            });
        })->count();

        // Kamar Tersedia
        $kamarTersedia = $totalKamar - $kamarTerisi;

        // Occupancy Rate
        $occupancyRate = $totalKamar > 0
            ? round(($kamarTerisi / $totalKamar) * 100, 1)
            : 0;

        // Total Pemesanan Bulan Ini
        $pemesananBulanIni = Pemesanan::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Pemesanan Bulan Lalu
        $pemesananBulanLalu = Pemesanan::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        // Persentase perubahan pemesanan
        $perubahanPemesanan = $pemesananBulanLalu > 0
            ? round((($pemesananBulanIni - $pemesananBulanLalu) / $pemesananBulanLalu) * 100, 1)
            : 0;

        // Revenue Bulan Ini
        $revenueBulanIni = Pemesanan::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('payment_status', 'paid')
            ->sum('total_harga');

        // Revenue Bulan Lalu
        $revenueBulanLalu = Pemesanan::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->where('payment_status', 'paid')
            ->sum('total_harga');

        // Persentase perubahan revenue
        $perubahanRevenue = $revenueBulanLalu > 0
            ? round((($revenueBulanIni - $revenueBulanLalu) / $revenueBulanLalu) * 100, 1)
            : 0;

        // Total User/Tamu
        $totalUsers = User::where('role', 'user')->count();

        // Check-in Hari Ini
        $checkinHariIni = Pemesanan::where('tgl_check_in', now()->toDateString())
            ->whereIn('status_pemesanan', ['confirm', 'checkin'])
            ->where('payment_status', 'paid')
            ->count();

        return [
            // Stat 1: Occupancy Rate
            Stat::make('Occupancy Rate', $occupancyRate . '%')
                ->description("{$kamarTerisi} dari {$totalKamar} kamar terisi")
                ->descriptionIcon('heroicon-o-home')
                ->chart($this->getOccupancyChart())
                ->color($occupancyRate >= 70 ? 'success' : ($occupancyRate >= 40 ? 'warning' : 'danger')),

            // Stat 2: Kamar Tersedia
            Stat::make('Kamar Tersedia', $kamarTersedia)
                ->description("Total {$totalKamar} kamar")
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart($this->getRoomAvailabilityChart()),

            // Stat 3: Pemesanan Bulan Ini
            Stat::make('Pemesanan Bulan Ini', $pemesananBulanIni)
                ->description($perubahanPemesanan >= 0
                    ? "+{$perubahanPemesanan}% dari bulan lalu"
                    : "{$perubahanPemesanan}% dari bulan lalu")
                ->descriptionIcon($perubahanPemesanan >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($perubahanPemesanan >= 0 ? 'success' : 'danger')
                ->chart($this->getBookingChart()),

            // Stat 4: Revenue Bulan Ini
            Stat::make('Revenue Bulan Ini', 'Rp ' . number_format($revenueBulanIni, 0, ',', '.'))
                ->description($perubahanRevenue >= 0
                    ? "+{$perubahanRevenue}% dari bulan lalu"
                    : "{$perubahanRevenue}% dari bulan lalu")
                ->descriptionIcon($perubahanRevenue >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($perubahanRevenue >= 0 ? 'success' : 'danger')
                ->chart($this->getRevenueChart()),

            // Stat 5: Check-in Hari Ini
            Stat::make('Check-in Hari Ini', $checkinHariIni)
                ->description('Tamu yang check-in')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('info'),

            // Stat 6: Total Tamu Terdaftar
            Stat::make('Total Tamu', $totalUsers)
                ->description('User terdaftar')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary'),
        ];
    }

    /**
     * Chart untuk Occupancy Rate (7 hari terakhir)
     */
    protected function getOccupancyChart(): array
    {
        $data = [];
        $totalKamar = Kamar::count();

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();

            $kamarTerisi = Kamar::whereHas('detailPemesanan', function ($q) use ($date) {
                $q->whereHas('pemesanan', function ($query) use ($date) {
                    $query->whereIn('status_pemesanan', ['confirm', 'checkin'])
                        ->where('payment_status', 'paid')
                        ->where('tgl_check_in', '<=', $date)
                        ->where('tgl_check_out', '>=', $date);
                });
            })->count();

            $occupancy = $totalKamar > 0 ? round(($kamarTerisi / $totalKamar) * 100) : 0;
            $data[] = $occupancy;
        }

        return $data;
    }

    /**
     * Chart untuk Room Availability (7 hari terakhir)
     */
    protected function getRoomAvailabilityChart(): array
    {
        $data = [];
        $totalKamar = Kamar::count();

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();

            $kamarTerisi = Kamar::whereHas('detailPemesanan', function ($q) use ($date) {
                $q->whereHas('pemesanan', function ($query) use ($date) {
                    $query->whereIn('status_pemesanan', ['confirm', 'checkin'])
                        ->where('payment_status', 'paid')
                        ->where('tgl_check_in', '<=', $date)
                        ->where('tgl_check_out', '>=', $date);
                });
            })->count();

            $data[] = $totalKamar - $kamarTerisi;
        }

        return $data;
    }

    /**
     * Chart untuk Booking (30 hari terakhir)
     */
    protected function getBookingChart(): array
    {
        $data = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();

            $bookings = Pemesanan::whereDate('created_at', $date)->count();
            $data[] = $bookings;
        }

        return $data;
    }

    /**
     * Chart untuk Revenue (30 hari terakhir)
     */
    protected function getRevenueChart(): array
    {
        $data = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();

            $revenue = Pemesanan::whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->sum('total_harga');

            $data[] = round($revenue / 1000000, 2); // Dalam jutaan
        }

        return $data;
    }
}
