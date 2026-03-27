<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => 'dashboard',
            'Planes de Suscripción' => '#',
        ]" />
    </div>

    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Planes de Suscripción</h1>
            <p class="text-sm text-gray-500 dark:text-gray-300">
                Define la frecuencia de cobro: mensual, anual, semanal, etc.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            @can('SubscriptionPlan Create')
                <a href="{{ route('admin.subscription-plans.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                    <i class="fa-solid fa-plus mr-2"></i> Nuevo Plan
                </a>
            @endcan

            @canany(['SubscriptionPlan ExportExcel', 'SubscriptionPlan ExportPdf', 'SubscriptionPlan ImportExcel'])
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 transition">
                        <i class="fa-solid fa-ellipsis-vertical mr-2"></i> Acciones
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition
                        class="absolute right-0 mt-2 w-52 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden z-50">
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">

                            @can('SubscriptionPlan ExportExcel')
                                <li>
                                    <a href="{{ route('admin.subscription-plans.export.excel', ['search' => $search, 'status' => $status]) }}"
                                        target="_blank"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 block">
                                        <i class="fa-solid fa-file-export mr-2 text-green-600"></i> Exportar Excel
                                    </a>
                                </li>
                            @endcan

                            @can('SubscriptionPlan ExportPdf')
                                <li>
                                    <a href="{{ route('admin.subscription-plans.export.pdf', ['search' => $search, 'status' => $status]) }}"
                                        target="_blank"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 block">
                                        <i class="fa-solid fa-file-pdf mr-2 text-red-600"></i> Reporte PDF
                                    </a>
                                </li>
                            @endcan

                            @can('SubscriptionPlan ImportExcel')
                                <li>
                                    <button type="button" onclick="document.getElementById('importPlansFile').click()"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        <i class="fa-solid fa-file-import mr-2 text-orange-600"></i> Importar
                                    </button>
                                    <form action="{{ route('admin.subscription-plans.import') }}" method="POST"
                                        enctype="multipart/form-data" id="importPlansForm" class="hidden">
                                        @csrf
                                        <input type="file" name="file" id="importPlansFile"
                                            accept=".csv,.xlsx,.xls"
                                            onchange="document.getElementById('importPlansForm').submit()">
                                    </form>
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
                    class="absolute right-0 mt-2 w-52 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-3 z-50">
                    <p class="font-semibold text-sm text-gray-700 dark:text-gray-300 mb-2">Columnas</p>
                    <label class="flex items-center mb-1 text-sm">
                        <input type="checkbox" wire:model.live="columns.order" class="mr-2 rounded border-gray-300">
                        Orden
                    </label>
                    <label class="flex items-center mb-1 text-sm">
                        <input type="checkbox" wire:model.live="columns.interval_label" class="mr-2 rounded border-gray-300">
                        Intervalo
                    </label>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-wrap gap-4 justify-between items-center">
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
            <select wire:model.live="status"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                <option value="all">Todos</option>
                <option value="active">Activos</option>
                <option value="inactive">Inactivos</option>
            </select>

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
        @can('SubscriptionPlan Delete')
            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg flex justify-between items-center sticky top-2 z-50">
                <span class="text-sm text-gray-700 dark:text-gray-200">{{ $this->selectedCount }} seleccionado(s)</span>
                <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                    onclick="confirmDeleteSelectedPlans()">
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
                    @can('SubscriptionPlan Delete')
                        <th class="px-4 py-3 text-center">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="rounded border-gray-300 dark:border-gray-600">
                        </th>
                    @endcan

                    <th wire:click="sortBy('id')"
                        class="px-4 py-3 text-center cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase hover:bg-gray-100 dark:hover:bg-gray-600">
                        ID @if ($sortField === 'id') <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i> @endif
                    </th>

                    <th wire:click="sortBy('name')"
                        class="px-4 py-3 text-left cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase hover:bg-gray-100 dark:hover:bg-gray-600">
                        Nombre @if ($sortField === 'name') <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i> @endif
                    </th>

                    @if ($columns['interval_label'])
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            Frecuencia
                        </th>
                    @endif

                    @if ($columns['order'])
                        <th wire:click="sortBy('order')"
                            class="px-4 py-3 text-center cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase hover:bg-gray-100 dark:hover:bg-gray-600">
                            Orden @if ($sortField === 'order') <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i> @endif
                        </th>
                    @endif

                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Productos
                    </th>

                    <th wire:click="sortBy('active')"
                        class="px-4 py-3 text-center cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase hover:bg-gray-100 dark:hover:bg-gray-600">
                        Estado @if ($sortField === 'active') <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i> @endif
                    </th>

                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Acciones
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($plans as $plan)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">

                        @can('SubscriptionPlan Delete')
                            <td class="px-4 py-3 text-center">
                                <input type="checkbox"
                                    wire:model.live="selectedItems.{{ $plan->id }}"
                                    value="{{ $plan->id }}"
                                    class="rounded border-gray-300 dark:border-gray-600">
                            </td>
                        @endcan

                        <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-300">{{ $plan->id }}</td>

                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200 font-medium">
                            {{ $plan->name }}
                        </td>

                        @if ($columns['interval_label'])
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs
                                             bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                                    <i class="fa-regular fa-clock text-[10px]"></i>
                                    {{ $plan->interval_label }}
                                </span>
                            </td>
                        @endif

                        @if ($columns['order'])
                            <td class="px-4 py-3 text-center text-sm text-gray-600 dark:text-gray-400">{{ $plan->order }}</td>
                        @endif

                        {{-- Contador de productos asociados --}}
                        <td class="px-4 py-3 text-center">
                            @if ($plan->product_templates_count > 0)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs
                                             bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300">
                                    <i class="fa-solid fa-box text-[10px]"></i>
                                    {{ $plan->product_templates_count }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>

                        {{-- Toggle estado --}}
                        <td class="px-4 py-3 text-center">
                            @can('SubscriptionPlan Update')
                                <button wire:click="toggleState({{ $plan->id }})"
                                    title="{{ $plan->active ? 'Desactivar' : 'Activar' }}"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors
                                           focus:outline-none focus:ring-2 focus:ring-offset-2
                                           {{ $plan->active ? 'bg-green-500 focus:ring-green-500' : 'bg-gray-300 dark:bg-gray-600 focus:ring-gray-400' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform
                                                 {{ $plan->active ? 'translate-x-6' : 'translate-x-1' }}">
                                    </span>
                                </button>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $plan->active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' }}">
                                    <i class="fa-solid fa-circle text-[6px]"></i>
                                    {{ $plan->active ? 'Activo' : 'Inactivo' }}
                                </span>
                            @endcan
                        </td>

                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                @can('SubscriptionPlan Update')
                                    <a href="{{ route('admin.subscription-plans.edit', $plan) }}"
                                        class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full
                                               bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-green-600 transition">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Editar</span>
                                    </a>
                                @endcan

                                @can('SubscriptionPlan Delete')
                                    <button onclick="confirmDeletePlan({{ $plan->id }}, '{{ addslashes($plan->name) }}')"
                                        class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full
                                               bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-red-600 transition">
                                        <i class="fa-solid fa-trash"></i>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Eliminar</span>
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fa-solid fa-inbox text-4xl mb-2 block"></i>
                            No se encontraron planes de suscripción
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $plans->links() }}</div>

    @push('scripts')
        <script>
            function confirmDeletePlan(id, name) {
                Swal.fire({
                    title: '¿Eliminar plan?',
                    html: `El plan <strong>"${name}"</strong> será eliminado.<br>
                           <span class="text-sm text-gray-500">No se puede eliminar si está asignado a productos o suscripciones.</span>`,
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

            function confirmDeleteSelectedPlans() {
                Swal.fire({
                    title: '¿Eliminar seleccionados?',
                    text: 'Esta acción eliminará los planes seleccionados.',
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
