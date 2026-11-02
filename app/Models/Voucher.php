<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Voucher extends Model
{
    protected $table = 'vouchers';
    protected $primaryKey = 'id_voucher';

    protected $fillable = [
        'kode_voucher',
        'nama_voucher',
        'deskripsi',
        'jumlah_diskon',
        'minimal_transaksi',
        'tipe_voucher',
        'tanggal_mulai',
        'tanggal_berakhir',
        'is_active',
        'maksimal_penggunaan',
        'total_digunakan',
    ];

    protected $casts = [
        'jumlah_diskon' => 'decimal:2',
        'minimal_transaksi' => 'decimal:2',
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi many-to-many dengan User
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_vouchers', 'id_voucher', 'id_user')
            ->withPivot('is_used', 'used_at', 'id_pemesanan')
            ->withTimestamps();
    }

    /**
     * Check apakah voucher masih valid
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $today = now()->startOfDay();

        if ($this->tanggal_mulai > $today || $this->tanggal_berakhir < $today) {
            return false;
        }

        // Check maksimal penggunaan
        if ($this->maksimal_penggunaan && $this->total_digunakan >= $this->maksimal_penggunaan) {
            return false;
        }

        return true;
    }

    /**
     * Check apakah user bisa menggunakan voucher ini
     */
    public function canBeUsedBy($userId, $totalTransaksi): array
    {
        if (!$this->isValid()) {
            return [
                'can_use' => false,
                'message' => 'Voucher tidak valid atau sudah expired'
            ];
        }

        if ($totalTransaksi < $this->minimal_transaksi) {
            return [
                'can_use' => false,
                'message' => 'Minimal transaksi Rp ' . number_format($this->minimal_transaksi, 0, ',', '.')
            ];
        }

        $userVoucher = $this->users()
            ->where('users.id_user', $userId)  // ← Specify table name
            ->wherePivot('is_used', false)
            ->first();

        if (!$userVoucher) {
            return [
                'can_use' => false,
                'message' => 'Anda tidak memiliki voucher ini'
            ];
        }

        return [
            'can_use' => true,
            'message' => 'Voucher dapat digunakan',
            'diskon' => $this->jumlah_diskon
        ];
    }

    /**
     * Scope untuk voucher yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_berakhir', '>=', now());
    }
}
