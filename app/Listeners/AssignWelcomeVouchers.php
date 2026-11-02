<?php

namespace App\Listeners;

use App\Models\Voucher;
use App\Models\UserVoucher;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;

class AssignWelcomeVouchers
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;

        // Get all welcome vouchers
        $welcomeVouchers = Voucher::where('tipe_voucher', 'welcome')
            ->where('is_active', true)
            ->get();

        foreach ($welcomeVouchers as $voucher) {
            try {
                UserVoucher::create([
                    'id_user' => $user->id_user,
                    'id_voucher' => $voucher->id_voucher,
                    'is_used' => false,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to assign voucher to user: ' . $e->getMessage());
            }
        }

        Log::info("Welcome vouchers assigned to user: {$user->email}");
    }
}
