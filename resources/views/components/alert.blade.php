@props([
    'type' => 'info',
    'dismissible' => true,
    'icon' => null
])

@php
$alertClasses = [
    'success' => 'alert-success',
    'error' => 'alert-danger',
    'danger' => 'alert-danger',
    'warning' => 'alert-warning',
    'info' => 'alert-info',
    'primary' => 'alert-primary',
    'secondary' => 'alert-secondary',
    'light' => 'alert-light',
    'dark' => 'alert-dark',
][$type] ?? 'alert-info';

$alertIcons = [
    'success' => 'bi-check-circle',
    'error' => 'bi-exclamation-triangle',
    'danger' => 'bi-exclamation-triangle',
    'warning' => 'bi-exclamation-triangle',
    'info' => 'bi-info-circle',
    'primary' => 'bi-info-circle',
    'secondary' => 'bi-info-circle',
    'light' => 'bi-info-circle',
    'dark' => 'bi-info-circle',
][$type] ?? 'bi-info-circle';

$iconToShow = $icon ?? $alertIcons;
@endphp

<div {{ $attributes->merge(['class' => "alert {$alertClasses}" . ($dismissible ? ' alert-dismissible fade show' : '')]) }}>
    @if($iconToShow)
        <i class="bi {{ $iconToShow }} me-2"></i>
    @endif
    
    {{ $slot }}
    
    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>