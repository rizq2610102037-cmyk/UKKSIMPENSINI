<?php

namespace App\Http\Controllers\User;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Kamar;
use App\Models\Voucher;
use App\Models\Pemesanan;
use App\Models\TipeKamar;
use App\Models\UserVoucher;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DetailPemesanan;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    // Setup Midtrans
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    // Menampilkan form booking
    public function create(Request $request)
    {
        $tipeKamarIds = $request->input('tipe_kamar', []);

        if (empty($tipeKamarIds)) {
            return redirect()->back()->with('error', 'Minimal 1 tipe kamar terpilih!');
        }

        $tipeKamarList = TipeKamar::whereIn('id_tipe_kamar', $tipeKamarIds)
            ->withCount('kamar')
            ->get();

        foreach ($tipeKamarList as $tipe) {
            $tipe->jumlah_kamar_total = $tipe->kamar_count;
        }

        return view('user.booking', compact('tipeKamarList'));
    }

    /**
     * API untuk mendapatkan kamar yang tersedia berdasarkan tanggal
     */
    public function getAvailableRooms(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_tipe_kamar' => 'required|exists:tipe_kamar,id_tipe_kamar',
                'tgl_check_in' => 'required|date',
                'tgl_check_out' => 'required|date|after:tgl_check_in',
            ]);

            $checkIn = $validated['tgl_check_in'];
            $checkOut = $validated['tgl_check_out'];
            $tipeKamarId = $validated['id_tipe_kamar'];

            // Gunakan scope available dari Model Kamar
            $availableRooms = Kamar::where('id_tipe_kamar', $tipeKamarId)
                ->available($checkIn, $checkOut)
                ->orderBy('nomor_kamar', 'asc')
                ->get(['id_kamar', 'nomor_kamar', 'nama_kamar']);

            return response()->json([
                'success' => true,
                'rooms' => $availableRooms
            ]);
        } catch (\Exception $e) {
            Log::error('Get Available Rooms Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Simpan pemesanan dan buat snap token
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_pemesan' => 'required|string|max:255',
                'email' => 'required|email',
                'tgl_check_in' => 'required|date|after_or_equal:today',
                'tgl_check_out' => 'required|date|after:tgl_check_in',
                'kamar_ids' => 'required|array|min:1',
                'kamar_ids.*' => 'exists:kamar,id_kamar',
                'addon_sarapan' => 'nullable|boolean',
                'addon_alat_tidur' => 'nullable|boolean',
                'voucher_id' => 'nullable|exists:vouchers,id_voucher', // ← TAMBAHAN
            ]);

            $orderId = 'ORDER-' . time() . '-' . Str::random(8);

            $checkIn = \Carbon\Carbon::parse($request->tgl_check_in);
            $checkOut = \Carbon\Carbon::parse($request->tgl_check_out);
            $jumlahMalam = $checkIn->diffInDays($checkOut);

            if ($jumlahMalam <= 0) {
                return response()->json(['error' => 'Tanggal check-out harus setelah check-in'], 400);
            }

            // Validasi ketersediaan kamar
            foreach ($validated['kamar_ids'] as $kamarId) {
                $kamar = Kamar::find($kamarId);
                if (!$kamar->isAvailable($request->tgl_check_in, $request->tgl_check_out)) {
                    return response()->json([
                        'error' => "Kamar {$kamar->nomor_kamar} sudah tidak tersedia untuk tanggal tersebut"
                    ], 400);
                }
            }

            // Hitung total harga
            $totalHarga = 0;
            $itemDetails = [];
            $kamarDetails = [];

            foreach ($validated['kamar_ids'] as $kamarId) {
                $kamar = Kamar::with('tipeKamar')->find($kamarId);
                $hargaPerMalam = (int) $kamar->tipeKamar->harga;
                $subtotal = $hargaPerMalam * $jumlahMalam;

                $totalHarga += $subtotal;

                $itemDetails[] = [
                    'id' => 'kamar-' . $kamarId,
                    'price' => $hargaPerMalam,
                    'quantity' => $jumlahMalam,
                    'name' => $kamar->tipeKamar->nama_tipe . ' - Kamar ' . $kamar->nomor_kamar,
                ];

                $kamarDetails[] = [
                    'id_kamar' => $kamar->id_kamar,
                    'id_tipe_kamar' => $kamar->id_tipe_kamar,
                    'harga_per_malam' => $hargaPerMalam
                ];
            }

            // Tambahan biaya add-on
            if ($request->has('addon_sarapan')) {
                $biayaSarapan = 300000;
                $totalHarga += $biayaSarapan;
                $itemDetails[] = [
                    'id' => 'addon-sarapan',
                    'price' => $biayaSarapan,
                    'quantity' => 1,
                    'name' => 'Sarapan (Add-on)',
                ];
            }

            if ($request->has('addon_alat_tidur')) {
                $biayaAlatTidur = 300000;
                $totalHarga += $biayaAlatTidur;
                $itemDetails[] = [
                    'id' => 'addon-alat-tidur',
                    'price' => $biayaAlatTidur,
                    'quantity' => 1,
                    'name' => 'Alat Tidur Tambahan (Add-on)',
                ];
            }

            // ✅ PROSES VOUCHER
            $diskonVoucher = 0;
            $voucherId = null;
            $kodeVoucherUsed = null;

            if ($request->filled('voucher_id')) {
                $voucher = Voucher::find($request->voucher_id);

                if ($voucher) {
                    $validation = $voucher->canBeUsedBy(Auth::id(), $totalHarga);

                    if ($validation['can_use']) {
                        $diskonVoucher = $voucher->jumlah_diskon;
                        $voucherId = $voucher->id_voucher;
                        $kodeVoucherUsed = $voucher->kode_voucher;

                        // Tambahkan diskon ke item details (sebagai item negatif)
                        $itemDetails[] = [
                            'id' => 'voucher-discount',
                            'price' => -$diskonVoucher, // Negatif untuk diskon
                            'quantity' => 1,
                            'name' => 'Diskon Voucher (' . $voucher->kode_voucher . ')',
                        ];
                    }
                }
            }

            // Total setelah diskon
            $totalSetelahDiskon = $totalHarga - $diskonVoucher;

            // Pastikan tidak negatif
            if ($totalSetelahDiskon < 0) {
                $totalSetelahDiskon = 0;
            }

            // Buat pemesanan
            $pemesanan = Pemesanan::create([
                'id_user' => Auth::id(),
                'nama_pemesan' => $request->nama_pemesan,
                'email' => $request->email,
                'tgl_check_in' => $request->tgl_check_in,
                'tgl_check_out' => $request->tgl_check_out,
                'jumlah_kamar' => count($validated['kamar_ids']),
                'order_id' => $orderId,
                'total_harga' => $totalHarga,
                'id_voucher' => $voucherId, // ← SIMPAN VOUCHER
                'diskon_voucher' => $diskonVoucher, // ← SIMPAN DISKON
                'kode_voucher_used' => $kodeVoucherUsed, // ← SIMPAN KODE
                'addon_sarapan' => $request->has('addon_sarapan'),
                'addon_alat_tidur' => $request->has('addon_alat_tidur'),
                'status_pemesanan' => 'pending',
                'payment_status' => 'pending',
            ]);

            // Simpan detail pemesanan
            foreach ($kamarDetails as $detail) {
                DetailPemesanan::create([
                    'id_pemesanan' => $pemesanan->id_pemesanan,
                    'id_kamar' => $detail['id_kamar'],
                    'id_tipe_kamar' => $detail['id_tipe_kamar'],
                    'jumlah_kamar' => 1,
                    'harga' => $detail['harga_per_malam'],
                    'harga_per_malam' => $detail['harga_per_malam'],
                    'subtotal' => $detail['harga_per_malam'] * $jumlahMalam,
                ]);
            }

            // ✅ TANDAI VOUCHER SEBAGAI DIGUNAKAN (tapi baru setelah payment berhasil)
            // Ini akan dihandle di callback/checkPaymentStatus

            // Setup Midtrans
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $totalSetelahDiskon, // ← Gunakan total setelah diskon
                ],
                'customer_details' => [
                    'first_name' => $request->nama_pemesan,
                    'email' => $request->email,
                ],
                'item_details' => $itemDetails
            ];

            Log::info('Midtrans Request:', $params);

            $snapToken = Snap::getSnapToken($params);
            $pemesanan->update(['snap_token' => $snapToken]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'total_original' => $totalHarga,
                'diskon' => $diskonVoucher,
                'total_bayar' => $totalSetelahDiskon
            ]);
        } catch (\Exception $e) {
            Log::error('Booking Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Callback dari Midtrans
    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        Log::info('Midtrans Callback Received:', [
            'order_id' => $request->order_id,
            'transaction_status' => $request->transaction_status,
            'payment_type' => $request->payment_type,
            'signature_valid' => ($hashed == $request->signature_key)
        ]);

        if ($hashed == $request->signature_key) {
            $pemesanan = Pemesanan::where('order_id', $request->order_id)->first();

            if ($pemesanan) {
                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    $pemesanan->update([
                        'payment_status' => 'paid',
                        'status_pemesanan' => 'confirm'
                    ]);

                    // ✅ TANDAI VOUCHER SEBAGAI DIGUNAKAN
                    if ($pemesanan->id_voucher) {
                        $userVoucher = UserVoucher::where('id_user', $pemesanan->id_user)
                            ->where('id_voucher', $pemesanan->id_voucher)
                            ->where('is_used', false)
                            ->first();

                        if ($userVoucher) {
                            $userVoucher->update([
                                'is_used' => true,
                                'used_at' => now(),
                                'id_pemesanan' => $pemesanan->id_pemesanan
                            ]);

                            // Update total penggunaan voucher
                            $voucher = Voucher::find($pemesanan->id_voucher);
                            if ($voucher) {
                                $voucher->increment('total_digunakan');
                            }

                            Log::info('Voucher marked as used: ' . $pemesanan->kode_voucher_used);
                        }
                    }

                    Log::info('Payment Success for Order: ' . $request->order_id);
                } elseif ($request->transaction_status == 'pending') {
                    $pemesanan->update(['payment_status' => 'pending']);
                    Log::info('Payment Pending for Order: ' . $request->order_id);
                } elseif ($request->transaction_status == 'deny' || $request->transaction_status == 'expire' || $request->transaction_status == 'cancel') {
                    $pemesanan->update([
                        'payment_status' => 'failed',
                        'status_pemesanan' => 'cancel'
                    ]);
                    // Voucher tidak di-mark sebagai used karena payment gagal
                    Log::info('Payment Failed for Order: ' . $request->order_id);
                }
            } else {
                Log::warning('Order not found: ' . $request->order_id);
            }
        } else {
            Log::error('Invalid Signature for Order: ' . $request->order_id);
        }

        return response()->json(['status' => 'success']);
    }

    // Method untuk cek status pembayaran manual
    public function checkPaymentStatus(Request $request)
    {
        try {
            $orderId = $request->order_id;

            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');

            $statusResponse = \Midtrans\Transaction::status($orderId);
            $transactionStatus = $statusResponse->transaction_status ?? null;
            $paymentType = $statusResponse->payment_type ?? 'N/A';

            Log::info('Payment Status Check:', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'payment_type' => $paymentType
            ]);

            $pemesanan = Pemesanan::where('order_id', $orderId)->first();

            if (!$pemesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemesanan tidak ditemukan'
                ], 404);
            }

            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                $pemesanan->update([
                    'payment_status' => 'paid',
                    'status_pemesanan' => 'confirm'
                ]);

                // ✅ TANDAI VOUCHER SEBAGAI DIGUNAKAN
                if ($pemesanan->id_voucher) {
                    $userVoucher = UserVoucher::where('id_user', $pemesanan->id_user)
                        ->where('id_voucher', $pemesanan->id_voucher)
                        ->where('is_used', false)
                        ->first();

                    if ($userVoucher) {
                        $userVoucher->update([
                            'is_used' => true,
                            'used_at' => now(),
                            'id_pemesanan' => $pemesanan->id_pemesanan
                        ]);

                        $voucher = Voucher::find($pemesanan->id_voucher);
                        if ($voucher) {
                            $voucher->increment('total_digunakan');
                        }

                        Log::info('Voucher marked as used via checkPayment: ' . $pemesanan->kode_voucher_used);
                    }
                }

                Log::info('Payment confirmed for order: ' . $orderId);

                return response()->json([
                    'success' => true,
                    'status' => 'paid',
                    'message' => 'Pembayaran berhasil!'
                ]);
            } elseif ($transactionStatus == 'pending') {
                return response()->json([
                    'success' => false,
                    'status' => 'pending',
                    'message' => 'Pembayaran masih pending'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'status' => $transactionStatus,
                    'message' => 'Status pembayaran: ' . $transactionStatus
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Check Payment Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error checking payment status: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method untuk checkout
    public function checkout($id)
    {
        try {
            $pemesanan = Pemesanan::where('id_pemesanan', $id)
                ->where('id_user', Auth::id())
                ->firstOrFail();

            if ($pemesanan->status_pemesanan !== 'checkin') {
                return redirect()->back()->with('error', 'Hanya pemesanan dengan status check-in yang bisa checkout');
            }

            $pemesanan->update([
                'status_pemesanan' => 'checkout'
            ]);

            return redirect()->route('user.dashboard')->with('success', 'Checkout berhasil!');
        } catch (\Exception $e) {
            Log::error('Checkout Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat checkout');
        }
    }


    /**
     * Tampilkan detail pemesanan / struk pembayaran
     */
    public function receipt($orderId)
    {
        try {
            $pemesanan = Pemesanan::with(['detailPemesanan.tipeKamar', 'detailPemesanan.kamar', 'user'])
                ->where('order_id', $orderId)
                ->where('id_user', Auth::id())
                ->firstOrFail();

            // Hitung jumlah malam menginap
            $checkIn = \Carbon\Carbon::parse($pemesanan->tgl_check_in);
            $checkOut = \Carbon\Carbon::parse($pemesanan->tgl_check_out);
            $jumlahMalam = $checkIn->diffInDays($checkOut);

            // Grouping kamar berdasarkan tipe
            $kamarByTipe = $pemesanan->detailPemesanan->groupBy('id_tipe_kamar');

            $rincianKamar = [];
            foreach ($kamarByTipe as $tipeId => $details) {
                $tipeKamar = $details->first()->tipeKamar;
                $jumlahKamar = $details->count();
                $hargaPerMalam = $tipeKamar->harga;
                $subtotal = $hargaPerMalam * $jumlahKamar * $jumlahMalam;

                $rincianKamar[] = [
                    'tipe' => $tipeKamar->nama_tipe,
                    'jumlah_kamar' => $jumlahKamar,
                    'harga_per_malam' => $hargaPerMalam,
                    'jumlah_malam' => $jumlahMalam,
                    'subtotal' => $subtotal,
                    'nomor_kamar' => $details->pluck('kamar.nomor_kamar')->filter()->toArray()
                ];
            }

            // Hitung biaya add-on
            $biayaAddOn = 0;
            $addOnList = [];

            if ($pemesanan->addon_sarapan) {
                $biayaSarapan = 300000;
                $biayaAddOn += $biayaSarapan;
                $addOnList[] = [
                    'nama' => 'Paket Sarapan',
                    'harga' => $biayaSarapan
                ];
            }

            if ($pemesanan->addon_alat_tidur) {
                $biayaAlatTidur = 300000;
                $biayaAddOn += $biayaAlatTidur;
                $addOnList[] = [
                    'nama' => 'Alat Tidur Tambahan',
                    'harga' => $biayaAlatTidur
                ];
            }

            // Total keseluruhan
            $totalKamar = array_sum(array_column($rincianKamar, 'subtotal'));
            $grandTotal = $totalKamar + $biayaAddOn + $pemesanan->denda;

            return view('user.receipt', compact(
                'pemesanan',
                'rincianKamar',
                'addOnList',
                'jumlahMalam',
                'totalKamar',
                'biayaAddOn',
                'grandTotal'
            ));
        } catch (\Exception $e) {
            Log::error('Receipt Error: ' . $e->getMessage());
            return redirect()->route('user.dashboard')->with('error', 'Struk pembayaran tidak ditemukan');
        }
    }

    /**
     * Get voucher yang tersedia untuk user
     */
    public function getUserVouchers(Request $request)
    {
        try {
            // ✅ FIX: Gunakan query yang lebih eksplisit
            $vouchers = Voucher::whereHas('users', function ($query) {
                $query->where('users.id_user', Auth::id())
                    ->where('user_vouchers.is_used', false);
            })
                ->where('is_active', true)
                ->where('tanggal_mulai', '<=', now())
                ->where('tanggal_berakhir', '>=', now())
                ->get();

            return response()->json([
                'success' => true,
                'vouchers' => $vouchers->map(function ($voucher) {
                    return [
                        'id' => $voucher->id_voucher,
                        'kode' => $voucher->kode_voucher,
                        'nama' => $voucher->nama_voucher,
                        'deskripsi' => $voucher->deskripsi,
                        'diskon' => $voucher->jumlah_diskon,
                        'minimal_transaksi' => $voucher->minimal_transaksi,
                        'tanggal_berakhir' => $voucher->tanggal_berakhir->format('d M Y')
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Get User Vouchers Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validasi voucher
     */
    /**
     * Validasi voucher
     */
    public function validateVoucher(Request $request)
    {
        try {
            $request->validate([
                'kode_voucher' => 'required|string',
                'total_transaksi' => 'required|numeric|min:0'
            ]);

            Log::info('Validating voucher:', [
                'kode' => $request->kode_voucher,
                'total' => $request->total_transaksi,
                'user_id' => Auth::id()
            ]);

            $voucher = Voucher::where('kode_voucher', $request->kode_voucher)->first();

            if (!$voucher) {
                Log::warning('Voucher not found:', ['kode' => $request->kode_voucher]);
                return response()->json([
                    'success' => false,
                    'message' => 'Kode voucher tidak ditemukan'
                ]);
            }

            Log::info('Voucher found:', [
                'id' => $voucher->id_voucher,
                'nama' => $voucher->nama_voucher,
                'is_active' => $voucher->is_active,
                'tanggal_mulai' => $voucher->tanggal_mulai,
                'tanggal_berakhir' => $voucher->tanggal_berakhir
            ]);

            $validation = $voucher->canBeUsedBy(Auth::id(), $request->total_transaksi);

            Log::info('Validation result:', $validation);

            if (!$validation['can_use']) {
                return response()->json([
                    'success' => false,
                    'message' => $validation['message']
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Voucher berhasil diterapkan!',
                'voucher' => [
                    'id' => $voucher->id_voucher,
                    'kode' => $voucher->kode_voucher,
                    'nama' => $voucher->nama_voucher,
                    'diskon' => $voucher->jumlah_diskon,
                    'minimal_transaksi' => $voucher->minimal_transaksi
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error:', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid: ' . json_encode($e->errors())
            ], 422);
        } catch (\Exception $e) {
            Log::error('Validate Voucher Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
