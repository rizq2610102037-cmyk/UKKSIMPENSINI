<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KamarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kamar')->insert([
            [
                'id_tipe_kamar' => 1,
                'nomor_kamar' => 101,
                'nama_kamar' => 'Standard 101',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_tipe_kamar' => 1,
                'nomor_kamar' => 102,
                'nama_kamar' => 'Standard 102',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_tipe_kamar' => 2,
                'nomor_kamar' => 201,
                'nama_kamar' => 'Premium 201',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_tipe_kamar' => 3,
                'nomor_kamar' => 301,
                'nama_kamar' => 'Deluxe 301',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
