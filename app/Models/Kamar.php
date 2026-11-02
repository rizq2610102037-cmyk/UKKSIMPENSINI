<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kamar extends Model
{
    protected $table = 'kamar';
    protected $primaryKey = 'id_kamar';

    protected $fillable = [
        'nomor_kamar',
        'id_tipe_kamar',
        'nama_kamar',
    ];

    public function tipeKamar(): BelongsTo
    {
        return $this->belongsTo(TipeKamar::class, 'id_tipe_kamar', 'id_tipe_kamar');
    }

    public function detailPemesanan(): HasMany
    {
        return $this->hasMany(DetailPemesanan::class, 'id_kamar', 'id_kamar');
    }

   /**
     * ✅ FIXED: Cek apakah kamar tersedia pada rentang tanggal tertentu
     * 
     * Logika: Kamar TIDAK tersedia jika ada pemesanan yang overlap
     * Overlap terjadi jika: tgl_check_in < $checkOut DAN tgl_check_out > $checkIn
     */
    public function isAvailable($checkIn, $checkOut)
    {
        return !$this->detailPemesanan()
            ->whereHas('pemesanan', function ($query) use ($checkIn, $checkOut) {
                $query->where('tgl_check_in', '<', $checkOut)
                      ->where('tgl_check_out', '>', $checkIn)
                      ->whereNotIn('status_pemesanan', ['cancel', 'checkout']);
            })
            ->exists();
    }

    /**
     * ✅ FIXED: Scope untuk mendapatkan kamar yang tersedia
     * 
     * Logika sama dengan isAvailable() tapi untuk query builder
     */
    public function scopeAvailable($query, $checkIn, $checkOut)
    {
        return $query->whereDoesntHave('detailPemesanan', function ($q) use ($checkIn, $checkOut) {
            $q->whereHas('pemesanan', function ($query) use ($checkIn, $checkOut) {
                $query->where('tgl_check_in', '<', $checkOut)
                      ->where('tgl_check_out', '>', $checkIn)
                      ->whereNotIn('status_pemesanan', ['cancel', 'checkout']);
            });
        });
    }
}
