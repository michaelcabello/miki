<div class="space-y-6">

    <x-admin.table-wrapper
        title="Ubicaciones de Almacén"
        :newRoute="route('admin.warehouse-locations.create')"
        canCreate="WarehouseLocation Create"
        :selectedCount="$selectedCount"
        :showTrashed="$showTrashed"
        canDelete="WarehouseLocation Delete"
        canRestore="WarehouseLocation Restore"
        canExportExcel="WarehouseLocation ExportExcel"
        canExportPdf="WarehouseLocation ExportPdf"
        canImport="WarehouseLocation ImportExcel"
        :exportRoute="route('admin.warehouse-locations.export')"
        :pdfRoute="route('admin.warehouse-locations.pdf')"
        :search="$search"
        :visibleColumns="array_keys(array_filter($columns))"
    >

        {{-- Slot: Toggle Papelera + Columnas opcionales --}}
        <x-slot name="extraActions">
            <div class="flex items-center gap-4">

                @if(auth()->user()->can('WarehouseLocation Restore'))
                    <label class="flex items-center cursor-pointer group">
                        <div class="mr-2 text-sm font-medium text-gray-600 dark:text-gray-400">Ver Papelera</div>
                        <div class="relative">
                            <input type="checkbox" wire:model.live="showTrashed" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600 transition-colors"></div>
                        </div>
                    </label>
                @endif

                {{-- Selector de columna Orden --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-eye mr-2"></i> Columnas
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak
                        class="absolute right-0 z-50 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-3">
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 border-b pb-1">Configuración</p>
                        <label class="flex items-center text-sm cursor-pointer hover:bg-gray-50 p-1 rounded">
                            <input type="checkbox" wire:model.live="columns.order"
                                class="mr-2 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <span>Ver columna Orden</span>
                        </label>
                    </div>
                </div>

            </div>
        </x-slot>

        {{-- Slot: Filtros de estado + usage + almacén --}}
        <x-slot name="filters">
            @if (!$showTrashed)
                {{-- Filtro Estado --}}
                <select wire:model.live="status"
                    class="rounded-lg border-none bg-transparent text-sm font-medium text-gray-600 dark:text-gray-300 focus:ring-0">
                    <option value="all">Todos</option>
                    <option value="active">Activos</option>
                    <option value="inactive">Inactivos</option>
                </select>

                {{-- Filtro Tipo (usage) --}}
                <select wire:model.live="filterUsage"
                    class="rounded-lg border-none bg-transparent text-sm font-medium text-gray-600 dark:text-gray-300 focus:ring-0">
                    <option value="all">Todos los tipos</option>
                    @foreach(\App\Models\WarehouseLocation::$usageLabels as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>

                {{-- Filtro Almacén --}}
                <select wire:model.live="filterWarehouse"
                    class="rounded-lg border-none bg-transparent text-sm font-medium text-gray-600 dark:text-gray-300 focus:ring-0">
                    <option value="all">Todos los almacenes</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->code }} - {{ $wh->name }}</option>
                    @endforeach
                </select>
            @endif
        </x-slot>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        @can('WarehouseLocation Delete')
                            <th class="p-4 text-center">
                                <input type="checkbox" wire:model.live="selectAll"
                                    class="rounded border-gray-300 text-blue-600">
                            </th>
                        @endcan

                        <x-admin.th field="id"            :$sortField :$sortDirection align="center">ID</x-admin.th>
                        @if ($columns['order'])
                            <x-admin.th field="order"     :$sortField :$sortDirection align="center">Orden</x-admin.th>
                        @endif
                        <x-admin.th field="code"          :$sortField :$sortDirection align="center">Código</x-admin.th>
                        <x-admin.th field="complete_name" :$sortField :$sortDirection>Ubicación</x-admin.th>
                        <x-admin.th field="usage"         :$sortField :$sortDirection align="center">Tipo</x-admin.th>
                        <x-admin.th field="warehouse_id"  :$sortField :$sortDirection align="center">Almacén</x-admin.th>
                        <x-admin.th field="state"         :$sortField :$sortDirection align="center">Estado</x-admin.th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($locations as $item)
                        <tr wire:key="{{ $item->id }}"
                            class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors
                                {{ isset($selectedItems[$item->id]) && $selectedItems[$item->id] ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}">

                            @can('WarehouseLocation Delete')
                                <td class="p-4 text-center">
                                    <input type="checkbox"
                                        wire:model.live="selectedItems.{{ $item->id }}"
                                        wire:key="check-{{ $item->id }}"
                                        class="rounded border-gray-300 text-blue-600">
                                </td>
                            @endcan

                            <td class="px-4 py-3 text-center text-sm text-gray-500">{{ $item->id }}</td>

                            @if ($columns['order'])
                                <td class="px-4 py-3 text-center text-sm text-gray-500">{{ $item->order }}</td>
                            @endif

                            {{-- Código con badge --}}
                            <td class="px-4 py-3 text-center text-sm font-medium">
                                <span class="px-2 py-1 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 text-xs font-bold font-mono">
                                    {{ $item->code }}
                                </span>
                            </td>

                            {{-- Nombre completo estilo árbol Odoo --}}
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-gray-800 dark:text-gray-100">
                                    {{ $item->complete_name ?? $item->name }}
                                </div>
                                @if($item->parent)
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        <i class="fa-solid fa-turn-up fa-xs mr-1 rotate-90"></i>
                                        {{ $item->parent->complete_name ?? $item->parent->name }}
                                    </div>
                                @endif
                                @if($item->scrap_location)
                                    <span class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 rounded text-[10px] bg-red-100 text-red-600 font-bold">
                                        <i class="fa-solid fa-trash-can text-[9px]"></i> Merma
                                    </span>
                                @endif
                            </td>

                            {{-- Badge de tipo (usage) —  colores del modelo --}}
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $item->usage_color }}">
                                    {{ $item->usage_label }}
                                </span>
                            </td>

                            {{-- Almacén --}}
                            <td class="px-4 py-3 text-center text-sm">
                                @if($item->warehouse)
                                    <span class="px-2 py-1 rounded bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 text-xs font-mono">
                                        {{ $item->warehouse->code }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- Toggle State --}}
                            <td class="px-4 py-3 text-center">
                                @if ($showTrashed)
                                    <span class="text-xs text-red-400 italic">Eliminado</span>
                                @else
                                    @can('WarehouseLocation Update')
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

                            {{-- Acciones --}}
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-3">
                                    @if ($showTrashed)
                                        @can('WarehouseLocation Restore')
                                            <button type="button"
                                                onclick="confirmRestore({{ $item->id }}, '{{ addslashes($item->name) }}')"
                                                class="text-indigo-600 hover:text-indigo-800 transition" title="Restaurar">
                                                <i class="fa-solid fa-trash-arrow-up fa-lg"></i>
                                            </button>
                                        @endcan
                                        @can('forceDelete', $item)
                                            <button type="button"
                                                onclick="confirmForceDelete({{ $item->id }}, '{{ addslashes($item->name) }}')"
                                                class="text-red-600 hover:text-red-800 transition" title="Eliminar permanentemente">
                                                <i class="fa-solid fa-eraser fa-lg"></i>
                                            </button>
                                        @endcan
                                    @else
                                        @can('WarehouseLocation Update')
                                            <a href="{{ route('admin.warehouse-locations.edit', $item) }}"
                                                class="text-blue-600 hover:text-blue-800 transition" title="Editar">
                                                <i class="fa-solid fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('WarehouseLocation Delete')
                                            <button type="button"
                                                onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->name) }}')"
                                                class="text-red-600 hover:text-red-800 transition" title="Eliminar">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        @endcan
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-map-pin text-4xl mb-3 block opacity-30"></i>
                                No se encontraron ubicaciones.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
            {{ $locations->links() }}
        </div>

    </x-admin.table-wrapper>

    {{-- Modal de Importación --}}
    <x-admin.import-modal
        :routeName="route('admin.warehouse-locations.import')"
        :templateRoute="route('admin.warehouse-locations.template')"
        columns="code, name, warehouse_id (código), parent_id (código), usage, scrap_location (0/1), order, state (0/1)"
    />

</div>
