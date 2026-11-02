<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Reservasi - Hotel Bagustan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">
                <i class="fas fa-hotel"></i> Hotel Bagustan - Reservasi
            </h1>
            <div class="flex items-center gap-4">
                {{-- Booking Offline --}}
                <a href="{{ route('reservasi.bookingOffline') }}"
                    class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded transition flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i> Booking Walk-in
                </a>
                <span><i class="fas fa-user-circle"></i> {{ auth()->user()->nama_user }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded transition">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mx-auto py-6 px-6 md:px-0">
        <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Tamu Aktif</p>
                        <h3 class="text-3xl font-bold text-blue-600">{{ $stats['total_tamu'] }}</h3>
                    </div>
                    <i class="fas fa-users text-4xl text-blue-600"></i>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Sudah Check-in</p>
                        <h3 class="text-3xl font-bold text-green-600">{{ $stats['checked_in'] }}</h3>
                    </div>
                    <i class="fas fa-check-circle text-4xl text-green-600"></i>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Menunggu Check-in</p>
                        <h3 class="text-3xl font-bold text-yellow-600">{{ $stats['menunggu_checkin'] }}</h3>
                    </div>
                    <i class="fas fa-clock text-4xl text-yellow-600"></i>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Check-out Hari Ini</p>
                        <h3 class="text-3xl font-bold text-purple-600">{{ $stats['checked_out_hari_ini'] }}</h3>
                    </div>
                    <i class="fas fa-door-open text-4xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">
                <i class="fas fa-clipboard-list"></i> Data Tamu yang Sudah Bayar
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pemesan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detail Kamar</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check-in</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check-out</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Bayar</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Denda</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($pemesananList as $index => $pemesanan)
                            @php
                                $bgClass = $pemesanan->checkout_by_user ? 'bg-orange-50' : '';
                            @endphp
                            <tr class="hover:bg-gray-50 {{ $bgClass }}">
                                <td class="px-4 py-3">{{ $pemesananList->firstItem() + $index }}</td>
                                <td class="px-4 py-3 font-mono text-sm">{{ $pemesanan->order_id }}</td>
                                <td class="px-4 py-3 font-medium">{{ $pemesanan->nama_pemesan }}</td>
                                <td class="px-4 py-3 text-sm">{{ $pemesanan->email }}</td>
                                <td class="px-4 py-3">
                                    <div class="text-sm">
                                        @foreach ($pemesanan->detailPemesanan as $detail)
                                            <div class="mb-1">
                                                <span class="font-medium">{{ $detail->tipeKamar->nama_tipe }}</span>
                                                <span class="text-gray-500">({{ $detail->jumlah_kamar }} kamar)</span>
                                                @if($detail->kamar)
                                                    <div class="text-xs text-blue-600">
                                                        <i class="fas fa-door-open"></i> No. {{ $detail->kamar->nomor_kamar }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ $pemesanan->tgl_check_in->format('d M Y') }}</td>
                                <td class="px-4 py-3">{{ $pemesanan->tgl_check_out->format('d M Y') }}</td>
                                <td class="px-4 py-3 font-bold text-blue-600">
                                    Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        @if ($pemesanan->status_pemesanan === 'confirm')
                                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock"></i> Confirmed
                                            </span>
                                        @elseif($pemesanan->status_pemesanan === 'checkin')
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle"></i> Checked In
                                            </span>
                                        @elseif($pemesanan->status_pemesanan === 'checkout')
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                                <i class="fas fa-door-open"></i> Checked Out
                                            </span>
                                        @endif

                                        @if ($pemesanan->checkout_by_user)
                                            <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">
                                                <i class="fas fa-exclamation-triangle"></i> Checkout oleh User
                                            </span>
                                        @endif

                                        @if ($pemesanan->cancelled_by_user)
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                                <i class="fas fa-ban"></i> Dibatalkan oleh User
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($pemesanan->denda > 0)
                                        <div>
                                            <span class="text-red-600 font-bold">Rp
                                                {{ number_format($pemesanan->denda, 0, ',', '.') }}</span>
                                            @if ($pemesanan->keterangan_denda)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ Str::limit($pemesanan->keterangan_denda, 30) }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-center gap-2 flex-wrap">
                                        @if ($pemesanan->status_pemesanan === 'confirm')
                                            <button onclick="checkIn({{ $pemesanan->id_pemesanan }})"
                                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition">
                                                <i class="fas fa-sign-in-alt"></i> Check-in
                                            </button>
                                        @elseif($pemesanan->status_pemesanan === 'checkin')
                                            <button onclick="checkOut({{ $pemesanan->id_pemesanan }})"
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition">
                                                <i class="fas fa-sign-out-alt"></i> Check-out
                                            </button>
                                        @endif

                                        @if (in_array($pemesanan->status_pemesanan, ['checkin', 'confirm', 'checkout']))
                                            <button
                                                onclick="openDendaModal({{ $pemesanan->id_pemesanan }}, '{{ $pemesanan->nama_pemesan }}', '{{ $pemesanan->order_id }}')"
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition">
                                                <i class="fas fa-exclamation-triangle"></i> Denda
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>Tidak ada data tamu yang sudah bayar</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $pemesananList->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Denda -->
    <div id="dendaModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">
                    <i class="fas fa-exclamation-triangle text-red-500"></i> Tambah Denda
                </h3>
                <button onclick="closeDendaModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <form id="dendaForm" onsubmit="submitDenda(event)">
                <input type="hidden" id="pemesananId" name="pemesanan_id">

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Order ID</label>
                    <input type="text" id="orderId"
                        class="w-full px-3 py-2 border rounded bg-gray-100 font-mono text-sm" readonly>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Nama Tamu</label>
                    <input type="text" id="namaTamu" class="w-full px-3 py-2 border rounded bg-gray-100"
                        readonly>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Pilih Jenis Pelanggaran *</label>
                    <select id="kategoriDenda" class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-red-500"
                        required onchange="updateHargaDenda()">
                        <option value="" data-harga="0">-- Pilih Kerusakan --</option>
                        <option value="Alat Tidur" data-harga="300000">Alat Tidur (Rp 300.000)</option>
                        <option value="Furnitur" data-harga="500000">Furnitur (Rp 500.000)</option>
                        <option value="Elektronik" data-harga="1000000">Elektronik (Rp 1.000.000)</option>
                        <option value="Alat Kamar Mandi" data-harga="700000">Alat Kamar Mandi (Rp 700.000)</option>
                        <option value="Checkout Dini" data-harga="200000">Checkout Dini oleh User (Rp 200.000)
                        </option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Total Denda Otomatis (Rp)</label>
                    <input type="text" id="displayHarga"
                        class="w-full px-3 py-2 border rounded bg-gray-50 font-bold text-red-600" readonly
                        value="Rp 0">
                    <input type="hidden" id="jumlahDenda" name="denda">
                    <input type="hidden" id="keteranganDenda" name="keterangan_denda">
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeDendaModal()"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 rounded transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 rounded transition">
                        <i class="fas fa-plus-circle"></i> Tambah Denda
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function updateHargaDenda() {
            const select = document.getElementById('kategoriDenda');
            const selectedOption = select.options[select.selectedIndex];
            const harga = selectedOption.getAttribute('data-harga');
            const kategoriTeks = selectedOption.value;

            if (harga && harga !== "0") {
                document.getElementById('jumlahDenda').value = harga;
                document.getElementById('keteranganDenda').value = "Denda kerusakan: " + kategoriTeks;
                document.getElementById('displayHarga').value = "Rp " + new Intl.NumberFormat('id-ID').format(harga);
            } else {
                document.getElementById('jumlahDenda').value = "";
                document.getElementById('keteranganDenda').value = "";
                document.getElementById('displayHarga').value = "Rp 0";
            }
        }

        function openDendaModal(id, nama, orderId) {
            document.getElementById('pemesananId').value = id;
            document.getElementById('namaTamu').value = nama;
            document.getElementById('orderId').value = orderId;
            document.getElementById('kategoriDenda').value = "";
            document.getElementById('jumlahDenda').value = "";
            document.getElementById('displayHarga').value = "Rp 0";
            document.getElementById('dendaModal').classList.remove('hidden');
        }

        function closeDendaModal() {
            document.getElementById('dendaModal').classList.add('hidden');
        }

        async function submitDenda(event) {
            event.preventDefault();
            const id = document.getElementById('pemesananId').value;
            const denda = document.getElementById('jumlahDenda').value;
            const keterangan = document.getElementById('keteranganDenda').value;

            if (!denda || denda === "") {
                alert('Silakan pilih kategori denda terlebih dahulu!');
                return;
            }

            if (!confirm(
                    `Konfirmasi: Tambahkan denda sebesar Rp ${parseInt(denda).toLocaleString('id-ID')} untuk kerusakan ${keterangan}?`
                    )) {
                return;
            }

            try {
                const response = await fetch(`/reservasi/add-denda/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        denda: denda,
                        keterangan_denda: keterangan
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert('✓ ' + result.message);
                    closeDendaModal();
                    location.reload();
                } else {
                    alert('✗ Gagal: ' + result.message);
                }
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan sistem saat menyimpan denda.');
            }
        }

        async function checkIn(id) {
            if (!confirm('Apakah Anda yakin ingin melakukan check-in untuk tamu ini?')) return;

            try {
                const response = await fetch(`/reservasi/check-in/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const result = await response.json();

                if (result.success) {
                    alert('✓ ' + result.message);
                    location.reload();
                } else {
                    alert('✗ ' + result.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan koneksi saat check-in.');
            }
        }

        async function checkOut(id) {
            if (!confirm('Apakah Anda yakin ingin melakukan check-out untuk tamu ini?')) return;

            try {
                const response = await fetch(`/reservasi/check-out/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const result = await response.json();

                if (result.success) {
                    alert('✓ ' + result.message);
                    location.reload();
                } else {
                    alert('✗ ' + result.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan koneksi saat check-out.');
            }
        }

        document.getElementById('dendaModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDendaModal();
            }
        });
    </script>
</body>

</html>