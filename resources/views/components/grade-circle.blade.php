@props(['grade' => 'B', 'size' => 'md', 'label' => null])

@php
    $colorClasses = match ($grade) {
        'A' => 'border-emerald-600 text-emerald-700 bg-emerald-50/60',
        'B' => 'border-blue-600 text-blue-700 bg-blue-50/60',
        'C' => 'border-amber-500 text-amber-700 bg-amber-50/60',
        'D' => 'border-orange-600 text-orange-700 bg-orange-50/60',
        'E' => 'border-red-600 text-red-700 bg-red-50/60',
        default => 'border-blue-600 text-blue-700 bg-blue-50/60',
    };

    $dimensions = match ($size) {
        'sm' => 'w-9 h-9 text-base border-2',
        'lg' => 'w-20 h-20 text-4xl border-4 shadow-sm',
        'xl' => 'w-24 h-24 text-5xl border-4 shadow-md',
        default => 'w-12 h-12 text-xl border-[3px]',
    };
@endphp

<div class="inline-flex flex-col items-center gap-1">
    @if ($label)
        <span class="text-xs font-bold text-slate-700">{{ $label }}</span>
    @endif
    <div class="rounded-full flex items-center justify-center font-extrabold {{ $colorClasses }} {{ $dimensions }}">
        {{ $grade }}
    </div>
</div>
