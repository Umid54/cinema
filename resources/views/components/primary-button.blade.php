<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' =>
            'inline-flex items-center justify-center
             px-6 py-3 rounded-xl font-semibold
             text-cyan-300
             bg-cyan-500/10 border border-cyan-400/40
             hover:bg-cyan-500/20 hover:text-cyan-200
             focus:outline-none focus:ring-2 focus:ring-cyan-400/50
             transition duration-150'
    ]) }}
>
    {{ $slot }}
</button>
