<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Pemesanan;
use App\Models\Reservasi;
use App\Models\TipeKamar;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DetailPemesanan;
use App\Models\DetailReservasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ReservasiController extends Controller
{
    /**
     * Tampilkan dashboard reservasi dengan data tamu yang sudah bayar
     */
    public function dashboard()
    {
        $pemesananList = Pemesanan::with(['user', 'detailPemesanan.tipeKamar', 'detailPemesanan.kamar']) // ← Tambahkan ini
            ->where('payment_status', 'paid')
            ->whereIn('status_pemesanan', ['confirm', 'checkin', 'checkout'])
            ->orderByRaw("CASE 
            WHEN checkout_by_user = 1 THEN 0 
            ELSE 1 
        END")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total_tamu' => Pemesanan::where('payment_status', 'paid')
                ->whereIn('status_pemesanan', ['confirm', 'checkin'])
                ->count(),
            'checked_in' => Pemesanan::where('status_pemesanan', 'checkin')->count(),
            'menunggu_checkin' => Pemesanan::where('payment_status', 'paid')
                ->where('status_pemesanan', 'confirm')
                ->count(),
            'checked_out_hari_ini' => Pemesanan::where('status_pemesanan', 'checkout')
                ->whereDate('updated_at', today())
                ->count(),
        ];

        return view('reservasi.dashboard', compact('pemesananList', 'stats'));
    }

    /**
     * ✅ FORM BOOKING OFFLINE (Walk-in Guest)
     */
    public function bookingOfflineForm()
    {
        $tipeKamarList = TipeKamar::with('kamar')->get();

        return view('reservasi.booking-offline', compact('tipeKamarList'));
    }

    /**
     * ✅ CEK KETERSEDIAAN KAMAR UNTUK BOOKING OFFLINE
     */
    public function checkAvailability(Request $request)
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
            Log::error('Check Availability Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ SIMPAN BOOKING OFFLINE (FIXED - Return pemesanan_id)
     */
    public function storeOfflineBooking(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'nama_tamu' => 'required|string|max:255',
                'email_tamu' => 'required|email',
                'no_telp_tamu' => 'required|string|max:15',
                'tgl_check_in' => 'required|date',
                'tgl_check_out' => 'required|date|after:tgl_check_in',
                'kamar_ids' => 'required|array|min:1',
                'kamar_ids.*' => 'exists:kamar,id_kamar',
                'metode_pembayaran' => 'required|in:cash,debit,credit,transfer',
                'catatan' => 'nullable|string|max:500',
            ]);

            $orderId = 'OFFLINE-' . time() . '-' . Str::random(6);

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
            $kamarDetails = [];

            foreach ($validated['kamar_ids'] as $kamarId) {
                $kamar = Kamar::with('tipeKamar')->find($kamarId);
                $hargaPerMalam = (int) $kamar->tipeKamar->harga;
                $subtotal = $hargaPerMalam * 1 * $jumlahMalam;

                $totalHarga += $subtotal;

                $kamarDetails[] = [
                    'id_kamar' => $kamar->id_kamar,
                    'id_tipe_kamar' => $kamar->id_tipe_kamar,
                    'harga_per_malam' => $hargaPerMalam,
                    'jumlah_kamar' => 1,
                    'subtotal' => $subtotal
                ];
            }

            // Buat pemesanan
            $pemesanan = Pemesanan::create([
                'id_user' => null,
                'nama_pemesan' => $request->nama_tamu,
                'email' => $request->email_tamu,
                'tgl_check_in' => $request->tgl_check_in,
                'tgl_check_out' => $request->tgl_check_out,
                'jumlah_kamar' => count($validated['kamar_ids']),
                'order_id' => $orderId,
                'total_harga' => $totalHarga,
                'status_pemesanan' => 'confirm',
                'payment_status' => 'paid',
                'addon_sarapan' => false,
                'addon_alat_tidur' => false,
            ]);

            // Simpan detail pemesanan
            foreach ($kamarDetails as $detail) {
                DetailPemesanan::create([
                    'id_pemesanan' => $pemesanan->id_pemesanan,
                    'id_kamar' => $detail['id_kamar'],
                    'id_tipe_kamar' => $detail['id_tipe_kamar'],
                    'harga_per_malam' => $detail['harga_per_malam'],
                ]);
            }

            DB::commit();

            Log::info('Offline booking created by: ' . Auth::user()->nama_user, [
                'order_id' => $orderId,
                'nama_tamu' => $request->nama_tamu,
                'total_harga' => $totalHarga,
                'metode_pembayaran' => $request->metode_pembayaran
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking offline berhasil dibuat!',
                'order_id' => $orderId,
                'pemesanan_id' => $pemesanan->id_pemesanan, // ✅ TAMBAHKAN INI
                'total_harga' => $totalHarga,
                'data' => $pemesanan
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Validasi gagal: ' . json_encode($e->errors())
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Offline Booking Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check-in tamu - Update status menjadi checkin
     */
    public function checkIn($id)
    {
        try {
            DB::beginTransaction();

            $pemesanan = Pemesanan::with('detailPemesanan')->findOrFail($id);

            if ($pemesanan->payment_status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran belum lunas!'
                ], 400);
            }

            if ($pemesanan->status_pemesanan !== 'confirm') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemesanan tidak dapat di-check-in. Status saat ini: ' . $pemesanan->status_pemesanan
                ], 400);
            }

            $reservasi = Reservasi::create([
                'tgl_reservasi' => now()->toDateString(),
            ]);

            foreach ($pemesanan->detailPemesanan as $detail) {
                DetailReservasi::create([
                    'id_reservasi' => $reservasi->id_reservasi,
                    'id_tipe_kamar' => $detail->id_tipe_kamar,
                    'tgl_akses' => now()->toDateString(),
                ]);
            }

            $pemesanan->update([
                'status_pemesanan' => 'checkin'
            ]);

            DB::commit();

            Log::info('Check-in berhasil untuk pemesanan: ' . $pemesanan->order_id);

            return response()->json([
                'success' => true,
                'message' => 'Tamu berhasil check-in!',
                'data' => $pemesanan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Check-in error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan check-in: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check-out tamu - Update status menjadi checkout
     */
    public function checkOut($id)
    {
        try {
            $pemesanan = Pemesanan::findOrFail($id);

            if ($pemesanan->status_pemesanan !== 'checkin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemesanan tidak dapat di-check-out. Status saat ini: ' . $pemesanan->status_pemesanan
                ], 400);
            }

            $pemesanan->update([
                'status_pemesanan' => 'checkout'
            ]);

            Log::info('Check-out berhasil untuk pemesanan: ' . $pemesanan->order_id . ' oleh staff reservasi');

            return response()->json([
                'success' => true,
                'message' => 'Tamu berhasil check-out!',
                'data' => $pemesanan
            ]);
        } catch (\Exception $e) {
            Log::error('Check-out error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan check-out: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tambahkan denda ke pemesanan
     */
    public function addDenda(Request $request, $id)
    {
        $request->validate([
            'denda' => 'required|numeric|min:0',
            'keterangan_denda' => 'required|string|max:500'
        ]);

        try {
            $pemesanan = Pemesanan::findOrFail($id);

            $totalDenda = ($pemesanan->denda ?? 0) + $request->denda;

            $pemesanan->update([
                'denda' => $totalDenda,
                'keterangan_denda' => $request->keterangan_denda
            ]);

            Log::info('Denda ditambahkan untuk pemesanan: ' . $pemesanan->order_id . ' sebesar Rp ' . number_format($request->denda));

            return response()->json([
                'success' => true,
                'message' => 'Denda berhasil ditambahkan!',
                'data' => $pemesanan
            ]);
        } catch (\Exception $e) {
            Log::error('Add denda error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan denda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil detail pemesanan
     */
    public function getDetail($id)
    {
        try {
            $pemesanan = Pemesanan::with(['user', 'detailPemesanan.tipeKamar'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $pemesanan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Tampilkan struk pembayaran offline - FIXED untuk struktur DB saat ini
     */
    public function showReceipt($id)
    {
        try {
            $pemesanan = Pemesanan::findOrFail($id);

            // Hitung jumlah malam
            $checkIn = \Carbon\Carbon::parse($pemesanan->tgl_check_in);
            $checkOut = \Carbon\Carbon::parse($pemesanan->tgl_check_out);
            $jumlahMalam = $checkIn->diffInDays($checkOut);

            // Ambil detail dengan JOIN manual (karena struktur DB berbeda)
            $details = DB::table('detail_pemesanan as dp')
                ->join('tipe_kamar as tk', 'dp.id_tipe_kamar', '=', 'tk.id_tipe_kamar')
                ->leftJoin('kamar as k', 'dp.id_kamar', '=', 'k.id_kamar')
                ->where('dp.id_pemesanan', $pemesanan->id_pemesanan)
                ->select(
                    'dp.id_detail_pemesanan',
                    'dp.id_tipe_kamar',
                    'dp.id_kamar',
                    'dp.harga_per_malam',
                    'tk.nama_tipe',
                    'k.nomor_kamar'
                )
                ->get();

            Log::info('Receipt Details', [
                'pemesanan_id' => $pemesanan->id_pemesanan,
                'jumlah_malam' => $jumlahMalam,
                'jumlah_detail' => $details->count()
            ]);

            // Rincian kamar per tipe
            $rincianKamar = [];
            $totalKamar = 0;

            foreach ($details as $detail) {
                $tipeId = $detail->id_tipe_kamar;
                $tipeNama = $detail->nama_tipe;
                $hargaPerMalam = $detail->harga_per_malam;

                // ✅ SETIAP detail = 1 kamar (karena insert per kamar)
                $jumlahKamarDetail = 1;

                // ✅ Hitung subtotal: harga × 1 kamar × jumlah malam
                $subtotalDetail = $hargaPerMalam * $jumlahKamarDetail * $jumlahMalam;

                Log::info('Processing Detail', [
                    'detail_id' => $detail->id_detail_pemesanan,
                    'tipe' => $tipeNama,
                    'harga_per_malam' => $hargaPerMalam,
                    'jumlah_malam' => $jumlahMalam,
                    'subtotal' => $subtotalDetail,
                    'nomor_kamar' => $detail->nomor_kamar
                ]);

                // Grouping by tipe ID
                if (!isset($rincianKamar[$tipeId])) {
                    $rincianKamar[$tipeId] = [
                        'tipe' => $tipeNama,
                        'jumlah_kamar' => 0,
                        'harga_per_malam' => $hargaPerMalam,
                        'nomor_kamar' => [],
                        'jumlah_malam' => $jumlahMalam,
                        'subtotal' => 0
                    ];
                }

                // Akumulasi per tipe
                $rincianKamar[$tipeId]['jumlah_kamar'] += $jumlahKamarDetail;
                $rincianKamar[$tipeId]['subtotal'] += $subtotalDetail;
                $totalKamar += $subtotalDetail;

                // Tambahkan nomor kamar
                if ($detail->nomor_kamar) {
                    $rincianKamar[$tipeId]['nomor_kamar'][] = $detail->nomor_kamar;
                }
            }

            Log::info('Rincian Kamar Final', [
                'rincianKamar' => $rincianKamar,
                'totalKamar' => $totalKamar
            ]);

            // Add-ons
            $addOnList = [];
            $biayaAddOn = 0;

            if ($pemesanan->addon_sarapan) {
                $hargaSarapan = 50000 * $pemesanan->jumlah_kamar * $jumlahMalam;
                $addOnList[] = ['nama' => 'Sarapan', 'harga' => $hargaSarapan];
                $biayaAddOn += $hargaSarapan;
            }

            if ($pemesanan->addon_alat_tidur) {
                $hargaAlatTidur = 30000 * $pemesanan->jumlah_kamar;
                $addOnList[] = ['nama' => 'Alat Tidur Tambahan', 'harga' => $hargaAlatTidur];
                $biayaAddOn += $hargaAlatTidur;
            }

            // Grand total
            $grandTotal = $totalKamar + $biayaAddOn + ($pemesanan->denda ?? 0);

            Log::info('Receipt Final Calculation', [
                'totalKamar' => $totalKamar,
                'biayaAddOn' => $biayaAddOn,
                'denda' => $pemesanan->denda ?? 0,
                'grandTotal' => $grandTotal
            ]);

            return view('reservasi.receipt', compact(
                'pemesanan',
                'jumlahMalam',
                'rincianKamar',
                'totalKamar',
                'addOnList',
                'biayaAddOn',
                'grandTotal'
            ));
        } catch (\Exception $e) {
            Log::error('Receipt Error: ' . $e->getMessage());
            Log::error('Stack Trace: ' . $e->getTraceAsString());
            return redirect()->route('reservasi.dashboard')
                ->with('error', 'Struk tidak ditemukan: ' . $e->getMessage());
        }
    }
}
