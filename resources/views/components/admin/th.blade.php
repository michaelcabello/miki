{{-- resources/views/components/admin/th.blade.php --}}
@props([
    'field',          // Nombre de la columna en la BD (ej: 'name')
    'sortField',      // Variable de Livewire con el campo actual de ordenamiento
    'sortDirection',  // Variable de Livewire ('asc' o 'desc')
    'align' => 'left' // Alineación: left, center, right
])

@php
    $isActive = $sortField === $field;
    $alignmentClass = [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
    ][$align] ?? 'text-left';
@endphp

<th {{ $attributes->merge([
    'class' => "px-4 py-3 cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors $alignmentClass"
]) }}
    wire:click="sortBy('{{ $field }}')">

    <div class="flex items-center {{ $align === 'center' ? 'justify-center' : ($align === 'right' ? 'justify-end' : '') }} gap-2">
        {{-- El texto de la columna --}}
        <span>{{ $slot }}</span>

        {{-- El Icono Dinámico --}}
        <span class="inline-flex">
            @if($isActive)
                {{-- Icono activo (Azul Indigo del ERP) --}}
                <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-indigo-600 dark:text-indigo-400 animate-fade-in"></i>
            @else
                {{-- Icono neutral (gris tenue) --}}
                <i class="fa-solid fa-sort text-gray-400 opacity-30 group-hover:opacity-100 transition-opacity"></i>
            @endif
        </span>
    </div>
</th>
