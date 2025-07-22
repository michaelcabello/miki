<div class="space-y-6">
    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-wrap gap-4 justify-between items-center">
        <!-- Buscar -->
        <div class="relative w-full md:w-1/3">
            <input type="text" wire:model.live="search" placeholder="Buscar usuario..."
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

    <!-- Indicador de carga -->
    <div wire:loading class="text-blue-500 text-sm mt-2 flex items-center gap-2">
        <i class="fa-solid fa-spinner fa-spin"></i> Cargando resultados...
    </div>

    <!-- Acciones masivas -->
    @if (count($selectedUsers) > 0)
        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg flex justify-between items-center">
            <span class="text-sm text-gray-700 dark:text-gray-200">
                {{ count($selectedUsers) }} seleccionados
            </span>
            <button wire:click="deleteSelected" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                <i class="fa-solid fa-trash"></i> Eliminar seleccionados
            </button>
        </div>
    @endif

    <!-- Tabla -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th wire:click="sortBy('id')" class="px-4 py-3 text-center cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                    ID
                    @if($sortField === 'id')
                        <i class="fa-solid {{ $sortDirection === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                    @endif
                </th>

                <th wire:click="sortBy('name')" class="px-4 py-3 text-center cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                    Nombre
                    @if($sortField === 'name')
                        <i class="fa-solid {{ $sortDirection === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                    @endif
                </th>

                <th wire:click="sortBy('email')" class="px-4 py-3 text-center cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                    Email
                    @if($sortField === 'email')
                        <i class="fa-solid {{ $sortDirection === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                    @endif
                </th>

                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">DNI</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Teléfono</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Local</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Estado</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse ($users as $user)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <td class="px-4 py-3 text-center">{{ $user->id }}</td>
                    <td class="px-4 py-3 text-center">{{ $user->name }}</td>
                    <td class="px-4 py-3 text-center">{{ $user->email }}</td>
                    <td class="px-4 py-3 text-center">{{ $user->employee->dni ?? '---' }}</td>
                    <td class="px-4 py-3 text-center">{{ $user->employee->movil ?? '---' }}</td>
                    <td class="px-4 py-3 text-center">{{ $user->employee->local->name ?? '---' }}</td>
                    <td class="px-4 py-3 text-center">
                        @if ($user->employee && $user->employee->state)
                            <span wire:click="toggleStatus({{ $user->id }})" class="cursor-pointer px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Activo</span>
                        @else
                            <span wire:click="toggleStatus({{ $user->id }})" class="cursor-pointer px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Inactivo</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-2">
                            <!-- Ver -->
                            <button class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-blue-600 transition">
                                <i class="fa-solid fa-eye"></i>
                                <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Ver</span>
                            </button>
                            <!-- Editar -->
                            <button class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-green-600 transition">
                                <i class="fa-solid fa-pen-to-square"></i>
                                <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Editar</span>
                            </button>
                            <!-- Eliminar -->
                            <button class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-red-600 transition">
                                <i class="fa-solid fa-trash"></i>
                                <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Eliminar</span>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-gray-500 dark:text-gray-400">No hay usuarios</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


    <!-- Paginación -->
    <div class="mt-4">{{ $users->links() }}</div>
</div>
