<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-poppins text-[#2a2a2a]">
    <section class="h-screen grid grid-cols-1 xl:grid-cols-2 px-6 md:px-0">
        <!-- Left: Login Form -->
        <div class="flex items-center justify-center xl:justify-start bg-[var(--bg-primary)] p-4 xl:p-10">
            {{ $slot }}
        </div>

        <!-- Right: Carousel -->
        <div class="hidden xl:block relative w-full h-full overflow-hidden">
            <div class="relative h-full w-full">
                <div class="absolute inset-0 transition-opacity duration-1000 ease-in-out">
                    <img src="img/img_form.png" class="object-cover w-full h-full" alt="Image 1">
                </div>
            </div>
        </div>
    </section>
</body>

</html>
