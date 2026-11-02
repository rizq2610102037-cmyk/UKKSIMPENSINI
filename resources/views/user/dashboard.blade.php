<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-center text-lg md:text-xl text-black/80">
            {{ __('Pesanan - Pengguna') }}
        </h2>
    </x-slot>

    {{-- Head Section - Alert Voucher Component --}}
    <section>
        <div class="container mx-auto py-24 px-6 md:px-0">
            <!-- Alert Messages -->
            @if (session('success'))
                <div
                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded-xl mb-8 text-md md:text-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded-xl mb-8 text-md md:text-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Voucher --}}
            @php
                $myVouchers = Auth::user()
                    ->vouchers()
                    ->wherePivot('is_used', false)
                    ->where('is_active', true)
                    ->where('tanggal_berakhir', '>=', now())
                    ->get();
            @endphp

            @if ($myVouchers->count() > 0)
                <div class="bg-[#9CC6DB]/10 rounded-2xl p-6 mb-8 border-2 border-[#9CC6DB]/50">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-[#1581BF] text-white p-3 rounded-full">
                                <i class="fas fa-ticket-alt text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-black/80">Voucher Saya</h2>
                                <p class="font-light text-md md:text-lg text-black/60">Anda memiliki
                                    {{ $myVouchers->count() }} voucher aktif</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($myVouchers as $voucher)
                            <div
                                class="bg-white rounded-xl overflow-hidden border-2 border-black/10 hover:border-[#1581BF] transition-all duration-500">
                                <div class="p-5">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span
                                                    class="bg-[#1581BF] text-white text-xs font-bold px-3 py-1 rounded-full">
                                                    {{ $voucher->kode_voucher }}
                                                </span>
                                                @if ($voucher->tipe_voucher === 'welcome')
                                                    <span
                                                        class="bg-blue-100 text-blue-600 text-xs font-medium px-2 py-1 rounded">
                                                        Welcome 🎉
                                                    </span>
                                                @elseif($voucher->tipe_voucher === 'special')
                                                    <span
                                                        class="bg-purple-100 text-purple-600 text-xs font-medium px-2 py-1 rounded">
                                                        Special ⭐
                                                    </span>
                                                @endif
                                            </div>
                                            <h3 class="font-bold text-gray-800 text-lg mb-1">
                                                {{ $voucher->nama_voucher }}
                                            </h3>
                                            <p class="text-sm text-gray-600 mb-3">
                                                {{ Str::limit($voucher->deskripsi, 80) }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="border-t border-gray-200 pt-3 space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">Diskon:</span>
                                            <span class="text-lg font-bold text-green-600">
                                                Rp {{ number_format($voucher->jumlah_diskon, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">Min. Transaksi:</span>
                                            <span class="text-sm font-medium text-gray-800">
                                                Rp {{ number_format($voucher->minimal_transaksi, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">Berlaku hingga:</span>
                                            <span class="text-sm font-medium text-red-600">
                                                {{ $voucher->tanggal_berakhir->format('d M Y') }}
                                            </span>
                                        </div>
                                    </div>
                                    <hr class="border border-black/10 my-6">
                                    <a href="/"
                                        class="block w-full bg-[#7dacc4] hover:bg-[#B6771D] text-white text-center px-4 py-2 rounded-xl font-bold transition duration-300">
                                        <i class="fas fa-shopping-cart"></i> Gunakan Sekarang!
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- Section Histori Pemesanan --}}
    <section class="bg-[#fff] mb-24">
        <div class="container mx-auto py-12 px-6 md:px-0">
            <h1 class="text-lg md:text-xl lg:text-2xl text-center md:text-start font-bold mb-4 mt-12">Histori Pemesanan
                Saya</h1>

            @if ($pemesanan->count() > 0)
                <div class="bg-[#f0f0f0] rounded-2xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-[#B6771D] text-white">
                                <tr>
                                    <th class="px-6 py-4 text-left">Order ID</th>
                                    <th class="px-6 py-4 text-left">Tanggal Check-in</th>
                                    <th class="px-6 py-4 text-left">Tanggal Check-out</th>
                                    <th class="px-6 py-4 text-left">Total Harga</th>
                                    <th class="px-6 py-4 text-left">Voucher</th>
                                    <th class="px-6 py-4 text-left">Denda</th>
                                    <th class="px-6 py-4 text-left">Status Pembayaran</th>
                                    <th class="px-6 py-4 text-left">Status Pemesanan</th>
                                    <th class="px-6 py-4 text-left">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pemesanan as $pesan)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-6 py-4 font-mono text-sm">{{ $pesan->order_id }}</td>
                                        <td class="px-6 py-4">{{ $pesan->tgl_check_in->format('d M Y') }}</td>
                                        <td class="px-6 py-4">{{ $pesan->tgl_check_out->format('d M Y') }}</td>
                                        <td class="px-6 py-4">
                                            <div>
                                                @if ($pesan->diskon_voucher > 0)
                                                    <div class="text-xs text-gray-400 line-through">
                                                        Rp {{ number_format($pesan->total_harga, 0, ',', '.') }}
                                                    </div>
                                                    <div class="font-bold text-green-600">
                                                        Rp
                                                        {{ number_format($pesan->total_harga - $pesan->diskon_voucher, 0, ',', '.') }}
                                                    </div>
                                                @else
                                                    <div class="font-bold">
                                                        Rp {{ number_format($pesan->total_harga, 0, ',', '.') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- ✅ Kolom Voucher -->
                                        <td class="px-6 py-4">
                                            @if ($pesan->kode_voucher_used)
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded font-mono">
                                                        {{ $pesan->kode_voucher_used }}
                                                    </span>
                                                    <span class="text-xs text-green-600 font-medium mt-1">
                                                        -Rp {{ number_format($pesan->diskon_voucher, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>

                                        <!-- Kolom Denda -->
                                        <td class="px-6 py-4">
                                            @if ($pesan->denda > 0)
                                                <div>
                                                    <span class="text-red-600 font-bold">
                                                        Rp {{ number_format($pesan->denda, 0, ',', '.') }}
                                                    </span>
                                                    @if ($pesan->keterangan_denda)
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            {{ $pesan->keterangan_denda }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>

                                        <!-- Status Pembayaran -->
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-medium
                                            @if ($pesan->payment_status == 'paid') bg-green-100 text-green-800
                                            @elseif($pesan->payment_status == 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($pesan->payment_status) }}
                                            </span>
                                        </td>

                                        <!-- Status Pemesanan -->
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col gap-1">
                                                <span
                                                    class="px-3 py-1 rounded-full text-xs font-medium
                                                @if ($pesan->status_pemesanan == 'confirm') bg-blue-100 text-blue-800
                                                @elseif($pesan->status_pemesanan == 'checkin') bg-purple-100 text-purple-800
                                                @elseif($pesan->status_pemesanan == 'checkout') bg-gray-100 text-gray-800
                                                @elseif($pesan->status_pemesanan == 'cancel') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                    {{ ucfirst($pesan->status_pemesanan) }}
                                                </span>

                                                @if ($pesan->checkout_by_user)
                                                    <span
                                                        class="px-2 py-1 rounded-full text-xs bg-orange-100 text-orange-800">
                                                        Checkout oleh Anda
                                                    </span>
                                                @endif
                                                @if ($pesan->cancelled_by_user)
                                                    <span
                                                        class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">
                                                        Dibatalkan oleh Anda
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Action Buttons -->
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex flex-col gap-2">
                                                @if ($pesan->status_pemesanan === 'checkin')
                                                    <form
                                                        action="{{ route('user.selfCheckout', $pesan->id_pemesanan) }}"
                                                        method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                            onclick="return confirm('Apakah Anda yakin ingin checkout?')"
                                                            class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                                                            <i class="fas fa-sign-out-alt"></i> Checkout
                                                        </button>
                                                    </form>
                                                @elseif($pesan->status_pemesanan === 'confirm' && $pesan->payment_status === 'paid')
                                                    <form
                                                        action="{{ route('user.cancelBooking', $pesan->id_pemesanan) }}"
                                                        method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                            onclick="return confirm('Apakah Anda yakin ingin membatalkan pemesanan ini?')"
                                                            class="w-full bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition text-sm">
                                                            <i class="fas fa-times-circle"></i> Batalkan
                                                        </button>
                                                    </form>
                                                @elseif($pesan->status_pemesanan === 'pending')
                                                    <span class="text-yellow-600 text-sm font-medium">Menunggu
                                                        Pembayaran</span>
                                                @elseif($pesan->status_pemesanan === 'checkout')
                                                    <span class="text-green-600 text-sm font-medium">✓ Selesai</span>
                                                @elseif($pesan->status_pemesanan === 'cancel')
                                                    <span class="text-red-600 text-sm font-medium">✗ Dibatalkan</span>
                                                @endif

                                                @if ($pesan->payment_status === 'paid')
                                                    <a href="{{ route('booking.receipt', $pesan->order_id) }}"
                                                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition text-sm">
                                                        <i class="fas fa-receipt"></i> Lihat Struk
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-[#f9ffff] rounded-2xl border p-12 text-center">
                    <svg class="w-24 h-24 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Belum Ada Pemesanan</h3>
                    <p class="text-gray-500 mb-6">Anda belum melakukan pemesanan kamar</p>
                    <a href="/"
                        class="inline-block bg-[#B6771D] hover:bg-[#1581BF] text-white text-md md:text-lg px-6 py-2.5 rounded-2xl font-bold transition duration-300">
                        Pesan Kamar Sekarang
                    </a>
                </div>
            @endif
        </div>
    </section>


</x-app-layout>
