<div class="space-y-6">
    {{-- 1. Wrapper Maestro: Gestiona Título, Botón Nuevo y Botón Rojo Masivo --}}
    <x-admin.table-wrapper title="Gestión de Tipos de Diario" :newRoute="route('admin.journaltypes.create')" canCreate="JournalType Create"
        :selectedCount="$selectedCount" :showTrashed="$showTrashed">
        {{-- Slot de Acciones Extra (Botón Mostrar Columnas y Toggle Papelera) --}}
        <x-slot name="extraActions">
            <div class="flex items-center gap-4">
                {{-- Toggle Papelera Odoo Style --}}
                <label class="flex items-center cursor-pointer group">
                    <div class="mr-2 text-sm font-medium text-gray-600 dark:text-gray-400">Ver Papelera</div>
                    <div class="relative">
                        <input type="checkbox" wire:model.live="showTrashed" class="sr-only peer">
                        <div
                            class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600 transition-colors">
                        </div>
                    </div>
                </label>

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

        {{-- 2. Filtros Adicionales (Se ocultan en Papelera) --}}
        @if (!$showTrashed)
            <div class="px-4 py-2 flex gap-4">
                <select wire:model.live="status"
                    class="rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-blue-500">
                    <option value="all">Todos los estados</option>
                    <option value="active">Solo Activos</option>
                    <option value="inactive">Solo Inactivos</option>
                </select>
            </div>
        @endif

        {{-- 3. Tabla de Datos --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="p-4 text-center">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="rounded border-gray-300 text-blue-600">
                        </th>
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
                            <td class="p-4 text-center">
                                <input type="checkbox" wire:model.live="selectedItems.{{ $jt->id }}"
                                    wire:key="check-{{ $jt->id }}" class="rounded border-gray-300 text-blue-600">
                            </td>
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
                                            <button wire:click="restore({{ $jt->id }})"
                                                class="text-indigo-600 hover:text-indigo-800 transition"
                                                title="Restaurar este registro">
                                                <i class="fa-solid fa-trash-arrow-up fa-lg"></i>
                                            </button>
                                        @endcan
                                    @else
                                        {{-- 🔵 Acciones Normales (Editar/Eliminar) --}}
                                        @can('JournalType Update')
                                            <a href="{{ route('admin.journaltypes.edit', $jt) }}" class="text-blue-600">
                                                <i class="fa-solid fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('JournalType Delete')
                                            <button
                                                onclick="confirmDeleteJournalType({{ $jt->id }}, '{{ addslashes($jt->name) }}')"
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

    {{-- 5. Scripts de Confirmación (SweetAlert2) --}}
    @push('scripts') {{-- 👈 Prueba con 'scripts' si 'js' no carga el SweetAlert --}}
<script>
    function confirmDeleteJournalType(id, name) {
        Swal.fire({
            title: '¿Mover a la papelera?',
            text: `El registro "${name}" será ocultado de la lista activa.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // 🚀 CORRECCIÓN: Enviamos los parámetros directamente sin la llave 'data'
                Livewire.dispatch('deleteSingle', { id: id, name: name });
            }
        });
    }

    function confirmBulkDelete() {
        Swal.fire({
            title: '¿Eliminar seleccionados?',
            text: "Se moverán a la papelera los registros seleccionados.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Sí, eliminar todo'
        }).then((result) => {
            if (result.isConfirmed) {
                // 🚀 CORRECCIÓN: El evento debe coincidir con el #[On] del PHP
                Livewire.dispatch('confirmDeleteSelected');
            }
        });
    }
</script>
@endpush
</div>
