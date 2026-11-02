<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailReservasi extends Model
{
    protected $table = 'detail_reservasi';
    protected $primaryKey = 'id_detail_reservasi';

    protected $fillable = [
        'id_reservasi',
        'id_tipe_kamar',
        'tgl_akses',
    ];

    protected $casts = [
        'tgl_akses' => 'datetime',
    ];

    public function reservasi(): BelongsTo
    {
        return $this->belongsTo(Reservasi::class, 'id_reservasi', 'id_reservasi');
    }

    public function tipeKamar(): BelongsTo
    {
        return $this->belongsTo(TipeKamar::class, 'id_tipe_kamar', 'id_tipe_kamar');
    }
}
