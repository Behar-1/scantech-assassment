@props([
    'label',
    'value',
    'tone' => 'blue',
])

@php
    $tones = [
        'amber' => 'bg-amber-50 text-amber-700 ring-amber-100',
        'blue' => 'bg-blue-50 text-blue-700 ring-blue-100',
        'cyan' => 'bg-cyan-50 text-cyan-700 ring-cyan-100',
        'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
    ];
@endphp

<div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">{{ $label }}</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">{{ $value }}</p>
        </div>
        <div @class(['grid size-11 shrink-0 place-items-center rounded-xl ring-1', $tones[$tone] ?? $tones['blue']])>
            {{ $icon }}
        </div>
    </div>
</div>
