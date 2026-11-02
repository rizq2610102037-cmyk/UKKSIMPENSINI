<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nama_user',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determine if the user can access the Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Get the user's name for Filament (REQUIRED)
     * Harus return string, tidak boleh null
     */
    public function getFilamentName(): string
    {
        return $this->nama_user ?? $this->email ?? 'User';
    }

    /**
     * Get name - fallback method
     */
    public function getName(): string
    {
        return $this->nama_user ?? $this->email ?? 'User';
    }

    /**
     * Get name attribute (untuk compatibility)
     */
    public function getNameAttribute(): string
    {
        return $this->nama_user ?? '';
    }

    // Helper method untuk cek role
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isReservasi(): bool
    {
        return $this->role === 'reservasi';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Relasi many-to-many dengan Voucher
     */
    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class, 'user_vouchers', 'id_user', 'id_voucher')
            ->withPivot('is_used', 'used_at', 'id_pemesanan')
            ->withTimestamps();
    }

    /**
     * Scope untuk filter voucher yang tersedia
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_berakhir', '>=', now());
    }

    /**
     * Get voucher yang belum digunakan
     */
    public function availableVouchers()
    {
        return $this->vouchers()
            ->wherePivot('is_used', false)
            ->available(); // gunakan scope
    }

    /**
     * Get user voucher record
     */
    public function userVouchers()
    {
        return $this->hasMany(UserVoucher::class, 'id_user', 'id_user');
    }
}
