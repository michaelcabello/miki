<div class="space-y-6">
    {{-- Breadcrumb --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => 'dashboard',
            'Atributos' => '#',
        ]" />
    </div>

    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Gestión de Atributos</h1>
            <p class="text-sm text-gray-500 dark:text-gray-300">Talla, Color, Material, etc. usados en variantes de producto.</p>
        </div>

        <div class="flex flex-wrap gap-2">

            @can('Attribute Create')
                <a href="{{ route('admin.attributes.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                    <i class="fa-solid fa-plus mr-2"></i> Nuevo Atributo
                </a>
            @endcan

            {{-- Acciones: export/import --}}
            @canany(['Attribute ExportExcel', 'Attribute ExportPdf', 'Attribute ImportExcel'])
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 transition">
                        <i class="fa-solid fa-ellipsis-vertical mr-2"></i> Acciones
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition
                        class="absolute right-0 mt-2 w-52 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden z-50">
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">

                            @can('Attribute ExportExcel')
                                <li>
                                    <a href="{{ route('admin.attributes.export.excel', ['search' => $search, 'status' => $status]) }}"
                                        target="_blank"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 block">
                                        <i class="fa-solid fa-file-export mr-2 text-green-600"></i> Exportar Excel
                                    </a>
                                </li>
                            @endcan

                            @can('Attribute ExportPdf')
                                <li>
                                    <a href="{{ route('admin.attributes.export.pdf', ['search' => $search, 'status' => $status]) }}"
                                        target="_blank"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 block">
                                        <i class="fa-solid fa-file-pdf mr-2 text-red-600"></i> Reporte PDF
                                    </a>
                                </li>
                            @endcan

                            @can('Attribute ImportExcel')
                                <li>
                                    <button type="button" onclick="document.getElementById('importAttributesFile').click()"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        <i class="fa-solid fa-file-import mr-2 text-orange-600"></i> Importar
                                    </button>

                                    <form action="{{ route('admin.attributes.import') }}" method="POST"
                                        enctype="multipart/form-data" id="importAttributesForm" class="hidden">
                                        @csrf
                                        <input type="file" name="file" id="importAttributesFile"
                                            accept=".csv,.xlsx,.xls"
                                            onchange="document.getElementById('importAttributesForm').submit()">
                                    </form>
                                </li>
                            @endcan

                        </ul>
                    </div>
                </div>
            @endcanany

            {{-- Mostrar columnas opcionales --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition">
                    <i class="fa-solid fa-eye mr-2"></i> Mostrar
                </button>

                <div x-show="open" @click.away="open = false" x-transition
                    class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-3 z-50">
                    <p class="text-gray-700 dark:text-gray-300 font-semibold mb-2">Columnas</p>
                    <label class="flex items-center mb-1">
                        <input type="checkbox" wire:model.live="columns.order" class="mr-2 rounded border-gray-300">
                        Orden
                    </label>
                    <label class="flex items-center mb-1">
                        <input type="checkbox" wire:model.live="columns.values_count" class="mr-2 rounded border-gray-300">
                        Nº Valores
                    </label>
                </div>
            </div>

        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-wrap gap-4 justify-between items-center">

        {{-- Buscar --}}
        <div class="relative w-full md:w-1/3">
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Buscar por nombre..."
                class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                       bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring focus:ring-blue-500" />
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fa-solid fa-search"></i>
            </span>
        </div>

        <div class="flex items-center gap-3">
            {{-- Estado --}}
            <select wire:model.live="status"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                <option value="all">Todos</option>
                <option value="active">Activos</option>
                <option value="inactive">Inactivos</option>
            </select>

            {{-- Registros por página --}}
            <select wire:model.live="perPage"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                <option value="10">10 registros</option>
                <option value="25">25 registros</option>
                <option value="50">50 registros</option>
            </select>
        </div>
    </div>

    {{-- Acciones masivas --}}
    @if ($this->selectedCount > 0)
        @can('Attribute Delete')
            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg flex justify-between items-center sticky top-2 z-50">
                <span class="text-sm text-gray-700 dark:text-gray-200">
                    {{ $this->selectedCount }} seleccionado(s)
                </span>
                <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                    onclick="confirmDeleteSelectedAttributes()">
                    <i class="fa-solid fa-trash"></i> Eliminar seleccionados
                </button>
            </div>
        @endcan
    @endif

    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    @can('Attribute Delete')
                        <th class="px-4 py-3 text-center">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="rounded border-gray-300 dark:border-gray-600">
                        </th>
                    @endcan

                    <th wire:click="sortBy('id')"
                        class="px-4 py-3 text-center cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase hover:bg-gray-100 dark:hover:bg-gray-600">
                        ID
                        @if ($sortField === 'id')
                            <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                        @endif
                    </th>

                    <th wire:click="sortBy('name')"
                        class="px-4 py-3 text-left cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase hover:bg-gray-100 dark:hover:bg-gray-600">
                        Nombre
                        @if ($sortField === 'name')
                            <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                        @endif
                    </th>

                    @if ($columns['order'])
                        <th wire:click="sortBy('order')"
                            class="px-4 py-3 text-center cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase hover:bg-gray-100 dark:hover:bg-gray-600">
                            Orden
                            @if ($sortField === 'order')
                                <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @endif
                        </th>
                    @endif

                    @if ($columns['values_count'])
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            Valores
                        </th>
                    @endif

                    <th wire:click="sortBy('state')"
                        class="px-4 py-3 text-center cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase hover:bg-gray-100 dark:hover:bg-gray-600">
                        Estado
                        @if ($sortField === 'state')
                            <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                        @endif
                    </th>

                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Acciones
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($attributes as $attribute)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">

                        @can('Attribute Delete')
                            <td class="px-4 py-3 text-center">
                                <input type="checkbox"
                                    wire:model.live="selectedItems.{{ $attribute->id }}"
                                    value="{{ $attribute->id }}"
                                    class="rounded border-gray-300 dark:border-gray-600">
                            </td>
                        @endcan

                        {{-- ID --}}
                        <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-300">
                            {{ $attribute->id }}
                        </td>

                        {{-- Nombre + link a valores --}}
                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200 font-medium">
                            <a href="{{ route('admin.attributes.values', $attribute) }}"
                                class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold">
                                {{ $attribute->name }}
                            </a>
                        </td>

                        {{-- Orden (opcional) --}}
                        @if ($columns['order'])
                            <td class="px-4 py-3 text-center text-sm text-gray-600 dark:text-gray-400">
                                {{ $attribute->order }}
                            </td>
                        @endif

                        {{-- Cantidad de valores (opcional) --}}
                        @if ($columns['values_count'])
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs
                                    {{ $attribute->values_count > 0
                                        ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300'
                                        : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                    <i class="fa-solid fa-tag text-[10px]"></i>
                                    {{ $attribute->values_count }}
                                </span>
                            </td>
                        @endif

                        {{-- Toggle estado --}}
                        <td class="px-4 py-3 text-center">
                            @can('Attribute Update')
                                <button wire:click="toggleState({{ $attribute->id }})"
                                    title="{{ $attribute->state ? 'Desactivar' : 'Activar' }}"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors
                                           focus:outline-none focus:ring-2 focus:ring-offset-2
                                           {{ $attribute->state
                                               ? 'bg-green-500 focus:ring-green-500'
                                               : 'bg-gray-300 dark:bg-gray-600 focus:ring-gray-400' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform
                                                 {{ $attribute->state ? 'translate-x-6' : 'translate-x-1' }}">
                                    </span>
                                </button>
                            @else
                                @if ($attribute->state)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                        <i class="fa-solid fa-circle text-[6px]"></i> Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">
                                        <i class="fa-solid fa-circle text-[6px]"></i> Inactivo
                                    </span>
                                @endif
                            @endcan
                        </td>

                        {{-- Acciones --}}
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">

                                {{-- Ver valores --}}
                                <a href="{{ route('admin.attributes.values', $attribute) }}"
                                    class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full
                                           bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-indigo-600 transition"
                                    title="Ver valores">
                                    <i class="fa-solid fa-tags"></i>
                                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Valores</span>
                                </a>

                                @can('Attribute Update')
                                    <a href="{{ route('admin.attributes.edit', $attribute) }}"
                                        class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full
                                               bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-green-600 transition"
                                        title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Editar</span>
                                    </a>
                                @endcan

                                @can('Attribute Delete')
                                    <button
                                        onclick="confirmDeleteAttribute({{ $attribute->id }}, '{{ addslashes($attribute->name) }}')"
                                        class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full
                                               bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-red-600 transition"
                                        title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Eliminar</span>
                                    </button>
                                @endcan

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fa-solid fa-inbox text-4xl mb-2 block"></i>
                            No se encontraron atributos
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div class="mt-4">
        {{ $attributes->links() }}
    </div>

    @push('scripts')
        <script>
            /**
             * Confirmación para eliminar un atributo individual.
             * Advierte que no se puede eliminar si tiene valores asignados a variantes.
             */
            function confirmDeleteAttribute(id, name) {
                Swal.fire({
                    title: '¿Eliminar atributo?',
                    html: `El atributo <strong>"${name}"</strong> será eliminado.<br>
                           <span class="text-sm text-gray-500">No se puede eliminar si tiene valores asignados a variantes de producto.</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.dispatch('deleteSingle', { id: id, name: name });
                    }
                });
            }

            /**
             * Confirmación para eliminar los atributos seleccionados.
             */
            function confirmDeleteSelectedAttributes() {
                Swal.fire({
                    title: '¿Eliminar seleccionados?',
                    text: '¡Esta acción eliminará los atributos seleccionados!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.dispatch('confirmDeleteSelected');
                    }
                });
            }

            document.addEventListener('livewire:init', () => {
                Livewire.on('itemDeleted', (event) => {
                    Swal.fire(event.title || '¡Eliminado!', event.text || '', event.icon || 'success');
                });

                Livewire.on('show-swalindex', (event) => {
                    Swal.fire(event.title || 'Notificación', event.text || '', event.icon || 'info');
                });
            });
        </script>
    @endpush

</div>
