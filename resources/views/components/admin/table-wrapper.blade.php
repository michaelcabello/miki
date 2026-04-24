@props([
    'title',
    'newRoute',
    'canCreate',
    'selectedCount'  => 0,
    'showTrashed'    => false,
    'canDelete'      => null,
    'canRestore'     => null,
    'canExportExcel' => null,
    'canExportPdf'   => null,
    'canImport'      => null,
    'exportRoute'    => null,
    'pdfRoute'       => null,
    'search'         => '',
    'visibleColumns' => [],
])

<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col md:flex-row justify-between items-center gap-4 border-b dark:border-gray-700">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $title }}</h1>
        </div>

        <div class="flex flex-wrap items-center gap-2">

            {{-- Botones masivos --}}
            @if ($selectedCount > 0)
                @if ($showTrashed && $canRestore)
                    @can($canRestore)
                        <button type="button"
                            onclick="confirmBulkAction('confirmRestoreSelected', '¿Restaurar seleccionados?', 'Los registros volverán a la lista activa.')"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition shadow-md">
                            <i class="fa-solid fa-trash-arrow-up mr-2"></i> Restaurar ({{ $selectedCount }})
                        </button>
                    @endcan
                @elseif (!$showTrashed && $canDelete)
                    @can($canDelete)
                        <button type="button"
                            onclick="confirmBulkAction('confirmDeleteSelected', '¿Eliminar seleccionados?', 'Se moverán a la papelera.')"
                            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition shadow-md animate-pulse">
                            <i class="fa-solid fa-trash-can mr-2"></i> Eliminar ({{ $selectedCount }})
                        </button>
                    @endcan
                @endif
            @endif

            {{-- Botón Nuevo --}}
            @can($canCreate)
                <a href="{{ $newRoute }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                    <i class="fa-solid fa-plus mr-2"></i> Nuevo
                </a>
            @endcan

            {{-- Extras del módulo: Toggle Papelera, Columnas, etc. --}}
            @if (isset($extraActions))
                {{ $extraActions }}
            @endif

            {{-- Botón Acciones: recibe todo de table-wrapper y lo pasa aquí --}}
            <x-admin.action-button-group
                :exportRoute="$exportRoute"
                :pdfRoute="$pdfRoute"
                :search="$search"
                :visibleColumns="$visibleColumns"
                :canExportExcel="$canExportExcel"
                :canExportPdf="$canExportPdf"
                :canImport="$canImport"
            />

        </div>
    </div>

    {{-- Buscador + Filtros + Paginación --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-wrap gap-4 items-center justify-between">
        <div class="flex-1 min-w-[300px] relative">
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Buscar..."
                class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 transition">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
        </div>

        <div class="flex items-center gap-3">
            @if (isset($filters))
                {{ $filters }}
            @endif
            <div class="h-8 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>
            <select wire:model.live="perPage"
                class="rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-blue-500 border-none bg-transparent font-medium">
                <option value="10">10 registros</option>
                <option value="25">25 registros</option>
                <option value="50">50 registros</option>
            </select>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden border dark:border-gray-700">
        {{ $slot }}
    </div>

</div>
