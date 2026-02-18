<div class="space-y-6">

    <!-- Encabezado -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Gestión de Partners</h1>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.partners.create') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                <i class="fa-solid fa-plus mr-2"></i> Nuevo Partner
            </a>

            <!-- Acciones -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 transition">
                    <i class="fa-solid fa-ellipsis-vertical mr-2"></i> Acciones
                </button>

                <div x-show="open" @click.away="open = false" x-transition
                    class="absolute right-0 mt-2 w-52 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden z-50">
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        <li>
                            <a href="{{ route('admin.partners.export.excel') }}" target="_blank"
                                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 block">
                                <i class="fa-solid fa-file-export mr-2 text-green-600"></i> Exportar Excel
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.partners.export.pdf') }}" target="_blank"
                                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 block">
                                <i class="fa-solid fa-file-pdf mr-2 text-red-600"></i> Reporte PDF
                            </a>
                        </li>
                        <li>
                            <button type="button" onclick="document.getElementById('importFile').click()"
                                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                <i class="fa-solid fa-file-import mr-2 text-orange-600"></i> Importar
                            </button>

                            <form action="{{ route('admin.partners.import') }}" method="POST" enctype="multipart/form-data" id="importForm" class="hidden">
                                @csrf
                                <input type="file" name="file" id="importFile" accept=".csv,.xlsx,.xls"
                                    onchange="document.getElementById('importForm').submit()">
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Mostrar columnas -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition">
                    <i class="fa-solid fa-eye mr-2"></i> Mostrar
                </button>

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
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar partner..."
                class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring focus:ring-blue-500" />
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fa-solid fa-search"></i>
            </span>
        </div>

        <!-- Scope -->
        <div>
            <select wire:model.live="scope"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                <option value="roots">Solo Partners</option>
                <option value="contacts">Solo Contactos</option>
                <option value="all">Todos</option>
            </select>
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
                    <th class="px-4 py-3 text-center">
                        <input type="checkbox" wire:model.live="selectAll"
                            class="rounded border-gray-300 dark:border-gray-600">
                    </th>

                    <th wire:click="sortBy('id')"
                        class="px-4 py-3 text-center cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase hover:bg-gray-100 dark:hover:bg-gray-600">
                        ID
                        @if ($sortField === 'id')
                            <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                        @endif
                    </th>

                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Imagen
                    </th>

                    <th wire:click="sortBy('name')"
                        class="px-4 py-3 text-left cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase hover:bg-gray-100 dark:hover:bg-gray-600">
                        Nombre
                        @if ($sortField === 'name')
                            <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                        @endif
                    </th>

                    @if ($columns['slug'])
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            Slug
                        </th>
                    @endif

                    @if ($columns['seo'])
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            SEO
                        </th>
                    @endif

                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Tipo
                    </th>

                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Estado
                    </th>

                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Acciones
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($partners as $partner)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">

                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" wire:model.live="selectedPartners.{{ $partner->id }}"
                                value="{{ $partner->id }}" class="rounded border-gray-300 dark:border-gray-600">
                        </td>

                        <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-300">
                            {{ $partner->id }}
                        </td>

                        <td class="px-4 py-3 text-center">
                            @if ($partner->image)
                                <img src="{{ $partner->image_url ?? $partner->image }}" alt="{{ $partner->name }}"
                                    class="w-12 h-12 object-cover rounded-lg mx-auto">
                            @else
                                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-lg mx-auto flex items-center justify-center">
                                    <i class="fa-solid fa-image text-gray-400"></i>
                                </div>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200 font-medium">
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-2">
                                    <span>{{ $partner->name }}</span>

                                    @if(is_null($partner->parent_id) && $partner->children->count() > 0)
                                        <button wire:click="toggleExpanded({{ $partner->id }})"
                                            class="text-xs px-2 py-1 rounded bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600">
                                            {{ $expandedId === $partner->id ? 'Ocultar contactos' : 'Ver contactos (' . $partner->children->count() . ')' }}
                                        </button>
                                    @endif
                                </div>

                                <div class="text-xs text-gray-500 dark:text-gray-300">
                                    {{ $partner->email ?? '—' }}
                                    @if($partner->phone || $partner->mobile)
                                        · {{ $partner->phone ?? $partner->mobile }}
                                    @endif
                                </div>
                            </div>
                        </td>

                        @if ($columns['slug'])
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ $partner->slug ?? '—' }}
                            </td>
                        @endif

                        @if ($columns['seo'])
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                <div class="space-y-1">
                                    @if (!empty($partner->title))
                                        <p class="font-semibold">{{ \Illuminate\Support\Str::limit($partner->title, 30) }}</p>
                                    @endif
                                    @if (!empty($partner->description))
                                        <p class="text-xs">{{ \Illuminate\Support\Str::limit($partner->description, 50) }}</p>
                                    @endif
                                </div>
                            </td>
                        @endif

                        <td class="px-4 py-3 text-center text-xs">
                            @if(!is_null($partner->parent_id))
                                <span class="px-2 py-1 rounded bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                    Contacto
                                </span>
                            @else
                                <span class="px-2 py-1 rounded bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                    Partner
                                </span>
                            @endif

                            @if($partner->is_customer)
                                <span class="ml-2 px-2 py-1 rounded bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                    Cliente
                                </span>
                            @endif
                            @if($partner->is_supplier)
                                <span class="ml-2 px-2 py-1 rounded bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300">
                                    Proveedor
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-center">
                            <button wire:click="toggleStatus({{ $partner->id }})"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2
                                {{ $partner->status ? 'bg-green-500 focus:ring-green-500' : 'bg-gray-300 dark:bg-gray-600 focus:ring-gray-400' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform
                                    {{ $partner->status ? 'translate-x-6' : 'translate-x-1' }}">
                                </span>
                            </button>
                        </td>

                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.partners.edit', $partner) }}"
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                    title="Editar">
                                    <i class="fa-solid fa-edit"></i>
                                </a>

                                <button onclick="confirmDeleteSingle({{ $partner->id }}, '{{ addslashes($partner->name) }}')"
                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                    title="Eliminar">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    {{-- Contactos expandibles SOLO para raíces --}}
                    @if($expandedId === $partner->id && is_null($partner->parent_id))
                        <tr class="bg-gray-50 dark:bg-gray-700/40">
                            <td colspan="9" class="px-6 py-4">
                                @if($partner->children->isEmpty())
                                    <div class="text-sm text-gray-500 dark:text-gray-300">
                                        No tiene contactos.
                                    </div>
                                @else
                                    <div class="space-y-2">
                                        @foreach($partner->children as $contact)
                                            <div class="flex items-center justify-between p-3 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                                <div class="flex items-center gap-3">
                                                    @if ($contact->image)
                                                        <img src="{{ $contact->image_url ?? $contact->image }}" alt="{{ $contact->name }}"
                                                            class="w-10 h-10 object-cover rounded-lg">
                                                    @else
                                                        <div class="w-10 h-10 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                                            <i class="fa-solid fa-user text-gray-400"></i>
                                                        </div>
                                                    @endif

                                                    <div class="flex flex-col">
                                                        <div class="font-semibold text-gray-800 dark:text-gray-100">
                                                            {{ $contact->name }}
                                                            <span class="ml-2 text-xs px-2 py-0.5 rounded bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                                                Contacto
                                                            </span>
                                                            @if($contact->is_customer)
                                                                <span class="ml-2 text-xs px-2 py-0.5 rounded bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                                                    Cliente
                                                                </span>
                                                            @endif
                                                            @if($contact->is_supplier)
                                                                <span class="ml-2 text-xs px-2 py-0.5 rounded bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300">
                                                                    Proveedor
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <div class="text-xs text-gray-600 dark:text-gray-300">
                                                            {{ $contact->email ?? '—' }}
                                                            @if($contact->phone || $contact->mobile || $contact->whatsapp)
                                                                · {{ $contact->phone ?? $contact->mobile ?? $contact->whatsapp }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-3">
                                                    <span class="text-xs px-2 py-1 rounded {{ $contact->status ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                                        {{ $contact->status ? 'Activo' : 'Inactivo' }}
                                                    </span>

                                                    <a href="{{ route('admin.partners.edit', $contact) }}"
                                                        class="text-blue-600 hover:text-blue-800" title="Editar contacto">
                                                        <i class="fa-solid fa-edit"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endif

                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fa-solid fa-inbox text-4xl mb-2"></i>
                            <p>No se encontraron partners</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $partners->links() }}
    </div>

    @push('scripts')
        <script>
            function confirmDelete() {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡Esta acción eliminará los partners seleccionados!",
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

            function confirmDeleteSingle(id, name) {
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
                        @this.dispatch('deleteSingle', { id: id, name: name });
                    }
                });
            }

            document.addEventListener('livewire:init', () => {
                Livewire.on('partnersDeleted', (event) => {
                    Swal.fire(event.title || '¡Eliminado!', event.text || '', event.icon || 'success');
                });

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
