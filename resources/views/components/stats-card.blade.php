@props([
    'title',
    'value',
    'subtitle' => null,
    'icon' => 'bi-info-circle',
    'color' => 'primary',
    'trend' => null,
    'trendIcon' => null,
    'trendText' => null
])

@php
$borderColors = [
    'primary' => 'border-primary',
    'success' => 'border-success',
    'warning' => 'border-warning',
    'danger' => 'border-danger',
    'info' => 'border-info',
    'secondary' => 'border-secondary',
][$color] ?? 'border-primary';

$textColors = [
    'primary' => 'text-primary',
    'success' => 'text-success',
    'warning' => 'text-warning',
    'danger' => 'text-danger',
    'info' => 'text-info',
    'secondary' => 'text-secondary',
][$color] ?? 'text-primary';

$trendClasses = [
    'up' => 'text-success',
    'down' => 'text-danger',
    'neutral' => 'text-muted',
][$trend] ?? 'text-muted';

$trendIcons = [
    'up' => 'bi-arrow-up',
    'down' => 'bi-arrow-down',
    'neutral' => 'bi-dash',
][$trend] ?? ($trendIcon ?? '');
@endphp

<div {{ $attributes->merge(['class' => "card shadow-sm border-start border-4 {$borderColors}"]) }}>
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <h6 class="card-subtitle text-muted mb-1">{{ $title }}</h6>
            <h2 class="fw-bold mb-0">{{ $value }}</h2>
            @if($subtitle)
                <small class="text-muted">{{ $subtitle }}</small>
            @endif
        </div>
        <i class="bi {{ $icon }} fs-1 {{ $textColors }} opacity-25"></i>
    </div>
    
    @if($trendText)
        <div class="card-footer bg-transparent py-2">
            <small class="{{ $trendClasses }}">
                @if($trendIcons)
                    <i class="bi {{ $trendIcons }}"></i>
                @endif
                {{ $trendText }}
            </small>
        </div>
    @endif
</div>