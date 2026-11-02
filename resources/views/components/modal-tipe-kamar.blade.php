@auth
    <div id="modalTipeKamar" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">Pilih Tipe Kamar</h2>
                    <button onclick="closeModalTipeKamar()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <form id="formPilihTipeKamar" action="{{ route('booking.create') }}" method="GET">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($tipeKamar as $tipe)
                            <div
                                class="border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition duration-300">
                                <img src="{{ asset('storage/' . $tipe->gambar) }}" alt="{{ $tipe->nama_tipe }}"
                                    class="w-full h-48 object-cover">
                                <div class="p-4">
                                    <h3 class="font-bold text-lg mb-2">{{ $tipe->nama_tipe }}</h3>
                                    <p class="text-sm text-gray-600 mb-3">{{ $tipe->nama_tipe_kamar }}</p>
                                    <p class="text-[#B6771D] font-bold text-xl mb-4">
                                        Rp {{ number_format($tipe->harga, 0, ',', '.') }} / malam
                                    </p>
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="checkbox" name="tipe_kamar[]" value="{{ $tipe->id_tipe_kamar }}"
                                            class="w-5 h-5 text-[#B6771D] border-gray-300 rounded focus:ring-[#B6771D]">
                                        <span class="text-sm font-medium">Pilih tipe kamar ini</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 flex justify-end space-x-4">
                        <button type="button" onclick="closeModalTipeKamar()"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-xl font-medium transition duration-300">
                            Batal
                        </button>
                        <button type="submit"
                            class="bg-[#B6771D] hover:bg-[#FF9D00] text-white px-8 py-3 rounded-xl font-medium transition duration-300">
                            Lanjutkan Pemesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function openModalTipeKamar() {
            document.getElementById('modalTipeKamar').classList.remove('hidden');
            document.getElementById('modalTipeKamar').classList.add('flex');
            document.body.style.overflow = 'hidden'; // Prevent background scroll
        }

        function closeModalTipeKamar() {
            document.getElementById('modalTipeKamar').classList.add('hidden');
            document.getElementById('modalTipeKamar').classList.remove('flex');
            document.body.style.overflow = 'auto'; // Restore scroll
        }

        // Close modal when clicking outside
        document.getElementById('modalTipeKamar').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModalTipeKamar();
            }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModalTipeKamar();
            }
        });

        // Validasi: minimal 1 tipe kamar harus dipilih
        document.getElementById('formPilihTipeKamar').addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('input[name="tipe_kamar[]"]:checked');
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Silakan pilih minimal 1 tipe kamar');
            }
        });
    </script>
@endauth
