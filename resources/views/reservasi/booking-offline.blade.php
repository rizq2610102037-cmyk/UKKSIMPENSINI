<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Booking Walk-in - Hotel Bagustan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">
                <i class="fas fa-hotel"></i> Booking Walk-in Guest
            </h1>
            <div class="flex items-center gap-4">
                <a href="{{ route('reservasi.dashboard') }}"
                    class="bg-gray-500 hover:bg-gray-600 px-4 py-2 rounded transition">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <span><i class="fas fa-user-circle"></i> {{ auth()->user()->nama_user }}</span>
            </div>
        </div>
    </nav>

    <div class="container mx-auto py-8 px-4">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-5xl mx-auto">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-user-plus text-blue-600"></i> Form Booking Tamu Walk-in
                </h2>
                <p class="text-gray-600">Isi form di bawah untuk membuat booking offline untuk tamu yang datang langsung
                </p>
            </div>

            <form id="formBookingOffline">
                @csrf

                {{-- Data Tamu --}}
                <div class="mb-8 bg-blue-50 p-6 rounded-lg">
                    <h3 class="text-lg font-bold mb-4 text-blue-800">
                        <i class="fas fa-user"></i> Data Tamu
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Nama Lengkap Tamu *</label>
                            <input type="text" name="nama_tamu" id="namaTamu" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Email Tamu *</label>
                            <input type="email" name="email_tamu" id="emailTamu" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">No. Telepon Tamu *</label>
                            <input type="tel" name="no_telp_tamu" id="noTelpTamu" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="contoh: 08123456789">
                        </div>
                    </div>
                </div>

                {{-- Tanggal Menginap --}}
                <div class="mb-8 bg-green-50 p-6 rounded-lg">
                    <h3 class="text-lg font-bold mb-4 text-green-800">
                        <i class="fas fa-calendar-alt"></i> Tanggal Menginap
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Check-in *</label>
                            <input type="date" name="tgl_check_in" id="checkIn" required min="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Check-out *</label>
                            <input type="date" name="tgl_check_out" id="checkOut" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    <p id="infoMalam" class="text-sm text-gray-600 mt-2"></p>
                    <p id="dateWarning" class="text-sm text-red-600 mt-2 hidden">
                        ⚠️ Silakan pilih tanggal check-in dan check-out terlebih dahulu
                    </p>
                </div>

                {{-- Pilih Kamar --}}
                <div class="mb-8">
                    <h3 class="text-lg font-bold mb-4">
                        <i class="fas fa-bed"></i> Pilih Kamar Tersedia
                    </h3>

                    @foreach ($tipeKamarList as $tipe)
                        <div class="kamar-item mb-6 p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition"
                            data-tipe-id="{{ $tipe->id_tipe_kamar }}" data-harga="{{ $tipe->harga }}">
                            <div class="flex items-start gap-4">
                                <img src="{{ asset('storage/' . $tipe->gambar) }}" alt="{{ $tipe->nama_tipe }}"
                                    class="w-32 h-32 object-cover rounded-lg">

                                <div class="flex-1">
                                    <h4 class="nama-tipe font-bold text-lg mb-2">{{ $tipe->nama_tipe }}</h4>
                                    <p class="text-sm text-gray-600 mb-2">{{ $tipe->nama_tipe_kamar }}</p>
                                    <p class="text-blue-600 font-bold text-lg mb-2">
                                        Rp {{ number_format($tipe->harga, 0, ',', '.') }} / malam
                                    </p>

                                    <input type="hidden" class="harga-kamar" value="{{ $tipe->harga }}">

                                    <div class="loading-rooms mt-4 hidden">
                                        <div class="flex items-center gap-2">
                                            <svg class="animate-spin h-5 w-5 text-blue-600"
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
                                        <div class="grid grid-cols-3 md:grid-cols-5 lg:grid-cols-6 gap-2 room-list">
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">💡 Klik nomor kamar untuk memilih (bisa
                                            pilih lebih dari 1)</p>
                                    </div>

                                    <div class="no-rooms mt-4 hidden">
                                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                            <p class="text-sm text-red-600">❌ Tidak ada kamar tersedia untuk tanggal
                                                yang dipilih</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Metode Pembayaran --}}
                <div class="mb-8 bg-yellow-50 p-6 rounded-lg">
                    <h3 class="text-lg font-bold mb-4 text-yellow-800">
                        <i class="fas fa-money-bill-wave"></i> Metode Pembayaran
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="metode_pembayaran" value="cash" required
                                class="hidden peer">
                            <div
                                class="border-2 border-gray-300 rounded-lg p-4 text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition">
                                <i class="fas fa-money-bill-wave text-3xl mb-2 text-green-600"></i>
                                <p class="font-medium">Cash</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="metode_pembayaran" value="debit" required
                                class="hidden peer">
                            <div
                                class="border-2 border-gray-300 rounded-lg p-4 text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition">
                                <i class="fas fa-credit-card text-3xl mb-2 text-blue-600"></i>
                                <p class="font-medium">Debit</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="metode_pembayaran" value="credit" required
                                class="hidden peer">
                            <div
                                class="border-2 border-gray-300 rounded-lg p-4 text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition">
                                <i class="fas fa-credit-card text-3xl mb-2 text-purple-600"></i>
                                <p class="font-medium">Credit Card</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="metode_pembayaran" value="transfer" required
                                class="hidden peer">
                            <div
                                class="border-2 border-gray-300 rounded-lg p-4 text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition">
                                <i class="fas fa-university text-3xl mb-2 text-red-600"></i>
                                <p class="font-medium">Transfer</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="mb-8">
                    <label class="block text-sm font-medium mb-2">Catatan Tambahan (Opsional)</label>
                    <textarea name="catatan" id="catatan" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Tulis catatan khusus untuk booking ini (misal: permintaan khusus tamu)"></textarea>
                </div>

                {{-- Total Harga --}}
                <div class="mb-8 bg-blue-50 p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-4">Ringkasan Pembayaran</h3>
                    <div id="detailBiaya" class="mb-4 space-y-2 text-sm">
                        <p class="text-gray-400">Silakan pilih tanggal dan kamar</p>
                    </div>
                    <div class="border-t pt-4 mt-4">
                        <div class="flex justify-between items-center text-2xl font-bold text-blue-600">
                            <span>Total Bayar:</span>
                            <span id="totalBayar">Rp 0</span>
                        </div>
                    </div>
                </div>

                {{-- Informasi Petugas --}}
                <div class="mb-8 bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        <strong>Dicatat oleh:</strong> {{ auth()->user()->nama_user }} ({{ auth()->user()->email }})
                    </p>
                </div>

                {{-- Button Submit --}}
                <div class="flex justify-end gap-4">
                    <a href="{{ route('reservasi.dashboard') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-8 py-3 rounded-xl font-medium transition duration-300">
                        Batal
                    </a>
                    <button type="submit" id="btnSubmit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-medium transition duration-300 flex items-center gap-2">
                        <i class="fas fa-save"></i> Simpan Booking
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('checkIn').min = today;

        let inputCheckIn = document.getElementById('checkIn');
        let inputCheckOut = document.getElementById('checkOut');
        let infoMalam = document.getElementById('infoMalam');
        let dateWarning = document.getElementById('dateWarning');
        let detailBiaya = document.getElementById('detailBiaya');
        let totalBayar = document.getElementById('totalBayar');

        let selectedRooms = {};

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
                const response = await fetch('{{ route('reservasi.checkAvailability') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
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
                            'flex items-center justify-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 transition';
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
                                this.classList.add('border-blue-600', 'bg-blue-50', 'shadow-md');
                                this.classList.remove('border-gray-300');
                            } else {
                                this.classList.remove('border-blue-600', 'bg-blue-50', 'shadow-md');
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
                return;
            }

            let total = 0;
            let htmlDetail = '';

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

            if (htmlDetail) {
                detailBiaya.innerHTML = htmlDetail;
            } else {
                detailBiaya.innerHTML = '<p class="text-gray-400">Silakan pilih kamar</p>';
            }

            totalBayar.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

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

        document.getElementById('formBookingOffline').addEventListener('submit', async function(e) {
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

            const metodePembayaran = document.querySelector('input[name="metode_pembayaran"]:checked');
            if (!metodePembayaran) {
                alert('❌ Silakan pilih metode pembayaran');
                return;
            }

            const formData = new FormData(this);

            // Add selected rooms
            Object.values(selectedRooms).forEach(roomIds => {
                roomIds.forEach(roomId => {
                    formData.append('kamar_ids[]', roomId);
                });
            });

            const btnSubmit = document.getElementById('btnSubmit');
            const originalText = btnSubmit.innerHTML;
            btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            btnSubmit.disabled = true;

            try {
                const response = await fetch('{{ route('reservasi.storeOfflineBooking') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                const data = await response.json();

                // ✅ DEBUG: Log response
                console.log('Response from server:', data);

                if (data.error) {
                    alert('❌ Error: ' + data.error);
                    btnSubmit.innerHTML = originalText;
                    btnSubmit.disabled = false;
                    return;
                }

                if (data.success) {
                    console.log('Booking success! Pemesanan ID:', data.pemesanan_id);

                    // ✅ PERBAIKAN: Gunakan route name yang benar
                    const receiptUrl = '{{ route('reservasi.receipt', ':id') }}'.replace(':id', data
                        .pemesanan_id);
                    console.log('Redirecting to:', receiptUrl);

                    window.location.href = receiptUrl;
                } else {
                    alert('❌ Terjadi kesalahan yang tidak diketahui');
                    console.error('Unexpected response:', data);
                    btnSubmit.innerHTML = originalText;
                    btnSubmit.disabled = false;
                }
            } catch (error) {
                alert('❌ Terjadi kesalahan: ' + error.message);
                console.error('Error:', error);
                btnSubmit.innerHTML = originalText;
                btnSubmit.disabled = false;
            }
        });
    </script>
</body>

</html>
