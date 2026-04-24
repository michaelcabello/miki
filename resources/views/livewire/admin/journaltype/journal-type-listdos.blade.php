<div class="space-y-6">
    {{-- 1. Wrapper Maestro: Gestiona Título, Botón Nuevo y Botón Rojo Masivo --}}
    {{-- <x-admin.table-wrapper title="Gestión de Tipos de Diario" :newRoute="route('admin.journaltypesdos.create')" canCreate="JournalType Create"
        :selectedCount="$selectedCount" :showTrashed="$showTrashed"> --}}
    <x-admin.table-wrapper title="Gestión de Tipos de Diario" :newRoute="route('admin.journaltypesdos.create')" canCreate="JournalType Create"
        :selectedCount="$selectedCount" :showTrashed="$showTrashed" canDelete="JournalType Delete" canRestore="JournalType Restore"
        canExportExcel="JournalType ExportExcel" canExportPdf="JournalType ExportPdf" canImport="JournalType ImportExcel"
        :exportRoute="route('admin.journaltypesdos.export')" :pdfRoute="route('admin.journaltypesdos.pdf')" :search="$search" :visibleColumns="array_keys(array_filter($columns))">
        {{-- Slot de Acciones Extra (Botón Mostrar Columnas y Toggle Papelera) --}}
        <x-slot name="extraActions">
            <div class="flex items-center gap-4">
                {{-- Toggle Papelera Odoo Style --}}

                @can('JournalType Restore')
                    <label class="flex items-center cursor-pointer group">
                        <div class="mr-2 text-sm font-medium text-gray-600 dark:text-gray-400">Ver Papelera</div>
                        <div class="relative">
                            <input type="checkbox" wire:model.live="showTrashed" class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600 transition-colors">
                            </div>
                        </div>
                    </label>
                @endcan

                {{-- Botón Mostrar Columnas --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-eye mr-2"></i> Columnas
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak
                        class="absolute right-0 z-50 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-3">
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 border-b pb-1">Configuración
                        </p>
                        <label class="flex items-center text-sm cursor-pointer hover:bg-gray-50 p-1 rounded">
                            <input type="checkbox" wire:model.live="columns.order"
                                class="mr-2 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <span>Ver columna Orden</span>
                        </label>
                    </div>
                </div>
            </div>
        </x-slot>


        {{-- 🚀 NUEVO: Slot para integrar el filtro de estado en la fila del buscador --}}
        <x-slot name="filters">
            @if (!$showTrashed)
                <select wire:model.live="status"
                    class="rounded-lg border-none bg-transparent text-sm font-medium text-gray-600 dark:text-gray-300 focus:ring-0">
                    <option value="all">Todos</option>
                    <option value="active">Activos</option>
                    <option value="inactive">Inactivos</option>
                </select>
            @endif
        </x-slot>



        {{-- 3. Tabla de Datos --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        @can('JournalType Delete')
                            <th class="p-4 text-center">
                                <input type="checkbox" wire:model.live="selectAll"
                                    class="rounded border-gray-300 text-blue-600">
                            </th>
                        @endcan

                        <x-admin.th field="id" :$sortField :$sortDirection align="center">ID</x-admin.th>
                        @if ($columns['order'])
                            <x-admin.th field="order" :$sortField :$sortDirection align="center">Orden</x-admin.th>
                        @endif
                        <x-admin.th field="code" :$sortField :$sortDirection>Código</x-admin.th>
                        <x-admin.th field="name" :$sortField :$sortDirection>Nombre</x-admin.th>
                        <x-admin.th field="state" :$sortField :$sortDirection align="center">Estado</x-admin.th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($journalTypes as $jt)
                        <tr wire:key="{{ $jt->id }}"
                            class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors {{ isset($selectedItems[$jt->id]) && $selectedItems[$jt->id] ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}">
                            @can('JournalType Delete')
                                <td class="p-4 text-center">
                                    <input type="checkbox" wire:model.live="selectedItems.{{ $jt->id }}"
                                        wire:key="check-{{ $jt->id }}" class="rounded border-gray-300 text-blue-600">
                                </td>
                            @endcan
                            <td class="px-4 py-3 text-center text-sm text-gray-500">{{ $jt->id }}</td>
                            @if ($columns['order'])
                                <td class="px-4 py-3 text-center text-sm">{{ $jt->order }}</td>
                            @endif
                            <td class="px-4 py-3 text-sm font-medium">
                                <span
                                    class="px-2 py-1 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 text-xs font-bold">
                                    {{ $jt->code }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $jt->name }}</td>
                            <td class="px-4 py-3 text-center">
                                @if ($showTrashed)
                                    <span class="text-xs text-red-400 italic">Eliminado</span>
                                @else
                                    @can('JournalType Update')
                                        <button wire:click="toggleState({{ $jt->id }})"
                                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $jt->state ? 'bg-green-500' : 'bg-gray-300' }}">
                                            <span
                                                class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $jt->state ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                        </button>
                                    @else
                                        <span
                                            class="px-2 py-1 rounded {{ $jt->state ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }} text-xs">
                                            {{ $jt->state ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    @endcan
                                @endif
                            </td>

                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-3">
                                    @if ($showTrashed)
                                        {{-- 🟢 Acciones de Papelera --}}
                                        {{-- He quitado el @can temporalmente para que verifiques que funcione --}}
                                        @can('JournalType Restore')
                                            <button type="button"
                                                onclick="confirmRestore({{ $jt->id }}, '{{ addslashes($jt->name) }}')"
                                                class="text-indigo-600 hover:text-indigo-800 transition"
                                                title="Restaurar este registro">
                                                <i class="fa-solid fa-trash-arrow-up fa-lg"></i>
                                            </button>
                                        @endcan

                                        {{-- 🚀 NUEVO: Botón Eliminar Definitivamente --}}
                                        @can('forceDelete', $jt)
                                            <button type="button"
                                                onclick="confirmForceDelete({{ $jt->id }}, '{{ addslashes($jt->name) }}')"
                                                class="text-red-600 hover:text-red-800 transition"
                                                title="Eliminar permanentemente">
                                                <i class="fa-solid fa-eraser fa-lg"></i>
                                            </button>
                                        @endcan
                                    @else
                                        {{-- 🔵 Acciones Normales (Editar/Eliminar) --}}
                                        @can('JournalType Update')
                                            <a href="{{ route('admin.journaltypesdos.edit', $jt) }}" class="text-blue-600">
                                                <i class="fa-solid fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('JournalType Delete')
                                            <button type="button"
                                                onclick="confirmDelete({{ $jt->id }}, '{{ addslashes($jt->name) }}')"
                                                class="text-red-600">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        @endcan
                                    @endif
                                </div>
                            </td>



                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- 4. Paginación --}}
        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
            {{ $journalTypes->links() }}
        </div>
    </x-admin.table-wrapper>



    {{-- Modal de Importación Evolucionado --}}

    <x-admin.import-modal :routeName="route('admin.journaltypesdos.import')" :templateRoute="route('admin.journaltypesdos.template')" columns="code, name, state, order" />



</div>
