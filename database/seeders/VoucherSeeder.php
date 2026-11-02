<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $welcomeVouchers = [
            [
                'kode_voucher' => 'WELCOME50K',
                'nama_voucher' => 'Voucher Selamat Datang 50K',
                'deskripsi' => 'Diskon Rp 50.000 untuk member baru dengan minimal transaksi Rp 500.000',
                'jumlah_diskon' => 50000,
                'minimal_transaksi' => 500000,
                'tipe_voucher' => 'welcome',
                'tanggal_mulai' => now()->startOfYear(),
                'tanggal_berakhir' => now()->addYear(),
                'is_active' => true,
                'maksimal_penggunaan' => null, // unlimited
            ],
            [
                'kode_voucher' => 'WELCOME100K',
                'nama_voucher' => 'Voucher Selamat Datang 100K',
                'deskripsi' => 'Diskon Rp 100.000 untuk member baru dengan minimal transaksi Rp 600.000',
                'jumlah_diskon' => 100000,
                'minimal_transaksi' => 600000,
                'tipe_voucher' => 'welcome',
                'tanggal_mulai' => now()->startOfYear(),
                'tanggal_berakhir' => now()->addYear(),
                'is_active' => true,
                'maksimal_penggunaan' => null,
            ],
            [
                'kode_voucher' => 'WELCOME150K',
                'nama_voucher' => 'Voucher Selamat Datang 150K',
                'deskripsi' => 'Diskon Rp 150.000 untuk member baru dengan minimal transaksi Rp 800.000',
                'jumlah_diskon' => 150000,
                'minimal_transaksi' => 800000,
                'tipe_voucher' => 'welcome',
                'tanggal_mulai' => now()->startOfYear(),
                'tanggal_berakhir' => now()->addYear(),
                'is_active' => true,
                'maksimal_penggunaan' => null,
            ],
        ];

        foreach ($welcomeVouchers as $voucherData) {
            Voucher::updateOrCreate(
                ['kode_voucher' => $voucherData['kode_voucher']],
                $voucherData
            );
        }

        $this->command->info('✓ Welcome vouchers created successfully!');
    }
}
