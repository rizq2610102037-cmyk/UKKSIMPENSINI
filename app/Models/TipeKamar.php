<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipeKamar extends Model
{
    protected $table = 'tipe_kamar';
    protected $primaryKey = 'id_tipe_kamar';

    protected $fillable = [
        'harga',
        'nama_tipe_kamar',
        'gambar',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    // ekspor id_tipe_kamar ke table lain - guna relasi antar table
    public function kamar(): HasMany
    {
        return $this->hasMany(Kamar::class, 'id_tipe_kamar', 'id_tipe_kamar');
    }

    public function pemesanan(): HasMany
    {
        return $this->hasMany(Pemesanan::class, 'id_tipe_kamar', 'id_tipe_kamar');
    }

    public function detailPemesanan(): HasMany
    {
        return $this->hasMany(DetailPemesanan::class, 'id_tipe_kamar', 'id_tipe_kamar');
    }

    // public function detailReservasi(): HasMany
    // {
    //     return $this->hasMany(DetailReservasi::class, 'id_tipe_kamar', 'id_tipe_kamar');
    // }
}
