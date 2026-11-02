<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\View\View;
use App\Models\UserVoucher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_user' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'nama_user' => $request->nama_user,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default role untuk registrasi
        ]);

        // Auto Assign Voucher
        $welcomeVoucher = Voucher::where('tipe_voucher', 'welcome')
            ->where('is_active', true)
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_berakhir', '>=', now())
            ->first();

        if ($welcomeVoucher) {
            UserVoucher::create([
                'id_user' => $user->id_user,
                'id_voucher' => $welcomeVoucher->id_voucher,
                'is_used' => false,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('user.dashboard');
    }
}
