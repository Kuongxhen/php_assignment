@props(['actions' => [], 'size' => 'sm'])

@php
    $sizeClasses = [
        'xs' => 'px-2 py-1 text-xs',
        'sm' => 'px-3 py-1 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base'
    ];
    
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['sm'];
@endphp

<div class="flex space-x-2">
    @foreach($actions as $action)
        @if($action['type'] === 'view')
            <a href="{{ $action['url'] }}" class="btn-info inline-flex items-center {{ $sizeClass }} border border-transparent font-medium rounded-lg text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 hover:scale-105">
                <i class="fas fa-eye mr-1"></i>
                View
            </a>
        @elseif($action['type'] === 'edit')
            <a href="{{ $action['url'] }}" class="btn-warning inline-flex items-center {{ $sizeClass }} border border-transparent font-medium rounded-lg text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-200 hover:scale-105">
                <i class="fas fa-edit mr-1"></i>
                Edit
            </a>
        @elseif($action['type'] === 'delete')
            <form action="{{ $action['url'] }}" method="POST" class="inline" onsubmit="return confirm('{{ $action['confirm'] ?? 'Are you sure?' }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger inline-flex items-center {{ $sizeClass }} border border-transparent font-medium rounded-lg text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 hover:scale-105">
                    <i class="fas fa-trash mr-1"></i>
                    {{ $action['label'] ?? 'Delete' }}
                </button>
            </form>
        @elseif($action['type'] === 'custom')
            <a href="{{ $action['url'] }}" class="{{ $action['class'] }} inline-flex items-center {{ $sizeClass }} border border-transparent font-medium rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 hover:scale-105">
                @if(isset($action['icon']))
                    <i class="{{ $action['icon'] }} mr-1"></i>
                @endif
                {{ $action['label'] }}
            </a>
        @endif
    @endforeach
</div>
