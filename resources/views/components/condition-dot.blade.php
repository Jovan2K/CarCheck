@props(['condition' => 'Good', 'showText' => true])

@php
    $dotClass = match ($condition) {
        'Good' => 'bg-emerald-600 ring-emerald-100',
        'Warning' => 'bg-amber-400 ring-amber-100',
        'Critical' => 'bg-red-600 ring-red-100',
        default => 'bg-slate-400 ring-slate-100',
    };

    $textClass = match ($condition) {
        'Good' => 'text-emerald-700',
        'Warning' => 'text-amber-700',
        'Critical' => 'text-red-700 font-bold',
        default => 'text-slate-600',
    };

    $text = match ($condition) {
        'Good' => 'OK / Baik',
        'Warning' => 'Perlu Diperhatikan',
        'Critical' => 'Rusak Parah (Critical)',
        default => $condition,
    };
@endphp

<span class="inline-flex items-center gap-1.5">
    <span class="w-3 h-3 rounded-full ring-4 {{ $dotClass }} shrink-0"></span>
    @if ($showText)
        <span class="text-xs font-semibold {{ $textClass }}">{{ $text }}</span>
    @endif
</span>
