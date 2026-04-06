<div x-data="{ open: false }" class="relative inline-block text-left">
    <button @click="open = !open"
            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
        <i class="fa-solid fa-ellipsis-vertical mr-2"></i> Acciones
        <i class="fa-solid fa-chevron-down ml-2 text-[10px]"></i>
    </button>

    <div x-show="open" @click.away="open = false" x-cloak
         class="absolute right-0 z-50 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-1">

        <button wire:click="exportExcel" @click="open = false"
                class="flex w-full items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100">
            <i class="fa-solid fa-file-excel mr-2 text-green-600"></i> Exportar Excel
        </button>

        <button wire:click="generatePdf" @click="open = false"
                class="flex w-full items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100">
            <i class="fa-solid fa-file-pdf mr-2 text-red-600"></i> Reporte PDF
        </button>

        <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

        <button wire:click="import" @click="open = false"
                class="flex w-full items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100">
            <i class="fa-solid fa-upload mr-2 text-orange-600"></i> Importar
        </button>
    </div>
</div>
