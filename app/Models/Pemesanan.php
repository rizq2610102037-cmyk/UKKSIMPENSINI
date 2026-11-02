<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pemesanan extends Model
{
    protected $table = 'pemesanan';
    protected $primaryKey = 'id_pemesanan';

    protected $fillable = [
        'id_user',
        'nama_pemesan',
        'email',
        'tgl_check_in',
        'tgl_check_out',
        'jumlah_kamar',
        'status_pemesanan',
        'order_id',
        'total_harga',
        'snap_token',
        'payment_status',
        'addon_sarapan',
        'addon_alat_tidur',
        'denda',
        'keterangan_denda',
        'checkout_by_user',
        'user_checkout_at',
        'cancelled_by_user',
        'user_cancel_at',
        'cancel_reason',
        // Kolom voucher
        'id_voucher',
        'diskon_voucher',
        'kode_voucher_used',
    ];

    protected $casts = [
        'tgl_check_in' => 'datetime',
        'tgl_check_out' => 'datetime',
        'denda' => 'decimal:2',

        'total_harga' => 'decimal:2',
        'addon_sarapan' => 'boolean',
        'addon_alat_tidur' => 'boolean',

        'checkout_by_user' => 'boolean',
        'cancelled_by_user' => 'boolean',
        'user_checkout_at' => 'datetime',
        'user_cancel_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // public function tipeKamar(): BelongsTo
    // {
    //     return $this->belongsTo(TipeKamar::class, 'id_tipe_kamar', 'id_tipe_kamar');
    // }

    public function detailPemesanan(): HasMany
    {
        return $this->hasMany(DetailPemesanan::class, 'id_pemesanan', 'id_pemesanan');
    }

    /**
     * Scope untuk filter pemesanan yang checkout oleh user
     */
    public function scopeCheckoutByUser($query)
    {
        return $query->where('checkout_by_user', true);
    }

    /**
     * Scope untuk filter pemesanan yang dibatalkan oleh user
     */
    public function scopeCancelledByUser($query)
    {
        return $query->where('cancelled_by_user', true);
    }

    /**
     * Accessor untuk mendapatkan total yang harus dibayar (termasuk denda)
     */
    public function getTotalWithDendaAttribute()
    {
        return $this->total_harga + $this->denda;
    }
}
