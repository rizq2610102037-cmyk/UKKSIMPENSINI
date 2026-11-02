<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TipeKamarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tipe_kamar')->insert([
            [
                'harga' => 300000.00,
                'nama_tipe_kamar' => 'Kamar Standar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'harga' => 350000.00,
                'nama_tipe_kamar' => 'Kamar Premium',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'harga' => 400000.00,
                'nama_tipe_kamar' => 'Kamar Luxury',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
