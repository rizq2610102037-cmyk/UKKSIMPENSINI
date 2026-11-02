@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'text-md w-full px-3 py-2 border border-black/20 rounded-xl focus:outline-none focus:ring focus:ring-black/10 bg-transparent text-black/80']) }}>
