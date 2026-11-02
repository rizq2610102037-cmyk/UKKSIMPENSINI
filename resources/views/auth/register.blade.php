<x-guest-layout>
    <form method="POST" class="w-full max-w-xl bg-transparent ms-0 xl:ms-6 2xl:ms-20" action="{{ route('register') }}">
        <h1 class="mb-1 md:mb-2 text-xl md:text-2xl lg:text-3xl font-bold text-[#2a2a2a]">Hallo, <br>
            Selamat Datang</h1>
            <p class="mb-10 text-sm md:text-md italic font-light text-black/60">
                PT. Samudra Bagustan Jaya Abadi
            </p>
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="nama_user" :value="__('Nama Lengkap *')" />
            <x-text-input id="nama_user" class="block mt-1 w-full" type="text" name="nama_user" :value="old('nama_user')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('nama_user')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email *')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password *')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password *')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>
        <div class="mt-10">
            <button type="submit"
                class="block w-full text-center cursor-pointer py-2 px-4 bg-[#014690] hover:bg-[#0065d8] text-white font-semibold rounded-2xl text-md md:text-lg transition-all duration-300">
                Daftar
            </button>
        </div>
        <hr class="h-px my-6 bg-[#014690]">
        <div class="mt-0 text-center xl:text-start">
            <span class="text-black/60 text-md md:text-lg font-normal">Sudah Punya Akun?</span>
            <a class="text-black underline text-md md:text-lg" href="{{ route('login') }}">Login</a>
        </div>

        {{-- <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div> --}}
    </form>
</x-guest-layout>
