<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Booking - Hotel Bagustan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-[#f0f0f0] font-poppins">

    <x-navbar></x-navbar>

    <div class="container mx-auto px-4 py-8 mt-20">
        <h1 class="text-3xl font-bold mb-8">Form Pemesanan Kamar</h1>

        <form id="formBooking" class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg p-8">
            @csrf

            {{-- Informasi Pemesan --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold mb-4 border-b pb-2">Informasi Pemesan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Nama Lengkap *</label>
                        <input type="text" name="nama_pemesan" required value="{{ auth()->user()->nama_user }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#B6771D] focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Email *</label>
                        <input type="email" name="email" required value="{{ auth()->user()->email }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#B6771D] focus:border-transparent">
                    </div>
                </div>
            </div>

            {{-- Tanggal Check-in & Check-out --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold mb-4 border-b pb-2">Tanggal Menginap</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Check-in *</label>
                        <input type="date" name="tgl_check_in" id="checkIn" required min="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#B6771D] focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Check-out *</label>
                        <input type="date" name="tgl_check_out" id="checkOut" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#B6771D] focus:border-transparent">
                    </div>
                </div>
                <p id="infoMalam" class="text-sm text-gray-600 mt-2"></p>
                <p id="dateWarning" class="text-sm text-red-600 mt-2 hidden">⚠️ Silakan pilih tanggal check-in dan
                    check-out terlebih dahulu untuk melihat kamar tersedia</p>
            </div>

            {{-- Detail Tipe Kamar & Room Selection --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold mb-4 border-b pb-2">Pilih Kamar yang Tersedia</h2>

                @foreach ($tipeKamarList as $tipe)
                    <div class="kamar-item mb-6 p-4 border border-gray-200 rounded-lg hover:border-[#B6771D] transition"
                        data-tipe-id="{{ $tipe->id_tipe_kamar }}" data-harga="{{ $tipe->harga }}">
                        <div class="flex items-start gap-4">
                            <img src="{{ asset('storage/' . $tipe->gambar) }}" alt="Gambar {{ $tipe->nama_tipe }}"
                                class="w-32 h-32 object-cover rounded-lg">

                            <div class="flex-1">
                                <h3 class="nama-tipe font-bold text-lg mb-2">{{ $tipe->nama_tipe }}</h3>
                                <p class="text-sm text-gray-600 mb-2">{{ $tipe->nama_tipe_kamar }}</p>
                                <p class="text-[#B6771D] font-bold text-lg mb-2">
                                    Rp {{ number_format($tipe->harga, 0, ',', '.') }} / malam
                                </p>

                                <input type="hidden" class="harga-kamar" value="{{ $tipe->harga }}">

                                <div class="loading-rooms mt-4 hidden">
                                    <div class="flex items-center gap-2">
                                        <svg class="animate-spin h-5 w-5 text-[#B6771D]"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <p class="text-sm text-gray-500">Memuat kamar tersedia...</p>
                                    </div>
                                </div>

                                <div class="room-selection mt-4 hidden">
                                    <label class="block text-sm font-medium mb-2">Pilih Kamar Tersedia</label>
                                    <div class="grid grid-cols-3 md:grid-cols-5 lg:grid-cols-6 gap-2 room-list"></div>
                                    <p class="text-xs text-gray-500 mt-2">💡 Klik nomor kamar untuk memilih (bisa pilih
                                        lebih dari 1)</p>
                                </div>

                                <div class="no-rooms mt-4 hidden">
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                        <p class="text-sm text-red-600">❌ Tidak ada kamar tersedia untuk tanggal yang
                                            dipilih</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Addon --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold mb-4 border-b pb-2">Fasilitas Tambahan</h2>
                <div class="space-y-3">
                    <label
                        class="flex items-center justify-between cursor-pointer p-4 border rounded-lg hover:bg-gray-50 transition">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" name="addon_sarapan" value="1"
                                class="addon-checkbox w-5 h-5 text-[#B6771D] border-gray-300 rounded focus:ring-[#B6771D]">
                            <span class="text-sm font-medium">Sarapan</span>
                        </div>
                        <span class="text-[#B6771D] font-bold">+ Rp 300.000</span>
                    </label>

                    <label
                        class="flex items-center justify-between cursor-pointer p-4 border rounded-lg hover:bg-gray-50 transition">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" name="addon_alat_tidur" value="1"
                                class="addon-checkbox w-5 h-5 text-[#B6771D] border-gray-300 rounded focus:ring-[#B6771D]">
                            <span class="text-sm font-medium">Alat Tidur Tambahan</span>
                        </div>
                        <span class="text-[#B6771D] font-bold">+ Rp 300.000</span>
                    </label>
                </div>
            </div>

            {{-- VOUCHER SECTION --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold mb-4 border-b pb-2 flex items-center gap-2">
                    <i class="fas fa-ticket-alt text-[#B6771D]"></i> Gunakan Voucher
                </h2>

                <div
                    class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-lg p-6 border-2 border-dashed border-[#B6771D]">
                    <div class="flex items-start gap-3 mb-4">
                        <i class="fas fa-gift text-[#B6771D] text-2xl mt-1"></i>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800 mb-1">Punya Voucher?</h3>
                            <p class="text-sm text-gray-600">Masukkan kode voucher atau pilih dari voucher yang Anda
                                miliki untuk mendapatkan diskon</p>
                        </div>
                    </div>

                    {{-- Input Kode Voucher --}}
                    <div class="flex gap-2 mb-4">
                        <input type="text" id="inputKodeVoucher"
                            placeholder="Masukkan kode voucher (contoh: WELCOME50K)"
                            class="flex-1 px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#B6771D] focus:border-transparent uppercase">
                        <button type="button" onclick="applyVoucher()"
                            class="bg-[#B6771D] hover:bg-[#FF9D00] text-white px-6 py-2 rounded-lg font-medium transition flex items-center gap-2">
                            <i class="fas fa-check"></i> Gunakan
                        </button>
                    </div>

                    {{-- Voucher yang Dimiliki User --}}
                    <div id="myVouchersSection" class="hidden">
                        <div class="flex justify-between items-center mb-3">
                            <p class="text-sm font-medium text-gray-700">Voucher Saya:</p>
                            <button type="button" onclick="toggleMyVouchers()"
                                class="text-sm text-[#B6771D] hover:underline flex items-center gap-1">
                                <i class="fas fa-chevron-down" id="voucherChevron"></i>
                                <span id="voucherToggleText">Lihat Voucher</span>
                            </button>
                        </div>

                        <div id="voucherList" class="hidden space-y-2">
                            {{-- Will be populated by JavaScript --}}
                        </div>
                    </div>

                    {{-- Applied Voucher Display --}}
                    <div id="appliedVoucher" class="hidden mt-4 bg-white rounded-lg p-4 border-2 border-green-500">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="bg-green-100 rounded-full p-2">
                                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-green-700" id="appliedVoucherName"></p>
                                    <p class="text-sm text-gray-600" id="appliedVoucherDiscount"></p>
                                </div>
                            </div>
                            <button type="button" onclick="removeVoucher()"
                                class="text-red-500 hover:text-red-700 transition">
                                <i class="fas fa-times-circle text-xl"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Voucher Error Message --}}
                    <div id="voucherError" class="hidden mt-4 bg-red-50 border border-red-200 rounded-lg p-3">
                        <p class="text-sm text-red-600 flex items-center gap-2">
                            <i class="fas fa-exclamation-circle"></i>
                            <span id="voucherErrorMessage"></span>
                        </p>
                    </div>
                </div>

                <input type="hidden" name="voucher_id" id="selectedVoucherId">
            </div>

            {{-- Total Harga --}}
            <div class="mb-8 bg-gray-50 p-6 rounded-lg">
                <h2 class="text-xl font-bold mb-4">Ringkasan Pembayaran</h2>
                <div id="detailBiaya" class="mb-4 space-y-2 text-sm">
                    <p class="text-gray-400">Silakan pilih tanggal dan kamar</p>
                </div>

                {{-- Diskon Voucher --}}
                <div id="diskonVoucherSection" class="hidden border-t pt-2 mb-2">
                    <div class="flex justify-between text-green-600 py-1">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-tag"></i> Diskon Voucher
                        </span>
                        <span class="font-bold" id="diskonVoucherAmount">- Rp 0</span>
                    </div>
                </div>

                <div class="border-t pt-4 mt-4">
                    <div class="flex justify-between items-center mb-2" id="totalSebelumDiskon"
                        style="display: none;">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="text-gray-600" id="subtotalAmount">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center text-2xl font-bold text-[#B6771D]">
                        <span>Total Bayar:</span>
                        <span id="totalBayar">Rp 0</span>
                    </div>
                </div>
            </div>

            {{-- Button Submit --}}
            <div class="flex justify-end gap-4">
                <a href="{{ url()->previous() }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-8 py-3 rounded-xl font-medium transition duration-300">
                    Kembali
                </a>
                <button type="submit" id="btnBayar"
                    class="bg-[#B6771D] hover:bg-[#FF9D00] text-white px-8 py-3 rounded-xl font-medium transition duration-300">
                    Bayar Sekarang
                </button>
            </div>
        </form>
    </div>

    <script>
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('checkIn').min = today;

        let inputCheckIn = document.getElementById('checkIn');
        let inputCheckOut = document.getElementById('checkOut');
        let infoMalam = document.getElementById('infoMalam');
        let dateWarning = document.getElementById('dateWarning');
        let detailBiaya = document.getElementById('detailBiaya');
        let totalBayar = document.getElementById('totalBayar');

        let selectedRooms = {};
        let appliedVoucherData = null; // Store applied voucher data
        let myVouchers = []; // Store user's vouchers

        // Load user vouchers on page load
        loadMyVouchers();

        function hitungMalam() {
            let tanggalMasuk = inputCheckIn.value;
            let tanggalKeluar = inputCheckOut.value;

            if (!tanggalMasuk || !tanggalKeluar) return 0;

            let masuk = new Date(tanggalMasuk);
            let keluar = new Date(tanggalKeluar);
            let selisih = keluar - masuk;
            let jumlahHari = selisih / (1000 * 60 * 60 * 24);

            return jumlahHari;
        }

        async function fetchAvailableRooms(tipeId, checkIn, checkOut, containerElement) {
            const loadingEl = containerElement.querySelector('.loading-rooms');
            const selectionEl = containerElement.querySelector('.room-selection');
            const noRoomsEl = containerElement.querySelector('.no-rooms');
            const roomListEl = containerElement.querySelector('.room-list');

            selectionEl.classList.add('hidden');
            noRoomsEl.classList.add('hidden');
            loadingEl.classList.remove('hidden');
            roomListEl.innerHTML = '';

            try {
                const response = await fetch('{{ route('booking.availableRooms') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id_tipe_kamar: tipeId,
                        tgl_check_in: checkIn,
                        tgl_check_out: checkOut
                    })
                });

                const data = await response.json();
                loadingEl.classList.add('hidden');

                if (data.success && data.rooms.length > 0) {
                    data.rooms.forEach(room => {
                        const roomButton = document.createElement('label');
                        roomButton.className =
                            'flex items-center justify-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-amber-50 transition';
                        roomButton.innerHTML = `
                            <input type="checkbox" 
                                   class="hidden room-checkbox" 
                                   data-tipe-id="${tipeId}"
                                   data-room-id="${room.id_kamar}"
                                   value="${room.id_kamar}">
                            <span class="text-sm font-medium">${room.nomor_kamar}</span>
                        `;

                        roomButton.addEventListener('click', function(e) {
                            e.preventDefault();
                            const checkbox = this.querySelector('input');
                            checkbox.checked = !checkbox.checked;

                            if (checkbox.checked) {
                                this.classList.add('border-[#B6771D]', 'bg-amber-50', 'shadow-md');
                                this.classList.remove('border-gray-300');
                            } else {
                                this.classList.remove('border-[#B6771D]', 'bg-amber-50', 'shadow-md');
                                this.classList.add('border-gray-300');
                            }

                            updateSelectedRooms();
                            hitungHarga();
                        });

                        roomListEl.appendChild(roomButton);
                    });

                    selectionEl.classList.remove('hidden');
                } else {
                    noRoomsEl.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error fetching rooms:', error);
                loadingEl.classList.add('hidden');
                noRoomsEl.classList.remove('hidden');
            }
        }

        function updateSelectedRooms() {
            selectedRooms = {};

            document.querySelectorAll('.room-checkbox:checked').forEach(checkbox => {
                const tipeId = checkbox.dataset.tipeId;
                const roomId = checkbox.dataset.roomId;

                if (!selectedRooms[tipeId]) {
                    selectedRooms[tipeId] = [];
                }
                selectedRooms[tipeId].push(roomId);
            });
        }

        function loadAllRooms() {
            const checkIn = inputCheckIn.value;
            const checkOut = inputCheckOut.value;

            if (!checkIn || !checkOut) {
                dateWarning.classList.remove('hidden');
                document.querySelectorAll('.kamar-item').forEach(item => {
                    item.querySelector('.room-selection').classList.add('hidden');
                    item.querySelector('.loading-rooms').classList.add('hidden');
                    item.querySelector('.no-rooms').classList.add('hidden');
                });
                return;
            }

            dateWarning.classList.add('hidden');

            document.querySelectorAll('.kamar-item').forEach(item => {
                const tipeId = item.dataset.tipeId;
                fetchAvailableRooms(tipeId, checkIn, checkOut, item);
            });
        }

        function hitungHarga() {
            let berapa_malam = hitungMalam();

            if (berapa_malam > 0) {
                infoMalam.textContent = `✅ ${berapa_malam} malam`;
                infoMalam.classList.remove('text-gray-600');
                infoMalam.classList.add('text-green-600');
            } else {
                infoMalam.textContent = '';
            }

            if (berapa_malam <= 0) {
                detailBiaya.innerHTML = '<p class="text-gray-400">Silakan pilih tanggal menginap</p>';
                totalBayar.textContent = 'Rp 0';
                document.getElementById('totalSebelumDiskon').style.display = 'none';
                document.getElementById('diskonVoucherSection').classList.add('hidden');
                return;
            }

            let total = 0;
            let htmlDetail = '';

            // Hitung harga kamar
            document.querySelectorAll('.kamar-item').forEach(kamarItem => {
                const tipeId = kamarItem.dataset.tipeId;
                const namaKamar = kamarItem.querySelector('.nama-tipe').textContent;
                const hargaKamar = parseInt(kamarItem.querySelector('.harga-kamar').value);

                const roomsSelected = selectedRooms[tipeId] || [];

                if (roomsSelected.length > 0) {
                    const subtotal = hargaKamar * roomsSelected.length * berapa_malam;
                    total += subtotal;

                    htmlDetail += '<div class="flex justify-between text-gray-700 py-1">';
                    htmlDetail +=
                        `<span>${namaKamar} (${roomsSelected.length} kamar × ${berapa_malam} malam)</span>`;
                    htmlDetail += `<span class="font-semibold">Rp ${subtotal.toLocaleString('id-ID')}</span>`;
                    htmlDetail += '</div>';
                }
            });

            // Hitung addon
            const addonSarapan = document.querySelector('input[name="addon_sarapan"]');
            const addonAlatTidur = document.querySelector('input[name="addon_alat_tidur"]');

            if (addonSarapan && addonSarapan.checked) {
                total += 300000;
                htmlDetail += '<div class="flex justify-between text-gray-700 py-1">';
                htmlDetail += '<span>Sarapan (Add-on)</span>';
                htmlDetail += '<span class="font-semibold">Rp 300.000</span>';
                htmlDetail += '</div>';
            }

            if (addonAlatTidur && addonAlatTidur.checked) {
                total += 300000;
                htmlDetail += '<div class="flex justify-between text-gray-700 py-1">';
                htmlDetail += '<span>Alat Tidur Tambahan (Add-on)</span>';
                htmlDetail += '<span class="font-semibold">Rp 300.000</span>';
                htmlDetail += '</div>';
            }

            if (htmlDetail) {
                detailBiaya.innerHTML = htmlDetail;
            } else {
                detailBiaya.innerHTML = '<p class="text-gray-400">Silakan pilih kamar</p>';
            }

            // Apply voucher discount
            let diskon = 0;
            if (appliedVoucherData) {
                // Re-validate voucher dengan total baru
                if (total >= appliedVoucherData.minimal_transaksi) {
                    diskon = appliedVoucherData.diskon;
                    document.getElementById('diskonVoucherSection').classList.remove('hidden');
                    document.getElementById('diskonVoucherAmount').textContent = `- Rp ${diskon.toLocaleString('id-ID')}`;
                    document.getElementById('totalSebelumDiskon').style.display = 'flex';
                    document.getElementById('subtotalAmount').textContent = `Rp ${total.toLocaleString('id-ID')}`;
                } else {
                    // Voucher tidak memenuhi minimal transaksi
                    showVoucherError(
                    `Minimal transaksi Rp ${appliedVoucherData.minimal_transaksi.toLocaleString('id-ID')}`);
                    removeVoucher();
                }
            } else {
                document.getElementById('diskonVoucherSection').classList.add('hidden');
                document.getElementById('totalSebelumDiskon').style.display = 'none';
            }

            const totalSetelahDiskon = total - diskon;
            totalBayar.textContent = 'Rp ' + totalSetelahDiskon.toLocaleString('id-ID');
        }

        // VOUCHER FUNCTIONS
        async function loadMyVouchers() {
            try {
                const response = await fetch('{{ route('booking.vouchers') }}', {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (data.success && data.vouchers.length > 0) {
                    myVouchers = data.vouchers;
                    document.getElementById('myVouchersSection').classList.remove('hidden');
                    renderVoucherList();
                }
            } catch (error) {
                console.error('Error loading vouchers:', error);
            }
        }

        function renderVoucherList() {
            const voucherList = document.getElementById('voucherList');
            voucherList.innerHTML = '';

            myVouchers.forEach(voucher => {
                const voucherCard = document.createElement('div');
                voucherCard.className =
                    'bg-white border-2 border-gray-200 rounded-lg p-4 hover:border-[#B6771D] transition cursor-pointer';
                voucherCard.onclick = () => selectVoucher(voucher);

                voucherCard.innerHTML = `
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="bg-[#B6771D] text-white text-xs font-bold px-2 py-1 rounded">${voucher.kode}</span>
                                <span class="text-green-600 font-bold">-Rp ${parseInt(voucher.diskon).toLocaleString('id-ID')}</span>
                            </div>
                            <p class="text-sm font-medium text-gray-800">${voucher.nama}</p>
                            <p class="text-xs text-gray-500 mt-1">Min. transaksi: Rp ${parseInt(voucher.minimal_transaksi).toLocaleString('id-ID')}</p>
                            <p class="text-xs text-gray-400">Berlaku hingga: ${voucher.tanggal_berakhir}</p>
                        </div>
                        <button type="button" class="text-[#B6771D] hover:text-[#FF9D00] font-medium text-sm">
                            Pakai
                        </button>
                    </div>
                `;

                voucherList.appendChild(voucherCard);
            });
        }

        function toggleMyVouchers() {
            const voucherList = document.getElementById('voucherList');
            const chevron = document.getElementById('voucherChevron');
            const toggleText = document.getElementById('voucherToggleText');

            if (voucherList.classList.contains('hidden')) {
                voucherList.classList.remove('hidden');
                chevron.classList.remove('fa-chevron-down');
                chevron.classList.add('fa-chevron-up');
                toggleText.textContent = 'Sembunyikan';
            } else {
                voucherList.classList.add('hidden');
                chevron.classList.remove('fa-chevron-up');
                chevron.classList.add('fa-chevron-down');
                toggleText.textContent = 'Lihat Voucher';
            }
        }

        function selectVoucher(voucher) {
            document.getElementById('inputKodeVoucher').value = voucher.kode;
            applyVoucher();
        }

        async function applyVoucher() {
            const kodeVoucher = document.getElementById('inputKodeVoucher').value.trim().toUpperCase();

            if (!kodeVoucher) {
                showVoucherError('Silakan masukkan kode voucher');
                return;
            }

            // Calculate current total
            let berapa_malam = hitungMalam();
            let total = 0;

            document.querySelectorAll('.kamar-item').forEach(kamarItem => {
                const tipeId = kamarItem.dataset.tipeId;
                const hargaKamar = parseInt(kamarItem.querySelector('.harga-kamar').value);
                const roomsSelected = selectedRooms[tipeId] || [];

                if (roomsSelected.length > 0) {
                    total += hargaKamar * roomsSelected.length * berapa_malam;
                }
            });

            const addonSarapan = document.querySelector('input[name="addon_sarapan"]');
            const addonAlatTidur = document.querySelector('input[name="addon_alat_tidur"]');

            if (addonSarapan && addonSarapan.checked) total += 300000;
            if (addonAlatTidur && addonAlatTidur.checked) total += 300000;

            if (total === 0) {
                showVoucherError('Silakan pilih kamar terlebih dahulu');
                return;
            }

            try {
                const response = await fetch('{{ route('booking.validateVoucher') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        kode_voucher: kodeVoucher,
                        total_transaksi: total
                    })
                });

                const data = await response.json();

                if (data.success) {
                    appliedVoucherData = {
                        id: data.voucher.id,
                        kode: data.voucher.kode,
                        nama: data.voucher.nama,
                        diskon: data.voucher.diskon,
                        minimal_transaksi: data.voucher.minimal_transaksi
                    };

                    document.getElementById('selectedVoucherId').value = data.voucher.id;
                    document.getElementById('appliedVoucherName').textContent = data.voucher.nama;
                    document.getElementById('appliedVoucherDiscount').textContent =
                        `Hemat Rp ${parseInt(data.voucher.diskon).toLocaleString('id-ID')}`;

                    document.getElementById('appliedVoucher').classList.remove('hidden');
                    document.getElementById('voucherError').classList.add('hidden');

                    hitungHarga();
                } else {
                    showVoucherError(data.message);
                }
            } catch (error) {
                console.error('Error applying voucher:', error);
                showVoucherError('Terjadi kesalahan saat validasi voucher');
            }
        }

        function removeVoucher() {
            appliedVoucherData = null;
            document.getElementById('selectedVoucherId').value = '';
            document.getElementById('inputKodeVoucher').value = '';
            document.getElementById('appliedVoucher').classList.add('hidden');
            document.getElementById('voucherError').classList.add('hidden');
            hitungHarga();
        }

        function showVoucherError(message) {
            document.getElementById('voucherErrorMessage').textContent = message;
            document.getElementById('voucherError').classList.remove('hidden');
            document.getElementById('appliedVoucher').classList.add('hidden');
        }

        // Event listeners
        inputCheckIn.addEventListener('change', function() {
            inputCheckOut.min = this.value;
            selectedRooms = {};
            loadAllRooms();
            hitungHarga();
        });

        inputCheckOut.addEventListener('change', function() {
            selectedRooms = {};
            loadAllRooms();
            hitungHarga();
        });

        document.querySelectorAll('.addon-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', hitungHarga);
        });

        // Form submit
        document.getElementById('formBooking').addEventListener('submit', function(e) {
            e.preventDefault();

            if (!inputCheckIn.value || !inputCheckOut.value) {
                alert('❌ Silakan isi tanggal check-in dan check-out');
                return;
            }

            const totalRooms = Object.values(selectedRooms).reduce((sum, rooms) => sum + rooms.length, 0);

            if (totalRooms === 0) {
                alert('❌ Silakan pilih minimal 1 kamar');
                return;
            }

            const formData = new FormData(this);

            formData.delete('kamar_ids[]');
            Object.values(selectedRooms).forEach(roomIds => {
                roomIds.forEach(roomId => {
                    formData.append('kamar_ids[]', roomId);
                });
            });

            const btnBayar = document.getElementById('btnBayar');
            const originalText = btnBayar.textContent;
            btnBayar.textContent = 'Memproses...';
            btnBayar.disabled = true;

            fetch('{{ route('booking.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert('❌ Error: ' + data.error);
                        btnBayar.textContent = originalText;
                        btnBayar.disabled = false;
                        return;
                    }

                    if (data.snap_token) {
                        snap.pay(data.snap_token, {
                            onSuccess: function(result) {
                                checkPaymentStatusAndRedirect(data.order_id);
                            },
                            onPending: function(result) {
                                alert('⏳ Menunggu pembayaran');
                                window.location.href = '{{ route('user.dashboard') }}';
                            },
                            onError: function(result) {
                                alert('❌ Pembayaran gagal');
                                btnBayar.textContent = originalText;
                                btnBayar.disabled = false;
                            },
                            onClose: function() {
                                checkPaymentStatusOnClose(data.order_id, btnBayar, originalText);
                            }
                        });
                    }
                })
                .catch(error => {
                    alert('❌ Terjadi kesalahan: ' + error.message);
                    console.error('Error:', error);
                    btnBayar.textContent = originalText;
                    btnBayar.disabled = false;
                });
        });

        function checkPaymentStatusAndRedirect(orderId) {
            fetch('{{ route('booking.checkPayment') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        order_id: orderId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.status === 'paid') {
                        window.location.href = '{{ route('thankyou') }}?order_id=' + orderId;
                    } else {
                        alert('✅ Pembayaran berhasil! Menunggu konfirmasi...');
                        window.location.href = '{{ route('user.dashboard') }}';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.location.href = '{{ route('thankyou') }}?order_id=' + orderId;
                });
        }

        function checkPaymentStatusOnClose(orderId, btnBayar, originalText) {
            fetch('{{ route('booking.checkPayment') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        order_id: orderId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.status === 'paid') {
                        alert('✅ Pembayaran berhasil!');
                        window.location.href = '{{ route('thankyou') }}?order_id=' + orderId;
                    } else {
                        alert('⚠️ Pembayaran belum selesai atau Anda menutup popup');
                        btnBayar.textContent = originalText;
                        btnBayar.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('⚠️ Anda menutup popup pembayaran');
                    btnBayar.textContent = originalText;
                    btnBayar.disabled = false;
                });
        }
    </script>

</body>

</html>
