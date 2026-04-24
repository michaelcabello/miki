@props([
    'exportRoute'    => null,
    'pdfRoute'       => null,
    'search'         => '',
    'visibleColumns' => [],
    'canExportExcel' => null,
    'canExportPdf'   => null,
    'canImport'      => null,
])

@if ($canExportExcel || $canExportPdf || $canImport)
<div x-data="{ open: false }" class="relative inline-block text-left">

    <button @click="open = !open"
        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
        <i class="fa-solid fa-ellipsis-vertical mr-2"></i> Acciones
        <i class="fa-solid fa-chevron-down ml-2 text-[10px]"></i>
    </button>

    <div x-show="open" @click.away="open = false" x-cloak
        class="absolute right-0 z-50 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-1">

        @if ($canExportExcel && $exportRoute)
            @can($canExportExcel)
                <a href="{{ $exportRoute . '?' . http_build_query(array_filter(['search' => $search, 'columns' => $visibleColumns])) }}"
                    class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/30 transition">
                    <i class="fa-solid fa-file-excel mr-3 text-green-600"></i> Exportar Excel
                </a>
            @endcan
        @endif

        @if ($canExportPdf && $pdfRoute)
            @can($canExportPdf)
                <a href="{{ $pdfRoute . '?' . http_build_query(array_filter(['search' => $search, 'columns' => $visibleColumns])) }}"
                    class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition">
                    <i class="fa-solid fa-file-pdf mr-3 text-red-600"></i> Reporte PDF
                </a>
            @endcan
        @endif

        @if ($canImport)
            @can($canImport)
                <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                <button @click="open = false; $dispatch('open-import')"
                    class="flex w-full items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-orange-50 dark:hover:bg-orange-900/30 transition">
                    <i class="fa-solid fa-file-import mr-3 text-orange-600"></i> Importar Datos
                </button>
            @endcan
        @endif

    </div>
</div>
@endif
