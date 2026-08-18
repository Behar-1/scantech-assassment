@props([
    'type' => 'button',
    'variant' => 'secondary',
    'size' => 'md',
])

@php
    $variants = [
        'primary' => 'border-brand-600 bg-brand-600 text-white shadow-sm hover:border-brand-700 hover:bg-brand-700 focus-visible:ring-brand-500',
        'secondary' => 'border-slate-300 bg-white text-slate-700 shadow-sm hover:bg-slate-50 focus-visible:ring-brand-500',
        'ghost' => 'border-transparent bg-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus-visible:ring-brand-500',
        'danger' => 'border-red-200 bg-red-50 text-red-700 shadow-sm hover:border-red-300 hover:bg-red-100 focus-visible:ring-red-500',
        'dark' => 'border-white/15 bg-white/10 text-white hover:bg-white/15 focus-visible:ring-white',
    ];

    $sizes = [
        'sm' => 'min-h-11 gap-1.5 rounded-lg px-3 py-2 text-xs',
        'md' => 'min-h-11 gap-2 rounded-xl px-4 py-2.5 text-sm',
    ];
@endphp

<button type="{{ $type }}"
        {{ $attributes->class([
            'inline-flex cursor-pointer items-center justify-center border font-semibold transition duration-200 active:opacity-80 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
            $variants[$variant] ?? $variants['secondary'],
            $sizes[$size] ?? $sizes['md'],
        ]) }}>
    {{ $slot }}
</button>
