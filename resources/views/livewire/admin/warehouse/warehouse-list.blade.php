<div class="space-y-6">

    <x-admin.table-wrapper
        title="Gestión de Almacenes"
        :newRoute="route('admin.warehouses.create')"
        canCreate="Warehouse Create"
        :selectedCount="$selectedCount"
        :showTrashed="$showTrashed"
        canDelete="Warehouse Delete"
        canRestore="Warehouse Restore"
        canExportExcel="Warehouse ExportExcel"
        canExportPdf="Warehouse ExportPdf"
        canImport="Warehouse ImportExcel"
        :exportRoute="route('admin.warehouses.export')"
        :pdfRoute="route('admin.warehouses.pdf')"
        :search="$search"
        :visibleColumns="array_keys(array_filter($columns))"
    >

        {{-- Slot: Toggle Papelera + Columnas opcionales --}}
        <x-slot name="extraActions">
            <div class="flex items-center gap-4">

                {{-- Toggle Papelera --}}
                @if(auth()->user()->can('Warehouse Restore'))
                    <label class="flex items-center cursor-pointer group">
                        <div class="mr-2 text-sm font-medium text-gray-600 dark:text-gray-400">Ver Papelera</div>
                        <div class="relative">
                            <input type="checkbox" wire:model.live="showTrashed" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600 transition-colors"></div>
                        </div>
                    </label>
                @endif

                {{-- Selector de columnas opcionales --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-eye mr-2"></i> Columnas
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak
                        class="absolute right-0 z-50 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-3">
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 border-b pb-1">Configuración</p>
                        <label class="flex items-center text-sm cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 p-1 rounded">
                            <input type="checkbox" wire:model.live="columns.order"
                                class="mr-2 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <span>Ver columna Orden</span>
                        </label>
                    </div>
                </div>

            </div>
        </x-slot>

        {{-- Slot: Filtro de estado --}}
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

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        @can('Warehouse Delete')
                            <th class="p-4 text-center">
                                <input type="checkbox" wire:model.live="selectAll"
                                    class="rounded border-gray-300 text-blue-600">
                            </th>
                        @endcan

                        <x-admin.th field="id"      :$sortField :$sortDirection align="center">ID</x-admin.th>
                        @if ($columns['order'])
                            <x-admin.th field="order" :$sortField :$sortDirection align="center">Orden</x-admin.th>
                        @endif
                        <x-admin.th field="code"    :$sortField :$sortDirection>Código</x-admin.th>
                        <x-admin.th field="name"    :$sortField :$sortDirection>Nombre</x-admin.th>
                        <x-admin.th field="address" :$sortField :$sortDirection>Dirección</x-admin.th>
                        <x-admin.th field="is_main" :$sortField :$sortDirection align="center">Principal</x-admin.th>
                        <x-admin.th field="state"   :$sortField :$sortDirection align="center">Estado</x-admin.th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($warehouses as $item)
                        <tr wire:key="{{ $item->id }}"
                            class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors
                                {{ isset($selectedItems[$item->id]) && $selectedItems[$item->id] ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}">

                            @can('Warehouse Delete')
                                <td class="p-4 text-center">
                                    <input type="checkbox"
                                        wire:model.live="selectedItems.{{ $item->id }}"
                                        wire:key="check-{{ $item->id }}"
                                        class="rounded border-gray-300 text-blue-600">
                                </td>
                            @endcan

                            <td class="px-4 py-3 text-center text-sm text-gray-500">{{ $item->id }}</td>

                            @if ($columns['order'])
                                <td class="px-4 py-3 text-center text-sm">{{ $item->order }}</td>
                            @endif

                            {{-- Código con badge indigo --}}
                            <td class="px-4 py-3 text-sm font-medium">
                                <span class="px-2 py-1 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 text-xs font-bold">
                                    {{ $item->code }}
                                </span>
                            </td>

                            {{-- Nombre + descripción como subtexto --}}
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $item->name }}</div>
                                @if ($item->description)
                                    <div class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ $item->description }}</div>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ $item->address ?? '—' }}
                            </td>

                            {{-- Badge almacén principal (estilo Odoo) --}}
                            <td class="px-4 py-3 text-center">
                                @if ($item->is_main)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300 text-xs font-bold">
                                        <i class="fa-solid fa-star text-[10px]"></i> Principal
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- Toggle Estado --}}
                            <td class="px-4 py-3 text-center">
                                @if ($showTrashed)
                                    <span class="text-xs text-red-400 italic">Eliminado</span>
                                @else
                                    @can('Warehouse Update')
                                        <button wire:click="toggleState({{ $item->id }})"
                                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $item->state ? 'bg-green-500' : 'bg-gray-300' }}">
                                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $item->state ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                        </button>
                                    @else
                                        <span class="px-2 py-1 rounded {{ $item->state ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }} text-xs">
                                            {{ $item->state ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    @endcan
                                @endif
                            </td>

                            {{-- Acciones por fila --}}
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-3">
                                    @if ($showTrashed)
                                        @can('Warehouse Restore')
                                            <button type="button"
                                                onclick="confirmRestore({{ $item->id }}, '{{ addslashes($item->name) }}')"
                                                class="text-indigo-600 hover:text-indigo-800 transition"
                                                title="Restaurar almacén">
                                                <i class="fa-solid fa-trash-arrow-up fa-lg"></i>
                                            </button>
                                        @endcan
                                        @can('forceDelete', $item)
                                            <button type="button"
                                                onclick="confirmForceDelete({{ $item->id }}, '{{ addslashes($item->name) }}')"
                                                class="text-red-600 hover:text-red-800 transition"
                                                title="Eliminar permanentemente">
                                                <i class="fa-solid fa-eraser fa-lg"></i>
                                            </button>
                                        @endcan
                                    @else
                                        @can('Warehouse Update')
                                            <a href="{{ route('admin.warehouses.edit', $item) }}"
                                                class="text-blue-600 hover:text-blue-800 transition"
                                                title="Editar almacén">
                                                <i class="fa-solid fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('Warehouse Delete')
                                            <button type="button"
                                                onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->name) }}')"
                                                class="text-red-600 hover:text-red-800 transition"
                                                title="Eliminar almacén">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        @endcan
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-gray-400 text-sm">
                                <i class="fa-solid fa-warehouse text-3xl mb-2 block opacity-30"></i>
                                No se encontraron almacenes.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
            {{ $warehouses->links() }}
        </div>

    </x-admin.table-wrapper>

    {{-- Modal de Importación --}}
    <x-admin.import-modal
        :routeName="route('admin.warehouses.import')"
        :templateRoute="route('admin.warehouses.template')"
        columns="code, name, description, address, is_main, order, state"
    />

</div>

