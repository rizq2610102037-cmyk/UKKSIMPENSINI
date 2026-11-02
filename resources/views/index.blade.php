<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Hotel Bagustan</title>

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#fff] font-poppins h-[2000px]">

    {{-- Navbar --}}
    <x-navbar></x-navbar>

    {{-- Hero Section --}}
    <section style="background-image: url('/img/bg_jumbotron.png')" id="home"
        class="bg-center bg-no-repeat h-screen bg-cover flex items-center justify-center">
        <div class="px-4 mx-auto max-w-screen-xl text-center py-24 lg:py-56">
            <h1 class="mb-2 text-3xl font-bold text-white md:text-5xl lg:text-6xl">
                Pengalaman Menginap Terbaik <br class="hidden md:block">
                di Hotel Bagustan
            </h1>
            <p
                class="mb-8 font-medium md:font-light text-white/80 text-md md:text-lg lg:text-xl xl:text-2xl sm:px-16 lg:px-48">
                Nikmati kamar nyaman, fasilitas lengkap, <br class="hidden md:block"> dan lokasi strategis
                untuk liburan maupun perjalanan bisnis Anda.
            </p>

            {{-- jika sudah login, tampilkan modal pemilihan tipe kamar --}}
            <div class="flex flex-col sm:flex-row sm:justify-center sm:space-y-0">
                @auth
                    {{-- Jika sudah login, buka modal --}}
                    <button onclick="openModalTipeKamar()"
                        class="inline-flex justify-center bg-[#B6771D] hover:bg-[#FF9D00] text-white items-center shadow-sm py-2 md:py-2.5 px-4 md:px-5 sm:ms-4 text-md md:text-lg font-medium text-center rounded-xl md:rounded-2xl transition duration-300 border border-black/10">
                        Pesan Sekarang!
                    </button>
                @else
                    {{-- Jika belum login, arahkan ke halaman login --}}
                    <a href="{{ route('login') }}"
                        class="inline-flex justify-center bg-[#B6771D] hover:bg-[#FF9D00] text-white items-center shadow-sm py-2 md:py-2.5 px-4 md:px-5 sm:ms-4 text-md md:text-lg font-medium text-center rounded-xl md:rounded-2xl transition duration-300 border border-black/10">
                        Pesan Sekarang!
                    </a>
                @endauth
            </div>
        </div>
    </section>

    {{-- Modal Tipe Kamar --}}
    <x-modal-tipe-kamar :tipeKamar="$tipeKamar"></x-modal-tipe-kamar>

    {{-- About - Fasilitas --}}
    <section class="bg-[#f0f0f0] py-32" id="about">
        <div class="container mx-auto px-6">
            <div class="flex flex-col items-center justify-center gap-6 md:gap-10 mb-10">
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-semibold">
                    Hunian Nyaman, Lokasi Strategis, Harga Bersahabat
                </h2>
                <p
                    class="text-black/60 leading-relaxed font-light text-md md:text-lg lg:text-xl xl:text-3xl text-justify">
                    Hotel Bagus STAN adalah pilihan ideal bagi mahasiswa dan pekerja yang ingin tinggal di
                    lokasi strategis dengan akses mudah ke berbagai tempat penting.
                    Berlokasi hanya <span class="text-black/60 font-semibold">5 menit dari Kampus STAN</span>,
                    hunian ini menawarkan kenyamanan, keamanan, dan kemudahan mobilitas sehari-hari
                    untuk menunjang aktivitas Anda.
                </p>

                <a
                    class="inline-flex items-center gap-2 mt-6 text-md md:text-lg lg:text-xl font-semibold hover:underline">
                    Know More
                    <span class="text-lg">+</span>
                </a>
            </div>

            <!-- BY THE NUMBER / FASILITAS -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card -->
                <div
                    class="bg-white rounded-2xl p-6 border border-black/0 hover:border-black/40 hover:-translate-y-2 transition duration-300 cursor-pointer">
                    <div class="mb-4 text-3xl text-black">
                        <span class="iconify" data-icon="mdi:wifi"></span>
                    </div>
                    <h3 class="text-lg md:text-2xl font-bold mb-4">Free WiFi</h3>
                    <p class="text-black/60 text-md md:text-lg">
                        Akses internet cepat dan stabil untuk belajar maupun bekerja.
                    </p>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 border border-black/0 hover:border-black/40 hover:-translate-y-2 transition duration-300 cursor-pointer">
                    <div class="mb-4 text-3xl text-black">
                        <span class="iconify" data-icon="mdi:clock-outline"></span>
                    </div>
                    <h3 class="text-lg md:text-2xl font-bold mb-4">24 / 7</h3>
                    <p class="text-black/60 text-md md:text-lg">
                        Akses hunian selama 24 jam penuh tanpa batas waktu.
                    </p>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 border border-black/0 hover:border-black/40 hover:-translate-y-2 transition duration-300 cursor-pointer">
                    <div class="mb-4 text-3xl text-black">
                        <span class="iconify" data-icon="mdi:parking"></span>
                    </div>
                    <h3 class="text-lg md:text-2xl font-bold mb-4">Parkir Gratis</h3>
                    <p class="text-black/60 text-md md:text-lg">
                        Area parkir luas dan aman untuk motor maupun mobil.
                    </p>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 border border-black/0 hover:border-black/40 hover:-translate-y-2 transition duration-300 cursor-pointer">
                    <div class="mb-4 text-3xl text-black">
                        <span class="iconify" data-icon="mdi:locker"></span>
                    </div>
                    <h3 class="text-lg md:text-2xl font-bold mb-4">Kasur Besar</h3>
                    <p class="text-black/60 text-md md:text-lg">
                        Tempat tidur nyaman dengan ukuran besar.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Gallery Kamar - Hotel --}}
    <section class="py-24 bg-white">
        <div class="container mx-auto px-6 text-center mb-14">
            <h2 class="text-3xl md:text-4xl font-semibold">
                Pilihan Kamar Favorit
            </h2>
        </div>

        <div class="flex flex-col lg:flex-row justify-center items-center gap-6 px-6">

            <!-- Left card -->
            <div
                class="w-[340px] h-[460px] rounded-2xl overflow-hidden
            transition-all duration-500 ease-out hover:scale-105">
                <img src="/img/img_kamar1.png" class="w-full h-full object-cover" />
            </div>

            <!-- Center card (focus) -->
            <div
                class="w-[340px] h-[460px] md:w-[380px] md:h-[540px]
            rounded-2xl overflow-hidden relative shadow-xl
            transition-all duration-500 ease-out hover:scale-110">
                <img src="/img/img_kamar2.png" class="w-full h-full object-cover" />
            </div>

            <!-- Right card -->
            <div
                class="w-[340px] h-[460px] rounded-2xl overflow-hidden
            transition-all duration-500 ease-out hover:scale-105">
                <img src="/img/img_kamar3.png" class="w-full h-full object-cover" />
            </div>

        </div>
    </section>

    {{-- Keunggulan Hotel --}}
    <section class="py-24 bg-white">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-10 text-center">

                <!-- Item -->
                <div class="flex flex-col items-center gap-6">
                    <div
                        class="w-16 h-16 flex items-center justify-center rounded-2xl bg-yellow-100 border border-yellow-300">
                        <span class="iconify text-3xl text-yellow-700" data-icon="mdi:currency-usd"></span>
                    </div>
                    <p class="font-medium text-black/80 text-md md:text-lg">
                        Harga Terjangkau
                    </p>
                </div>

                <div class="flex flex-col items-center gap-6">
                    <div
                        class="w-16 h-16 flex items-center justify-center rounded-2xl bg-yellow-100 border border-yellow-300">
                        <span class="iconify text-3xl text-yellow-700" data-icon="mdi:cash-multiple"></span>
                    </div>
                    <p class="font-medium text-black/80 text-md md:text-lg">
                        Biaya Transparan
                    </p>
                </div>

                <div class="flex flex-col items-center gap-6">
                    <div
                        class="w-16 h-16 flex items-center justify-center rounded-2xl bg-yellow-100 border border-yellow-300">
                        <span class="iconify text-3xl text-yellow-700" data-icon="mdi:cellphone"></span>
                    </div>
                    <p class="font-medium text-black/80 text-md md:text-lg">
                        Pantau via Handphone
                    </p>
                </div>

                <div class="flex flex-col items-center gap-6">
                    <div
                        class="w-16 h-16 flex items-center justify-center rounded-2xl bg-yellow-100 border border-yellow-300">
                        <span class="iconify text-3xl text-yellow-700" data-icon="mdi:lock-outline"></span>
                    </div>
                    <p class="font-medium text-black/80 text-md md:text-lg">
                        Kunci Double
                    </p>
                </div>

                <div class="flex flex-col items-center gap-6">
                    <div
                        class="w-16 h-16 flex items-center justify-center rounded-2xl bg-yellow-100 border border-yellow-300">
                        <span class="iconify text-3xl text-yellow-700" data-icon="mdi:key-variant"></span>
                    </div>
                    <p class="font-medium text-black/80 text-md md:text-lg">
                        Kunci Elektrik
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- Location & Transportation --}}
    <section class="py-24 bg-[#f0f0f0f0]" id="nearby">
        <div class="container mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

            <!-- Map -->
            <div class="w-full h-[380px] bg-gray-300 rounded-2xl flex items-center justify-center text-gray-600">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387.4840245982344!2d106.73041931285275!3d-6.276421465036852!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f16e5cbfa71b%3A0x4f949579e4089712!2sKost%20Bagus%20STAN!5e0!3m2!1sid!2sid!4v1767877370254!5m2!1sid!2sid" class="w-full h-full shadow-md rounded-2xl" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>

            <!-- Info -->
            <div>
                <h2 class="text-2xl md:text-3xl font-bold mb-6 md:mt-8">
                    Location & Transportation
                </h2>

                <p class="font-medium text-md md:text-lg mb-4">
                    Nearby:
                </p>

                <div class="space-y-4">
                    <!-- Item -->
                    <div class="flex items-center justify-between bg-white px-5 py-4 rounded-xl shadow-sm">
                        <div class="flex items-center gap-3">
                            <span class="iconify text-blue-600 text-xl" data-icon="mdi:map-marker"></span>
                            <span class="font-medium text-gray-900">
                                Jakarta Selatan
                            </span>
                        </div>
                        <span class="text-sm text-gray-600 font-medium">
                            18 KM
                        </span>
                    </div>

                    <div class="flex items-center justify-between bg-white px-5 py-4 rounded-xl shadow-sm">
                        <div class="flex items-center gap-3">
                            <span class="iconify text-blue-600 text-xl" data-icon="mdi:map-marker"></span>
                            <span class="font-medium text-gray-900">
                                Stasiun Terdekat
                            </span>
                        </div>
                        <span class="text-sm text-gray-600 font-medium">
                            2 KM
                        </span>
                    </div>

                    <div class="flex items-center justify-between bg-white px-5 py-4 rounded-xl shadow-sm">
                        <div class="flex items-center gap-3">
                            <span class="iconify text-blue-600 text-xl" data-icon="mdi:map-marker"></span>
                            <span class="font-medium text-gray-900">
                                Kampus STAN
                            </span>
                        </div>
                        <span class="text-sm text-gray-600 font-medium">
                            5 Menit
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- Footer --}}
    <x-footer></x-footer>

    <!-- Iconify CDN -->
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    {{-- Script Internal --}}
    <script>
        window.addEventListener("scroll", function() {
            const navbarTop = document.getElementById("navbarTop");
            const navbarBottom = document.getElementById("navbarBottom");
            const navLink = document.getElementById("navLink");

            if (window.scrollY > 0) {
                // NAVBAR ATAS
                navbarTop.classList.add("bg-[#f5f5f5]", "text-black");
                navbarTop.classList.remove("bg-transparent", "text-white");

                // NAVBAR BAWAH
                navbarBottom.classList.add("bg-[#f5f5f5]", "text-black");
                navbarBottom.classList.remove("bg-transparent", "text-white");

                // LINK REGISTER
                if (navLink) {
                    navLink.classList.remove("text-white");
                    navLink.classList.add("text-[#0D99FF]");
                }
            } else {
                // NAVBAR ATAS
                navbarTop.classList.remove("bg-[#f5f5f5]", "text-black");
                navbarTop.classList.add("bg-transparent", "text-white");

                // NAVBAR BAWAH
                navbarBottom.classList.remove("bg-[#f5f5f5]", "text-black");
                navbarBottom.classList.add("bg-transparent", "text-white");

                // LINK REGISTER
                if (navLink) {
                    navLink.classList.remove("text-[#0D99FF]");
                    navLink.classList.add("text-white");
                }
            }
        });
    </script>

</body>

</html>
