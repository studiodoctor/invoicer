@props(['status', 'type' => 'invoice'])

@php
    $colors = [
        'gray' => 'bg-gray-100 text-gray-800',
        'blue' => 'bg-blue-100 text-blue-800',
        'purple' => 'bg-purple-100 text-purple-800',
        'green' => 'bg-green-100 text-green-800',
        'red' => 'bg-red-100 text-red-800',
        'orange' => 'bg-orange-100 text-orange-800',
        'yellow' => 'bg-yellow-100 text-yellow-800',
        'teal' => 'bg-teal-100 text-teal-800',
    ];
    
    $colorClass = $colors[$status->color()] ?? $colors['gray'];
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $colorClass }}">
    {{ $status->label() }}
</span>