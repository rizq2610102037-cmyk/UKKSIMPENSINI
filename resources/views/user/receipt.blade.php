<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - {{ $pemesanan->order_id }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; }
        }
    </style>
</head>
<body class="bg-gray-100 font-poppins">
    <div class="container mx-auto px-4 py-8">
        <!-- Header with Action Buttons -->
        <div class="max-w-4xl mx-auto mb-6 no-print">
            <div class="flex justify-between items-center">
                <a href="{{ route('user.dashboard') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                </a>
                <div class="flex gap-3">
                    <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <button onclick="downloadPDF()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                        <i class="fas fa-download"></i> Download PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Receipt Container -->
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg p-8" id="receipt">
            <!-- Hotel Header -->
            <div class="text-center border-b-2 border-gray-200 pb-6 mb-6">
                <h1 class="text-3xl font-bold text-[#B6771D] mb-2">Hotel Bagustan</h1>
                <p class="text-gray-600">Jl. Contoh No. 123, Jakarta</p>
                <p class="text-gray-600">Telp: +62 123 4567 890 | Email: info@hotelbagustan.com</p>
            </div>

            <!-- Receipt Title -->
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">STRUK PEMBAYARAN</h2>
                <div class="inline-block bg-green-100 text-green-800 px-4 py-2 rounded-full">
                    <i class="fas fa-check-circle"></i> LUNAS
                </div>
            </div>

            <!-- Order Information -->
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div>
                    <h3 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-file-invoice text-[#B6771D]"></i> Informasi Pemesanan
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Order ID:</span>
                            <span class="font-mono font-bold">{{ $pemesanan->order_id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tanggal Pemesanan:</span>
                            <span class="font-medium">{{ $pemesanan->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status Pembayaran:</span>
                            <span class="font-medium text-green-600">
                                <i class="fas fa-check"></i> {{ ucfirst($pemesanan->payment_status) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status Pemesanan:</span>
                            <span class="font-medium text-blue-600">{{ ucfirst($pemesanan->status_pemesanan) }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-user text-[#B6771D]"></i> Informasi Tamu
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nama Pemesan:</span>
                            <span class="font-medium">{{ $pemesanan->nama_pemesan }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-medium">{{ $pemesanan->email }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stay Information -->
            <div class="bg-blue-50 rounded-xl p-6 mb-8">
                <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-[#B6771D]"></i> Periode Menginap
                </h3>
                <div class="grid md:grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Check-in</p>
                        <p class="text-lg font-bold text-gray-800">{{ $pemesanan->tgl_check_in->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500">14:00 WIB</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Check-out</p>
                        <p class="text-lg font-bold text-gray-800">{{ $pemesanan->tgl_check_out->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500">12:00 WIB</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Lama Menginap</p>
                        <p class="text-lg font-bold text-[#B6771D]">{{ $jumlahMalam }} Malam</p>
                    </div>
                </div>
            </div>

            <!-- Room Details -->
            <div class="mb-8">
                <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <i class="fas fa-bed text-[#B6771D]"></i> Detail Kamar
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe Kamar</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Kamar</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga/Malam</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Malam</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($rincianKamar as $item)
                                <tr>
                                    <td class="px-4 py-3 font-medium">{{ $item['tipe'] }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        @if(count($item['nomor_kamar']) > 0)
                                            {{ implode(', ', $item['nomor_kamar']) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">{{ $item['jumlah_kamar'] }}</td>
                                    <td class="px-4 py-3 text-right">Rp {{ number_format($item['harga_per_malam'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center">{{ $item['jumlah_malam'] }}</td>
                                    <td class="px-4 py-3 text-right font-bold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add-ons (if any) -->
            @if(count($addOnList) > 0)
                <div class="mb-8">
                    <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-plus-circle text-[#B6771D]"></i> Layanan Tambahan
                    </h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        @foreach($addOnList as $addon)
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-700">{{ $addon['nama'] }}</span>
                                <span class="font-medium">Rp {{ number_format($addon['harga'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Voucher Discount (if any) -->
            @if($pemesanan->diskon_voucher > 0)
                <div class="mb-8">
                    <h3 class="font-semibold text-green-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-ticket-alt"></i> Diskon Voucher
                    </h3>
                    <div class="bg-green-50 rounded-lg p-4 border-2 border-green-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium text-green-800 flex items-center gap-2">
                                    <i class="fas fa-tag"></i> {{ $pemesanan->kode_voucher_used }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">Selamat! Anda hemat dengan voucher</p>
                            </div>
                            <span class="font-bold text-green-600 text-lg">- Rp {{ number_format($pemesanan->diskon_voucher, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Denda (if any) -->
            @if($pemesanan->denda > 0)
                <div class="mb-8">
                    <h3 class="font-semibold text-red-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i> Denda
                    </h3>
                    <div class="bg-red-50 rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium text-red-800">{{ $pemesanan->keterangan_denda }}</p>
                            </div>
                            <span class="font-bold text-red-600">Rp {{ number_format($pemesanan->denda, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Total Summary -->
            <div class="border-t-2 border-gray-200 pt-6">
                <div class="space-y-3">
                    <div class="flex justify-between text-gray-700">
                        <span>Subtotal Kamar:</span>
                        <span class="font-medium">Rp {{ number_format($totalKamar, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($biayaAddOn > 0)
                        <div class="flex justify-between text-gray-700">
                            <span>Layanan Tambahan:</span>
                            <span class="font-medium">Rp {{ number_format($biayaAddOn, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    @if($pemesanan->denda > 0)
                        <div class="flex justify-between text-red-700">
                            <span>Denda:</span>
                            <span class="font-medium">Rp {{ number_format($pemesanan->denda, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-xl font-bold text-gray-800 pt-3 border-t-2 border-gray-300">
                        <span>TOTAL PEMBAYARAN:</span>
                        <span class="text-[#B6771D]">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer Notes -->
            <div class="mt-8 pt-6 border-t border-gray-200 text-center text-sm text-gray-600">
                <p class="mb-2">Terima kasih telah memilih Hotel Bagustan</p>
                <p>Struk ini dicetak pada {{ now()->format('d M Y, H:i') }} WIB</p>
                <p class="mt-4 text-xs">Simpan struk ini sebagai bukti pembayaran yang sah</p>
            </div>
        </div>
    </div>

    <script>
        function downloadPDF() {
            window.print();
        }
    </script>
</body>
</html>