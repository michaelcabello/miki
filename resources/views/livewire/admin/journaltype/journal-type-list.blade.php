<div class="space-y-6">


    {{-- Breadcrumb --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => 'dashboard',
            'Journal Types' => '#',
        ]" />
    </div>


    {{-- header --}}
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Journal Types Management') }}</h1>
        </div>

        <div class="flex flex-wrap gap-2">
            @can('JournalType Create')
                <a href="{{ route('admin.journaltypes.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                    <i class="fa-solid fa-plus mr-2"></i> {{ __('New Jornal Types') }}
                </a>
            @endcan

            <!-- Acciones -->

            @canany(['JournalType ExportExcel', 'JournalType ExportPdf', 'JournalType ImportExcel'])

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 transition">
                        <i class="fa-solid fa-ellipsis-vertical mr-2"></i> Acciones
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition
                        class="absolute right-0 mt-2 w-52 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden z-50">
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">

                            @can('JournalType ExportExcel')
                                <li>
                                    <a href="{{ route('admin.journaltypes.export.excel', ['search' => $search, 'status' => $status]) }}"
                                        target="_blank"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 block">
                                        <i class="fa-solid fa-file-export mr-2 text-green-600"></i> Exportar Excel
                                    </a>
                                </li>
                            @endcan

                            @can('JournalType ExportPdf')
                                <li>
                                    <a href="{{ route('admin.journaltypes.export.pdf', ['search' => $search, 'status' => $status]) }}"
                                        target="_blank"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 block">
                                        <i class="fa-solid fa-file-pdf mr-2 text-red-600"></i> Reporte PDF
                                    </a>
                                </li>
                            @endcan

                            @can('JournalType ImportExcel')
                                <li>
                                    <button type="button" onclick="document.getElementById('importJournalTypesFile').click()"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        <i class="fa-solid fa-file-import mr-2 text-orange-600"></i> Importar
                                    </button>

                                    <form action="{{ route('admin.journaltypes.import') }}" method="POST"
                                        enctype="multipart/form-data" id="importJournalTypesForm" class="hidden">
                                        @csrf
                                        <input type="file" name="file" id="importJournalTypesFile"
                                            accept=".csv,.xlsx,.xls"
                                            onchange="document.getElementById('importJournalTypesForm').submit()">
                                    </form>
                                </li>
                            @endcan

                        </ul>
                    </div>
                </div>
            @endcanany


            <!-- Mostrar columnas -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition">
                    <i class="fa-solid fa-eye mr-2"></i> Mostrar
                </button>

                <div x-show="open" @click.away="open = false" x-transition
                    class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-3 z-50">
                    <p class="text-gray-700 dark:text-gray-300 font-semibold mb-2">Columnas</p>
                    {{-- <label class="flex items-center mb-1">
                        <input type="checkbox" wire:model.live="columns.street" class="mr-2 rounded border-gray-300">
                        teléfono
                    </label> --}}
                    <label class="flex items-center mb-1">
                        <input type="checkbox" wire:model.live="columns.order" class="mr-2 rounded border-gray-300">
                        Orden
                    </label>
                    {{--  <label class="flex items-center mb-1">
                        <input type="checkbox" wire:model.live="columns.street" class="mr-2 rounded border-gray-300">
                        Dirección
                    </label> --}}
                </div>
            </div>
        </div>

    </div>





    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-wrap gap-4 justify-between items-center">

        <!-- Buscar -->
        <div class="relative w-full md:w-1/3">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por código o nombre..."
                class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring focus:ring-blue-500" />
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fa-solid fa-search"></i>
            </span>
        </div>

        <!-- Estado -->
        <div>
            <select wire:model.live="status"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                <option value="all">Todos</option>
                <option value="active">Activos</option>
                <option value="inactive">Inactivos</option>
            </select>
        </div>

        <!-- Cantidad por página -->
        <div>
            <select wire:model.live="perPage"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                <option value="10">10 registros</option>
                <option value="25">25 registros</option>
                <option value="50">50 registros</option>
            </select>
        </div>
    </div>

    <!-- Acciones masivas -->
    @if ($this->selectedCount > 0)
        @can('JournalType Delete')
            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg flex justify-between items-center sticky top-2 z-50">
                <span class="text-sm text-gray-700 dark:text-gray-200">
                    {{ $this->selectedCount }} seleccionado(s)
                </span>
                <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                    onclick="confirmDeleteSelectedJournalTypes()">
                    <i class="fa-solid fa-trash"></i> Eliminar seleccionados
                </button>
            </div>
        @endcan
    @endif

    <!-- Tabla -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    @can('JournalType Delete')
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

                    {{--  <th wire:click="sortBy('order')"
                        class="px-4 py-3 text-center cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase hover:bg-gray-100 dark:hover:bg-gray-600">
                        Orden
                        @if ($sortField === 'order')
                            <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                        @endif
                    </th> --}}

                    @if ($columns['order'])
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            Orden
                        </th>
                    @endif

                    <th wire:click="sortBy('code')"
                        class="px-4 py-3 text-left cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase hover:bg-gray-100 dark:hover:bg-gray-600">
                        Código
                        @if ($sortField === 'code')
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
                @forelse ($journalTypes as $jt)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        @can('JournalType Delete')
                            <td class="px-4 py-3 text-center">
                                <input type="checkbox" wire:model.live="selectedItems.{{ $jt->id }}"
                                    value="{{ $jt->id }}" class="rounded border-gray-300 dark:border-gray-600">
                            </td>
                        @endcan

                        <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-300">
                            {{ $jt->id }}
                        </td>

                        {{-- <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-300">
                            <span
                                class="px-2 py-1 rounded bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                {{ $jt->order }}
                            </span>
                        </td> --}}

                        @if ($columns['order'])
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                <div class="space-y-1">
                                    @if (!empty($jt->order))
                                        <p class="font-semibold">{{ $jt->order }}</p>
                                    @endif

                                </div>
                            </td>
                        @endif

                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200 font-medium">
                            <span
                                class="px-2 py-1 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                                {{ $jt->code }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                            {{ $jt->name }}
                        </td>

                        <td class="px-4 py-3 text-center">
                            @can('JournalType Update')
                                {{-- Usuario con permiso: ve el toggle interactivo --}}
                                <button wire:click="toggleState({{ $jt->id }})"
                                    title="{{ $jt->state ? 'Desactivar' : 'Activar' }}"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors
                                focus:outline-none focus:ring-2 focus:ring-offset-2
                                {{ $jt->state ? 'bg-green-500 focus:ring-green-500' : 'bg-gray-300 dark:bg-gray-600 focus:ring-gray-400' }}">
                                                    <span
                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform
                         {{ $jt->state ? 'translate-x-6' : 'translate-x-1' }}">
                                    </span>
                                </button>
                            @else
                                {{-- Usuario sin permiso: solo ve el badge informativo --}}
                                @if ($jt->state)
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                        <i class="fa-solid fa-circle text-[6px]"></i> Activo
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">
                                        <i class="fa-solid fa-circle text-[6px]"></i> Inactivo
                                    </span>
                                @endif
                            @endcan
                        </td>

                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                {{-- Si luego creas edit, descomenta --}}
                                @can('JournalType Update')
                                    <a href="{{ route('admin.journaltypes.edit', $jt) }}"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                        title="Editar">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                @endcan

                                @can('JournalType Delete')
                                    <button
                                        onclick="confirmDeleteJournalType({{ $jt->id }}, '{{ addslashes($jt->name) }}')"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                        title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fa-solid fa-inbox text-4xl mb-2"></i>
                            <p>No se encontraron tipos de diario</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $journalTypes->links() }}
    </div>

    @push('scripts')
        <script>
            function confirmDeleteSelectedJournalTypes() {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡Esta acción eliminará los registros seleccionados!",
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

            function confirmDeleteJournalType(id, name) {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `El registro "${name}" será eliminado.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.dispatch('deleteSingle', {
                            id: id,
                            name: name
                        });
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
