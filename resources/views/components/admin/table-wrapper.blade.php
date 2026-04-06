{{-- resources/views/components/admin/table-wrapper.blade.php --}}
@props(['title', 'newRoute', 'canCreate', 'selectedCount' => 0, 'showTrashed' => false])

<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col md:flex-row justify-between items-center gap-4 border-b dark:border-gray-700">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $title }}</h1>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if ($selectedCount > 0)
                @if ($showTrashed)
                    {{-- 🟢 BOTÓN RESTAURAR MASIVO --}}
                    <button type="button" onclick="confirmBulkRestore()"
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition shadow-md">
                        <i class="fa-solid fa-trash-arrow-up mr-2"></i> Restaurar ({{ $selectedCount }})
                    </button>
                @else
                    {{-- 🔴 BOTÓN ELIMINAR MASIVO --}}
                    @can('JournalType Delete')
                    <button type="button" onclick="confirmBulkDelete()"
                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition shadow-md animate-pulse">
                        <i class="fa-solid fa-trash-can mr-2"></i> Eliminar ({{ $selectedCount }})
                    </button>
                    @endcan
                @endif
            @endif

            @can($canCreate)
                <a href="{{ $newRoute }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                    <i class="fa-solid fa-plus mr-2"></i> Nuevo
                </a>
            @endcan

            @if (isset($extraActions)) {{ $extraActions }} @endif
            <x-action-button-group />
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden border dark:border-gray-700">
        {{ $slot }}
    </div>
</div>
