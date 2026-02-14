<div class="space-y-6">
    <!-- Encabezado con acciones -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Gestión de Marcas</h1>
        </div>
        <div class="flex flex-wrap gap-2">
            <!-- Botón Nuevo -->
            <a href="{{ route('admin.brands.create') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                <i class="fa-solid fa-plus mr-2"></i> Nueva Marca
            </a>

            <!-- Dropdown Acciones -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 transition">
                    <i class="fa-solid fa-ellipsis-vertical mr-2"></i> Acciones
                </button>

                <!-- Menú -->
                <div x-show="open" @click.away="open = false" x-transition
                    class="absolute right-0 mt-2 w-52 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden z-50">
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        <li>
                            <a href="{{ route('admin.brands.export.excel') }}" target="_blank"
                                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 block">
                                <i class="fa-solid fa-file-export mr-2 text-green-600"></i> Exportar Excel
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.brands.export.pdf') }}" target="_blank"
                                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 block">
                                <i class="fa-solid fa-file-pdf mr-2 text-red-600"></i> Reporte PDF
                            </a>
                        </li>
                        <li>
                            <button type="button" onclick="document.getElementById('importFile').click()"
                                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                <i class="fa-solid fa-file-import mr-2 text-orange-600"></i> Importar
                            </button>
                            <form action="{{ route('admin.brands.import') }}" method="POST" enctype="multipart/form-data" id="importForm" class="hidden">
                                @csrf
                                <input type="file" name="file" id="importFile" accept=".csv,.xlsx,.xls"
                                    onchange="document.getElementById('importForm').submit()">
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Dropdown Mostrar Columnas -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition">
                    <i class="fa-solid fa-eye mr-2"></i> Mostrar
                </button>

                <!-- Dropdown de checkboxes -->
                <div x-show="open" @click.away="open = false" x-transition
                    class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-3 z-50">
                    <p class="text-gray-700 dark:text-gray-300 font-semibold mb-2">Columnas</p>
                    <label class="flex items-center mb-1">
                        <input type="checkbox" wire:model.live="columns.slug" class="mr-2 rounded border-gray-300">
                        Slug
                    </label>
                    <label class="flex items-center mb-1">
                        <input type="checkbox" wire:model.live="columns.seo" class="mr-2 rounded border-gray-300">
                        SEO
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-wrap gap-4 justify-between items-center">
        <!-- Buscar -->
        <div class="relative w-full md:w-1/3">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar marca..."
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
        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg flex justify-between items-center sticky top-2 z-50">
            <span class="text-sm text-gray-700 dark:text-gray-200">
                {{ $this->selectedCount }} seleccionado(s)
            </span>
            <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700" onclick="confirmDelete()">
                <i class="fa-solid fa-trash"></i> Eliminar seleccionados
            </button>
        </div>
    @endif

    <!-- Tabla -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <!-- Checkbox seleccionar todos -->
                    <th class="px-4 py-3 text-center">
                        <input type="checkbox" wire:model.live="selectAll"
                            class="rounded border-gray-300 dark:border-gray-600">
                    </th>

                    <!-- ID -->
                    <th wire:click="sortBy('id')"
                        class="px-4 py-3 text-center cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase hover:bg-gray-100 dark:hover:bg-gray-600">
                        ID
                        @if ($sortField === 'id')
                            <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                        @endif
                    </th>

                    <!-- Imagen -->
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Imagen
                    </th>

                    <!-- Nombre -->
                    <th wire:click="sortBy('name')"
                        class="px-4 py-3 text-left cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase hover:bg-gray-100 dark:hover:bg-gray-600">
                        Nombre
                        @if ($sortField === 'name')
                            <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                        @endif
                    </th>

                    <!-- Slug (opcional) -->
                    @if ($columns['slug'])
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            Slug
                        </th>
                    @endif

                    <!-- SEO (opcional) -->
                    @if ($columns['seo'])
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            SEO
                        </th>
                    @endif

                    <!-- Orden -->
                    <th wire:click="sortBy('order')"
                        class="px-4 py-3 text-center cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase hover:bg-gray-100 dark:hover:bg-gray-600">
                        Orden
                        @if ($sortField === 'order')
                            <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                        @endif
                    </th>

                    <!-- Estado -->
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Estado
                    </th>

                    <!-- Acciones -->
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($brands as $brand)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <!-- Checkbox -->
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" wire:model.live="selectedBrands.{{ $brand->id }}"
                                value="{{ $brand->id }}" class="rounded border-gray-300 dark:border-gray-600">
                        </td>

                        <!-- ID -->
                        <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-300">
                            {{ $brand->id }}
                        </td>

                        <!-- Imagen -->
                        <td class="px-4 py-3 text-center">
                            @if ($brand->image)
                                <img src="{{ $brand->image_url }}" alt="{{ $brand->name }}"
                                    class="w-12 h-12 object-cover rounded-lg mx-auto">
                            @else
                                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-lg mx-auto flex items-center justify-center">
                                    <i class="fa-solid fa-image text-gray-400"></i>
                                </div>
                            @endif
                        </td>

                        <!-- Nombre -->
                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200 font-medium">
                            {{ $brand->name }}
                        </td>

                        <!-- Slug (opcional) -->
                        @if ($columns['slug'])
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ $brand->slug }}
                            </td>
                        @endif

                        <!-- SEO (opcional) -->
                        @if ($columns['seo'])
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                <div class="space-y-1">
                                    @if ($brand->title)
                                        <p class="font-semibold">{{ Str::limit($brand->title, 30) }}</p>
                                    @endif
                                    @if ($brand->description)
                                        <p class="text-xs">{{ Str::limit($brand->description, 50) }}</p>
                                    @endif
                                </div>
                            </td>
                        @endif

                        <!-- Orden -->
                        <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-300">
                            {{ $brand->order ?? 0 }}
                        </td>

                        <!-- Estado -->
                        <td class="px-4 py-3 text-center">
                            <button wire:click="toggleStatus({{ $brand->id }})"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2
                                {{ $brand->state ? 'bg-green-500 focus:ring-green-500' : 'bg-gray-300 dark:bg-gray-600 focus:ring-gray-400' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform
                                    {{ $brand->state ? 'translate-x-6' : 'translate-x-1' }}">
                                </span>
                            </button>
                        </td>

                        <!-- Acciones -->
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.brands.edit', $brand) }}"
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                    title="Editar">
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                <button onclick="confirmDeleteSingle({{ $brand->id }}, '{{ $brand->name }}')"
                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                    title="Eliminar">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fa-solid fa-inbox text-4xl mb-2"></i>
                            <p>No se encontraron marcas</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $brands->links() }}
    </div>

    @push('scripts')
        <script>
            // Confirmar eliminación masiva
            function confirmDelete() {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡Esta acción eliminará las marcas seleccionadas!",
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

            // Confirmar eliminación individual
            function confirmDeleteSingle(brandId, brandName) {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `La marca "${brandName}" será eliminada.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.dispatch('deleteSingle', {
                            id: brandId,
                            name: brandName
                        });
                    }
                });
            }

            // Escuchar eventos de Livewire
            document.addEventListener('livewire:init', () => {
                Livewire.on('brandsDeleted', (event) => {
                    Swal.fire(
                        event.title || '¡Eliminado!',
                        event.text || 'Las marcas han sido eliminadas.',
                        event.icon || 'success'
                    );
                });

                Livewire.on('itemDeleted', (event) => {
                    Swal.fire(
                        event.title || '¡Eliminado!',
                        event.text || 'La marca ha sido eliminada.',
                        event.icon || 'success'
                    );
                });

                Livewire.on('show-swalindex', (event) => {
                    Swal.fire(
                        event.title || 'Notificación',
                        event.text || '',
                        event.icon || 'info'
                    );
                });
            });
        </script>
    @endpush
</div>
