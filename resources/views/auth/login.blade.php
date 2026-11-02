<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" class="w-full max-w-xl bg-transparent ms-0 xl:ms-6 2xl:ms-20" action="{{ route('login') }}">
        <h1 class="mb-1 md:mb-2 text-xl md:text-2xl lg:text-3xl font-bold text-[#2a2a2a]">Hallo, <br>
            Selamat Datang Kembali!</h1>
            <p class="mb-10 text-sm md:text-md italic font-light text-black/60">
                PT. Samudra Bagustan Jaya Abadi
            </p>
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-6">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div class="mt-10">
            <button type="submit"
                class="block w-full text-center cursor-pointer py-2 px-4 bg-[#014690] hover:bg-[#0065d8] text-white font-semibold rounded-2xl text-md md:text-lg transition-all duration-300">
                Login
            </button>
        </div>
        <hr class="h-px my-6 bg-[#014690]">
        <div class="mt-0 text-center xl:text-start">
            <span class="text-black/60 text-md md:text-lg font-normal">Belum Punya Akun?</span>
            <a class="text-black underline text-md md:text-lg" href="{{ route('register') }}">Daftar</a>
        </div>
    </form>
</x-guest-layout>
