<div class="space-y-6">

    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Gestión de Atributos</h1>
            {{--   <p class="text-gray-500 text-sm">Administra cuentas y roles del sistema</p> --}}
        </div>
        <div class="flex flex-wrap gap-2">
            <!-- Nuevo -->
            <a href="{{ route('admin.attributes.create') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                <i class="fa-solid fa-plus mr-2"></i> Nuevo Atributo
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
                            <a href="{{ route('users.export.excel') }}" target="_blank"
                                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                <i class="fa-solid fa-file-export mr-2 text-green-600"></i> Exportar
                            </a>
                        </li>
                        <li>

                            <a href="{{ route('users.export.pdf') }}" target="_blank"
                                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                <i class="fa-solid fa-file-pdf mr-2 text-gray-600"></i> Reporte PDF
                            </a>

                        </li>
                        <li>
                            <label
                                class="w-full flex items-center px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 cursor-pointer">
                                <i class="fa-solid fa-file-import mr-2 text-orange-600"></i> Importar
                                <input type="file" accept=".csv, .xlsx" class="hidden" />
                            </label>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-wrap gap-4 justify-between items-center">
        <!-- Buscar -->
        <div class="relative w-full md:w-1/3">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar usuario..."
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

        <!-- Cantidad -->
        <div>
            <select wire:model.live="perPage"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                <option value="10">10 registros</option>
                <option value="25">25 registros</option>
                <option value="50">50 registros</option>
            </select>
        </div>
    </div>





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
                        class="px-4 py-3 text-center cursor-pointer text-xs font-semibold uppercase
                        {{ $sortField === 'id' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'text-gray-600 dark:text-gray-300' }}">
                        ID
                        @if ($sortField === 'id')
                            <i class="fa-solid {{ $sortDirection === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                        @endif
                    </th>


                    <th wire:click="sortBy('name')"
                        class="px-4 py-3 text-center cursor-pointer text-xs font-semibold uppercase
                        {{ $sortField === 'name' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'text-gray-600 dark:text-gray-300' }}">
                        Nombre
                        @if ($sortField === 'name')
                            <i class="fa-solid {{ $sortDirection === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                        @endif
                    </th>


                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Estado</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($attributes as $attribute)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <!-- Checkbox -->
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" wire:model.live="selectedUsers.{{ $attribute->id }}"
                                wire:key="user-checkbox-{{ $attribute->id }}">
                        </td>

                        <!-- ID -->
                        <td class="px-4 py-3 text-center">{{ $attribute->id }}</td>

                        <!-- Nombre con resaltado -->
                        <td class="px-4 py-3 text-center">
                          <a href={{ route('admin.attributes.values', $attribute) }} class="text-blue-600 hover:underline">{{$attribute->name}}</a>
                        </td>


                        <!-- Estado -->
                        <td class="px-4 py-3 text-center">
                            @if ($attribute->state)
                                <span wire:click="toggleStatus({{ $attribute->id }})"
                                    class="cursor-pointer px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Activo</span>
                            @else
                                <span wire:click="toggleStatus({{ $attribute->id }})"
                                    class="cursor-pointer px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Inactivo</span>
                            @endif
                        </td>

                        <!-- Acciones -->
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">


                                <!-- Ver -->
                                <a href="{{ route('admin.users.show', $attribute) }}"
                                    class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-blue-600 transition">
                                    <i class="fa-solid fa-eye"></i>
                                    <span
                                        class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Ver</span>
                                </a>
                                <!-- Editar -->
                                <a href="{{ route('admin.users.edit', $attribute) }}"
                                    class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-green-600 transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span
                                        class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Editar</span>
                                </a>
                                <!-- Eliminar -->
                                {{-- <button onclick="confirmDeleteSingle({{ $attribute->id }})" --}}
                                <button
                                    onclick="confirmDeletesimple({{ $attribute->id }}, @js($attribute->name), 'deleteSingle', 'Este usuario {{ $attribute->name }} con ID {{ $attribute->id }} será eliminado.')"
                                    class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-red-600 transition">
                                    <i class="fa-solid fa-trash"></i>
                                    <span
                                        class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Eliminar</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-gray-500 dark:text-gray-400">No hay usuarios
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4">{{ $attributes->links() }}</div>







    @push('scripts')
        <script>

        </script>
    @endpush
</div>
