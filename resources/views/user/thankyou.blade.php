<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terima Kasih - Hotel Bagustan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#f0f0f0] font-poppins">
    <div class="container mx-auto px-4 py-16 mt-12">
        <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg p-12 text-center">
            <div class="mb-6">
                <svg class="w-24 h-24 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-800 mb-4">Pembayaran Berhasil!</h1>
            <p class="text-gray-600 mb-2">Terima kasih telah melakukan pemesanan di Hotel Bagustan</p>
            
            @if(request('order_id'))
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <p class="text-sm text-gray-500 mb-1">Order ID</p>
                    <p class="text-lg font-mono font-bold text-gray-800">{{ request('order_id') }}</p>
                </div>
            @endif

            <div class="bg-[#FFF8E7] border-l-4 border-[#B6771D] p-4 mb-8">
                <p class="text-sm text-gray-700">
                    <i class="fas fa-info-circle text-[#B6771D]"></i>
                    <strong>Penting:</strong> Kami telah mengirimkan konfirmasi pemesanan ke email Anda. 
                    Silakan periksa inbox atau folder spam.
                </p>
            </div>

            <div class="flex flex-col gap-3 mb-6">
                @if(request('order_id'))
                    <a href="{{ route('booking.receipt', request('order_id')) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-xl font-medium transition duration-300 flex items-center justify-center gap-2">
                        <i class="fas fa-receipt"></i>
                        Lihat Struk Pembayaran
                    </a>
                @endif
                
                <a href="{{ route('user.dashboard') }}" 
                   class="bg-[#B6771D] hover:bg-[#FF9D00] text-white px-8 py-3 rounded-xl font-medium transition duration-300 flex items-center justify-center gap-2">
                    <i class="fas fa-history"></i>
                    Lihat Pesanan Saya
                </a>
                
                <a href="/"
                   class="border-2 border-[#B6771D] text-[#B6771D] hover:bg-[#B6771D] hover:text-white px-8 py-3 rounded-xl font-medium transition duration-300 flex items-center justify-center gap-2">
                    <i class="fas fa-home"></i>
                    Kembali ke Beranda
                </a>
            </div>

            <div class="text-sm text-gray-500">
                <p>Butuh bantuan? Hubungi kami di</p>
                <p class="font-medium text-[#B6771D]">
                    <i class="fas fa-phone"></i> +62 123 4567 890 | 
                    <i class="fas fa-envelope"></i> info@hotelbagustan.com
                </p>
            </div>
        </div>
    </div>
</body>
</html>