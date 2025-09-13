@props(['size' => 'md', 'color' => 'blue'])

@php
    $sizeClasses = [
        'xs' => 'w-4 h-4',
        'sm' => 'w-6 h-6',
        'md' => 'w-8 h-8',
        'lg' => 'w-12 h-12',
        'xl' => 'w-16 h-16'
    ];
    
    $colorClasses = [
        'blue' => 'text-blue-600',
        'green' => 'text-green-600',
        'yellow' => 'text-yellow-600',
        'red' => 'text-red-600',
        'purple' => 'text-purple-600',
        'gray' => 'text-gray-600'
    ];
    
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $colorClass = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<div class="flex items-center justify-center">
    <div class="animate-spin {{ $sizeClass }} {{ $colorClass }}">
        <i class="fas fa-spinner"></i>
    </div>
</div>
