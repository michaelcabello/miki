<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => 'dashboard',
            'Impuestos'  => '#',
        ]" />
    </div>

    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Taxes Management') }}</h1>
        </div>

        <div class="flex flex-wrap gap-2">

            @can('Tax Create')
                <a href="{{ route('admin.taxes.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                    <i class="fa-solid fa-plus mr-2"></i> Nuevo
                </a>
            @endcan

            {{-- Menú Acciones: Export / Import --}}
            @canany(['Tax ExportExcel', 'Tax ExportPdf', 'Tax ImportExcel'])
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 transition">
                    <i class="fa-solid fa-ellipsis-vertical mr-2"></i> Acciones
                </button>

                <div x-show="open" @click.away="open = false" x-transition
                     class="absolute right-0 mt-2 w-52 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden z-50">
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">

                        @can('Tax ExportExcel')
                        <li>
                            <a href="{{ route('admin.taxes.export.excel', ['search' => $search, 'status' => $status, 'type' => $typeFilter]) }}"
                               target="_blank"
                               class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 block">
                                <i class="fa-solid fa-file-export mr-2 text-green-600"></i> Exportar Excel
                            </a>
                        </li>
                        @endcan

                        @can('Tax ExportPdf')
                        <li>
                            <a href="{{ route('admin.taxes.export.pdf', ['search' => $search, 'status' => $status, 'type' => $typeFilter]) }}"
                               target="_blank"
                               class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 block">
                                <i class="fa-solid fa-file-pdf mr-2 text-red-600"></i> Reporte PDF
                            </a>
                        </li>
                        @endcan

                        {{--
                            IMPORTAR — se usa wire:model en el <input type="file"> en lugar de un
                            <form action="..."> tradicional.

                            Por qué: Livewire 3 gestiona su propio ciclo de request HTTP.
                            Un <form POST> interno colisiona con ese ciclo y provoca
                            "POST method not supported for route admin/taxes".

                            Con wire:model="importFile" + WithFileUploads, Livewire sube el archivo
                            a su endpoint interno (/livewire/upload-file), luego dispara
                            updatedImportFile() que ejecuta el import directamente en el componente.
                            No hay ningún POST externo ni redirección intermedia.
                        --}}
                        @can('Tax ImportExcel')
                        <li>
                            <label for="importTaxesFile"
                                   class="flex items-center w-full text-left px-4 py-2 text-sm
                                          hover:bg-gray-100 dark:hover:bg-gray-700
                                          text-gray-700 dark:text-gray-300 cursor-pointer">
                                <i class="fa-solid fa-file-import mr-2 text-orange-600"></i> Importar

                                {{-- El input usa wire:model — Livewire intercepta la selección
                                     y llama a updatedImportFile() automáticamente --}}
                                <input type="file"
                                       id="importTaxesFile"
                                       wire:model="importFile"
                                       accept=".csv,.xlsx,.xls"
                                       class="hidden">
                            </label>

                            {{-- Indicador de carga mientras Livewire procesa el archivo --}}
                            <div wire:loading wire:target="importFile"
                                 class="px-4 py-2 text-xs text-indigo-600 dark:text-indigo-300 flex items-center gap-1">
                                <svg class="animate-spin h-3 w-3" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                                Procesando...
                            </div>

                            {{-- Error de validación del archivo --}}
                            @error('importFile')
                            <p class="px-4 pb-2 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </li>
                        @endcan

                    </ul>
                </div>
            </div>
            @endcanany

            {{-- Columnas opcionales --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition">
                    <i class="fa-solid fa-eye mr-2"></i> Mostrar
                </button>
                <div x-show="open" @click.away="open = false" x-transition
                     class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-3 z-50">
                    <p class="text-gray-700 dark:text-gray-300 font-semibold mb-2">Columnas</p>
                    <label class="flex items-center mb-1">
                        <input type="checkbox" wire:model.live="columns.sequence" class="mr-2 rounded border-gray-300">
                        Secuencia
                    </label>
                    <label class="flex items-center mb-1">
                        <input type="checkbox" wire:model.live="columns.description" class="mr-2 rounded border-gray-300">
                        Descripción
                    </label>
                    <label class="flex items-center mb-1">
                        <input type="checkbox" wire:model.live="columns.tax_scope" class="mr-2 rounded border-gray-300">
                        Ámbito
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
                   placeholder="Buscar por nombre o descripción..."
                   class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                          bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring focus:ring-blue-500" />
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fa-solid fa-search"></i>
            </span>
        </div>

        {{-- Tipo de uso --}}
        <div>
            <select wire:model.live="typeFilter"
                    class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                <option value="all">Todos los tipos</option>
                <option value="sale">Ventas</option>
                <option value="purchase">Compras</option>
                <option value="none">Ninguno</option>
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

        {{-- Por página --}}
        <div>
            <select wire:model.live="perPage"
                    class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                <option value="10">10 registros</option>
                <option value="25">25 registros</option>
                <option value="50">50 registros</option>
            </select>
        </div>
    </div>

    {{-- Barra de acciones masivas (sticky, visible solo con selección) --}}
    @if ($this->selectedCount > 0)
        @can('Tax Delete')
        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg flex justify-between items-center sticky top-2 z-50">
            <span class="text-sm text-gray-700 dark:text-gray-200">
                {{ $this->selectedCount }} seleccionado(s)
            </span>
            <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                    onclick="confirmDeleteSelectedTaxes()">
                <i class="fa-solid fa-trash"></i> Eliminar seleccionados
            </button>
        </div>
        @endcan
    @endif

    {{-- Tabla principal --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs uppercase">
                <tr>
                    @can('Tax Delete')
                    <th class="px-4 py-3 w-10">
                        <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300">
                    </th>
                    @endcan

                    <th class="px-4 py-3 text-center cursor-pointer" wire:click="sortBy('id')">#</th>

                    @if ($columns['sequence'])
                    <th class="px-4 py-3 text-center cursor-pointer" wire:click="sortBy('sequence')">Sec.</th>
                    @endif

                    <th class="px-4 py-3 cursor-pointer" wire:click="sortBy('name')">Nombre</th>
                    <th class="px-4 py-3 text-center cursor-pointer" wire:click="sortBy('amount')">Monto</th>
                    <th class="px-4 py-3 text-center cursor-pointer" wire:click="sortBy('amount_type')">Tipo cálculo</th>
                    <th class="px-4 py-3 text-center cursor-pointer" wire:click="sortBy('type_tax_use')">Uso</th>

                    @if ($columns['tax_scope'])
                    <th class="px-4 py-3 text-center">Ámbito</th>
                    @endif

                    @if ($columns['description'])
                    <th class="px-4 py-3">Descripción</th>
                    @endif

                    <th class="px-4 py-3 text-center cursor-pointer" wire:click="sortBy('active')">Estado</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($taxes as $tax)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">

                    @can('Tax Delete')
                    <td class="px-4 py-3">
                        <input type="checkbox" wire:model.live="selectedItems.{{ $tax->id }}"
                               value="{{ $tax->id }}" class="rounded border-gray-300 dark:border-gray-600">
                    </td>
                    @endcan

                    <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">{{ $tax->id }}</td>

                    @if ($columns['sequence'])
                    <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">{{ $tax->sequence }}</td>
                    @endif

                    {{-- Nombre + cuenta contable si existe --}}
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $tax->name }}</p>
                        @if ($tax->account)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                <i class="fa-solid fa-landmark text-[10px] mr-1"></i>{{ $tax->account->name }}
                            </p>
                        @endif
                    </td>

                    {{-- Monto según tipo --}}
                    <td class="px-4 py-3 text-center font-mono text-gray-700 dark:text-gray-300">
                        @if ($tax->amount_type === 'percent')
                            {{ number_format($tax->amount, 2) }}%
                        @elseif ($tax->amount_type === 'fixed')
                            S/ {{ number_format($tax->amount, 2) }}
                        @else
                            {{ number_format($tax->amount, 2) }}
                        @endif
                    </td>

                    {{-- Tipo de cálculo --}}
                    <td class="px-4 py-3 text-center">
                        @php
                            $typeLabels = [
                                'percent'  => ['label' => 'Porcentaje', 'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
                                'fixed'    => ['label' => 'Fijo',       'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'],
                                'division' => ['label' => 'División',   'class' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'],
                                'group'    => ['label' => 'Grupo',      'class' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
                            ];
                            $at = $typeLabels[$tax->amount_type] ?? ['label' => $tax->amount_type, 'class' => 'bg-gray-100 text-gray-700'];
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $at['class'] }}">
                            {{ $at['label'] }}
                        </span>
                    </td>

                    {{-- Ámbito de uso --}}
                    <td class="px-4 py-3 text-center">
                        @php
                            $useLabels = [
                                'sale'     => ['label' => 'Ventas',  'class' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
                                'purchase' => ['label' => 'Compras', 'class' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300'],
                                'none'     => ['label' => 'Ninguno', 'class' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
                            ];
                            $ul = $useLabels[$tax->type_tax_use] ?? ['label' => $tax->type_tax_use, 'class' => 'bg-gray-100 text-gray-700'];
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $ul['class'] }}">
                            {{ $ul['label'] }}
                        </span>
                    </td>

                    @if ($columns['tax_scope'])
                    <td class="px-4 py-3 text-center text-xs text-gray-500 dark:text-gray-400">
                        {{ $tax->tax_scope ?? '—' }}
                    </td>
                    @endif

                    @if ($columns['description'])
                    <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400 max-w-xs truncate">
                        {{ $tax->description ?? '—' }}
                    </td>
                    @endif

                    {{-- Toggle activo/inactivo --}}
                    <td class="px-4 py-3 text-center">
                        @can('Tax Update')
                            <button wire:click="toggleActive({{ $tax->id }})"
                                    title="{{ $tax->active ? 'Desactivar' : 'Activar' }}"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors
                                           focus:outline-none focus:ring-2 focus:ring-offset-2
                                           {{ $tax->active ? 'bg-green-500 focus:ring-green-500' : 'bg-gray-300 dark:bg-gray-600 focus:ring-gray-400' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform
                                             {{ $tax->active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        @else
                            @if ($tax->active)
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

                    {{-- Acciones: editar / eliminar --}}
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-2">
                            @can('Tax Update')
                            <a href="{{ route('admin.taxes.edit', $tax) }}"
                               class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                               title="Editar">
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            @endcan

                            @can('Tax Delete')
                            <button onclick="confirmDeleteTax({{ $tax->id }}, '{{ addslashes($tax->name) }}')"
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
                    <td colspan="10" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        <i class="fa-solid fa-receipt text-4xl mb-2 block"></i>
                        <p>No se encontraron impuestos</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div class="mt-4">
        {{ $taxes->links() }}
    </div>

    @push('scripts')
    <script>
        function confirmDeleteSelectedTaxes() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¡Esta acción eliminará los impuestos seleccionados!',
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

        function confirmDeleteTax(id, name) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: `El impuesto "${name}" será eliminado.`,
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
