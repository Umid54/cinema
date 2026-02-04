@props(['disabled' => false])

<input
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge([
        'class' =>
            'w-full px-4 py-2 rounded-xl
             bg-black/40 border border-neutral-700
             text-white placeholder-slate-500
             focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50
             transition duration-150'
    ]) !!}
>
