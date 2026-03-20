<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => 'dashboard',
            'Diarios'   => '#',
        ]" />
    </div>

    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Gestión de Diarios</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Configura los diarios contables (Ventas, Compras, Banco, Caja, etc.)</p>
        </div>

        <div class="flex flex-wrap gap-2">
            @can('Journal Create')
                <a href="{{ route('admin.journals.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                    <i class="fa-solid fa-plus mr-2"></i> Nuevo Diario
                </a>
            @endcan

            {{-- Menú Acciones --}}
            @canany(['Journal ExportExcel', 'Journal ExportPdf', 'Journal ImportExcel'])
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 transition">
                        <i class="fa-solid fa-ellipsis-vertical mr-2"></i> Acciones
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition
                        class="absolute right-0 mt-2 w-52 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden z-50">
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @can('Journal ExportExcel')
                                <li>
                                    <a href="{{ route('admin.journals.export.excel', ['search' => $search, 'status' => $status, 'journal_type_id' => $journalTypeId]) }}"
                                        target="_blank"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 block">
                                        <i class="fa-solid fa-file-export mr-2 text-green-600"></i> Exportar Excel
                                    </a>
                                </li>
                            @endcan
                            @can('Journal ExportPdf')
                                <li>
                                    <a href="{{ route('admin.journals.export.pdf', ['search' => $search, 'status' => $status, 'journal_type_id' => $journalTypeId]) }}"
                                        target="_blank"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 block">
                                        <i class="fa-solid fa-file-pdf mr-2 text-red-600"></i> Reporte PDF
                                    </a>
                                </li>
                            @endcan
                            @can('Journal ImportExcel')
                                <li>
                                    <button type="button" onclick="document.getElementById('importJournalsFile').click()"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        <i class="fa-solid fa-file-import mr-2 text-orange-600"></i> Importar
                                    </button>
                                    <form action="{{ route('admin.journals.import') }}" method="POST"
                                        enctype="multipart/form-data" id="importJournalsForm" class="hidden">
                                        @csrf
                                        <input type="file" name="file" id="importJournalsFile"
                                            accept=".csv,.xlsx,.xls"
                                            onchange="document.getElementById('importJournalsForm').submit()">
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
                    <label class="flex items-center mb-1 text-sm text-gray-700 dark:text-gray-200">
                        <input type="checkbox" wire:model.live="columns.currency" class="mr-2 rounded border-gray-300">
                        Moneda
                    </label>
                    <label class="flex items-center mb-1 text-sm text-gray-700 dark:text-gray-200">
                        <input type="checkbox" wire:model.live="columns.use_documents" class="mr-2 rounded border-gray-300">
                        Usa documentos
                    </label>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-wrap gap-4 justify-between items-center">

        {{-- Búsqueda --}}
        <div class="relative w-full md:w-1/3">
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Buscar por código o nombre..."
                class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                       bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring focus:ring-blue-500" />
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fa-solid fa-search"></i>
            </span>
        </div>

        {{-- Filtro por tipo de diario --}}
        <div>
            <select wire:model.live="journalTypeId"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                <option value="">Todos los tipos</option>
                @foreach ($journalTypes as $jt)
                    <option value="{{ $jt->id }}">{{ $jt->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Estado --}}
        <div>
            <select wire:model.live="status"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                <option value="all">Todos</option>
                <option value="active">Activos</option>
                <option value="inactive">Inactivos</option>
            </select>
        </div>

        {{-- Registros por página --}}
        <div>
            <select wire:model.live="perPage"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                <option value="10">10 registros</option>
                <option value="25">25 registros</option>
                <option value="50">50 registros</option>
            </select>
        </div>
    </div>

    {{-- Barra acciones masivas --}}
    @if ($this->selectedCount > 0)
        @can('Journal Delete')
            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg flex justify-between items-center sticky top-2 z-50">
                <span class="text-sm text-gray-700 dark:text-gray-200">
                    {{ $this->selectedCount }} seleccionado(s)
                </span>
                <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                    onclick="confirmDeleteSelectedJournals()">
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
                    @can('Journal Delete')
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

                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Tipo
                    </th>

                    @if ($columns['currency'])
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            Moneda
                        </th>
                    @endif

                    @if ($columns['use_documents'])
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            Docs
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
                @forelse ($journals as $journal)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">

                        @can('Journal Delete')
                            <td class="px-4 py-3 text-center">
                                <input type="checkbox" wire:model.live="selectedItems.{{ $journal->id }}"
                                    value="{{ $journal->id }}"
                                    class="rounded border-gray-300 dark:border-gray-600">
                            </td>
                        @endcan

                        <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-300">
                            {{ $journal->id }}
                        </td>

                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 font-medium">
                                {{ $journal->code }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200 font-medium">
                            {{ $journal->name }}
                        </td>

                        <td class="px-4 py-3 text-sm">
                            @if ($journal->journalType)
                                <span class="px-2 py-1 rounded bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300 text-xs font-semibold">
                                    {{ $journal->journalType->name }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        @if ($columns['currency'])
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ $journal->currency?->name ?? '—' }}
                            </td>
                        @endif

                        @if ($columns['use_documents'])
                            <td class="px-4 py-3 text-center text-sm">
                                @if ($journal->use_documents)
                                    <i class="fa-solid fa-check text-green-500"></i>
                                @else
                                    <i class="fa-solid fa-xmark text-gray-300"></i>
                                @endif
                            </td>
                        @endif

                        {{-- Toggle de estado --}}
                        <td class="px-4 py-3 text-center">
                            @can('Journal Update')
                                <button wire:click="toggleState({{ $journal->id }})"
                                    title="{{ $journal->state ? 'Desactivar' : 'Activar' }}"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors
                                           focus:outline-none focus:ring-2 focus:ring-offset-2
                                           {{ $journal->state ? 'bg-green-500 focus:ring-green-500' : 'bg-gray-300 dark:bg-gray-600 focus:ring-gray-400' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform
                                                 {{ $journal->state ? 'translate-x-6' : 'translate-x-1' }}">
                                    </span>
                                </button>
                            @else
                                @if ($journal->state)
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
                                @can('Journal Update')
                                    <a href="{{ route('admin.journals.edit', $journal) }}"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                        title="Editar">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                @endcan
                                @can('Journal Delete')
                                    <button onclick="confirmDeleteJournal({{ $journal->id }}, '{{ addslashes($journal->name) }}')"
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
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fa-solid fa-inbox text-4xl mb-2"></i>
                            <p>No se encontraron diarios</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div class="mt-4">
        {{ $journals->links() }}
    </div>

    @push('scripts')
    <script>
        function confirmDeleteSelectedJournals() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción eliminará los diarios seleccionados.",
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) @this.dispatch('confirmDeleteSelected');
            });
        }

        function confirmDeleteJournal(id, name) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: `El diario "${name}" será eliminado.`,
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) @this.dispatch('deleteSingle', { id: id, name: name });
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

