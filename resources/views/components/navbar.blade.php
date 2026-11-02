<nav class="fixed top-0 left-0 right-0 z-50 text-white" id="navbarTop">
    <div class="flex flex-wrap justify-between items-center mx-auto px-6 max-w-screen-md lg:max-w-screen-lg xl:max-w-screen-xl 2xl:max-w-screen-2xl transition duration-300 p-4"
        id="navbarContent">
        {{-- Logo --}}
        <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
            <span class="self-center text-xl md:text-2xl font-bold">
                Hotel Bagustan
            </span>
        </a>

        {{-- Auth Section --}}
        <div class="flex items-center space-x-5 rtl:space-x-reverse text-sm">
            @if (Route::has('login'))
                @auth
                    @if (Auth::user()->role === 'admin')
                        <a href="/admin" class="text-white hover:underline font-medium" id="navLink">
                            Dashboard
                        </a>
                    @elseif (Auth::user()->role === 'reservasi')
                        <a href="{{ route('reservasi.dashboard') }}" class="text-white hover:underline font-medium" id="navLink">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('user.dashboard') }}" class="text-white hover:underline font-medium" id="navLink">
                            Dashboard
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="hover:underline font-medium md:text-md">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="text-white hover:underline font-bold md:text-md" id="navLink">
                        Register
                    </a>
                @endauth
            @endif
        </div>
    </div>
</nav>

{{-- Menu bawah --}}
<nav class="border-t border-black/50 text-white fixed top-14 md:top-16 left-0 right-0 z-40" id="navbarBottom">
    <div class="px-6 max-w-screen-md lg:max-w-screen-lg xl:max-w-screen-xl 2xl:max-w-screen-2xl py-3 mx-auto">
        <ul class="flex flex-row font-medium space-x-8 rtl:space-x-reverse text-sm md:text-md">
            <li>
                <a href="#home" class="hover:underline">
                    Home
                </a>
            </li>
            <li>
                <a href="#nearby" class="hover:underline">
                    Nearby
                </a>
            </li>
            <li>
                <a href={{ route('register') }} class="hover:underline">
                    Pesan Hotel
                </a>
            </li>
        </ul>
    </div>
</nav>
