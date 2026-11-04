@props(['title', 'value', 'subtitle' => null, 'accent' => 'slate'])

@php
    $borderAccent = match ($accent) {
        'emerald' => 'border-l-4 border-l-emerald-500',
        'amber' => 'border-l-4 border-l-amber-500',
        'red' => 'border-l-4 border-l-red-600',
        'blue' => 'border-l-4 border-l-blue-600',
        default => 'border-l-4 border-l-slate-800',
    };
@endphp

<div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-2xs {{ $borderAccent }}">
    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ $title }}</p>
    <p class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $value }}</p>
    @if ($subtitle)
        <p class="mt-1 text-xs text-slate-500">{{ $subtitle }}</p>
    @endif
</div>
