@props(['value'])

<label {{ $attributes->merge(['class' => 'block mb-1 text-md md:text-lg text-[#1a1a1a]']) }}>
    {{ $value ?? $slot }}
</label>
