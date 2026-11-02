<?php

namespace App\Http\Controllers\User;

use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil semua pemesanan milik user yang sedang login
        $pemesanan = Pemesanan::where('id_user', Auth::id())
            ->with('detailPemesanan.tipeKamar') // Eager load relasi
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.dashboard', compact('pemesanan'));
    }

    /**
     * User melakukan checkout sendiri (cancel/pembatalan)
     */
    public function checkout($id)
    {
        try {
            $pemesanan = Pemesanan::where('id_pemesanan', $id)
                ->where('id_user', Auth::id())
                ->firstOrFail();

            // Validasi: hanya bisa checkout jika status checkin
            if ($pemesanan->status_pemesanan !== 'checkin') {
                return redirect()->back()->with('error', 'Pemesanan tidak dapat di-checkout. Status saat ini: ' . $pemesanan->status_pemesanan);
            }

            // Update status menjadi checkout dengan catatan checkout oleh user
            $pemesanan->update([
                'status_pemesanan' => 'checkout',
                'checkout_by_user' => true, // Tandai bahwa checkout dilakukan oleh user
                'user_checkout_at' => now() // Waktu checkout oleh user
            ]);

            Log::info('User checkout: ' . $pemesanan->order_id . ' oleh user ID: ' . Auth::id());

            return redirect()->back()->with('success', 'Checkout berhasil! Terima kasih telah menginap di Hotel Bagustan.');
        } catch (\Exception $e) {
            Log::error('User checkout error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal melakukan checkout: ' . $e->getMessage());
        }
    }

    /**
     * User membatalkan pemesanan sebelum check-in
     */
    public function cancelBooking($id)
    {
        try {
            $pemesanan = Pemesanan::where('id_pemesanan', $id)
                ->where('id_user', Auth::id())
                ->firstOrFail();

            // Validasi: hanya bisa cancel jika status confirm (belum check-in)
            if ($pemesanan->status_pemesanan !== 'confirm') {
                return redirect()->back()->with('error', 'Pemesanan tidak dapat dibatalkan. Status saat ini: ' . $pemesanan->status_pemesanan);
            }

            // Update status menjadi cancel
            $pemesanan->update([
                'status_pemesanan' => 'cancel',
                'cancelled_by_user' => true,
                'user_cancel_at' => now(),
                'cancel_reason' => 'Dibatalkan oleh user'
            ]);

            Log::info('User cancel booking: ' . $pemesanan->order_id . ' oleh user ID: ' . Auth::id());

            return redirect()->back()->with('success', 'Pemesanan berhasil dibatalkan.');
        } catch (\Exception $e) {
            Log::error('User cancel booking error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membatalkan pemesanan: ' . $e->getMessage());
        }
    }
}
