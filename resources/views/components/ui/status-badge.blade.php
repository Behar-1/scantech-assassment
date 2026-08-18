@props(['status'])

@php
    $styles = match ($status) {
        'pending' => 'border-amber-200 bg-amber-50 text-amber-800 [&>span]:bg-amber-500',
        'assigned' => 'border-blue-200 bg-blue-50 text-blue-700 [&>span]:bg-blue-500',
        'driver_arriving' => 'border-violet-200 bg-violet-50 text-violet-700 [&>span]:bg-violet-500',
        'in_progress' => 'border-cyan-200 bg-cyan-50 text-cyan-800 [&>span]:bg-cyan-500',
        'completed' => 'border-emerald-200 bg-emerald-50 text-emerald-700 [&>span]:bg-emerald-500',
        'cancelled' => 'border-red-200 bg-red-50 text-red-700 [&>span]:bg-red-500',
        default => 'border-slate-200 bg-slate-50 text-slate-700 [&>span]:bg-slate-400',
    };

    $label = str($status)->replace('_', ' ')->title();
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border px-2.5 py-1 text-xs font-semibold', $styles]) }}>
    <span class="size-1.5 rounded-full" aria-hidden="true"></span>
    {{ $label }}
</span>
