@props(['level' => 'Low', 'size' => 'md'])

@php
    $styles = match ($level) {
        'Low' => 'bg-emerald-50 text-emerald-800 border-emerald-200 ring-emerald-500/20',
        'Moderate' => 'bg-amber-50 text-amber-800 border-amber-200 ring-amber-500/20',
        'High' => 'bg-red-50 text-red-800 border-red-200 ring-red-500/20',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };

    $dotColor = match ($level) {
        'Low' => 'bg-emerald-500',
        'Moderate' => 'bg-amber-500',
        'High' => 'bg-red-600 animate-pulse',
        default => 'bg-slate-400',
    };

    $label = match ($level) {
        'Low' => 'Risiko Rendah (Low Risk)',
        'Moderate' => 'Risiko Sedang (Moderate)',
        'High' => 'Risiko Tinggi (High Risk)',
        default => $level,
    };

    $sizeClasses = match ($size) {
        'sm' => 'px-2.5 py-0.5 text-xs',
        'lg' => 'px-4 py-1.5 text-sm font-bold',
        default => 'px-3 py-1 text-xs font-semibold',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full border ring-1 {$styles} {$sizeClasses}"]) }}>
    <span class="w-2 h-2 rounded-full {{ $dotColor }}"></span>
    <span>{{ $label }}</span>
</span>
