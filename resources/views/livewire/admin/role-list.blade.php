<div class="space-y-6">

    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Gestión de Roles</h1>
            {{--   <p class="text-gray-500 text-sm">Administra cuentas y roles del sistema</p> --}}
        </div>
        <div class="flex flex-wrap gap-2">
            <!-- Nuevo -->
            <a href="{{ route('admin.roles.create') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                <i class="fa-solid fa-plus mr-2"></i> Nuevo Rol
            </a>

        </div>
    </div>
    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-wrap gap-4 justify-between items-center">
        <!-- Buscar -->
        <div class="relative w-full md:w-1/3">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar rol..."
                class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring focus:ring-blue-500" />
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fa-solid fa-search"></i>
            </span>
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
                        Permisos</th>

                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($roles as $role)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">

                       <!-- ID -->
                        <td class="px-4 py-3 text-center">{{ $role->id }}</td>

                        <!-- Nombre con resaltado -->
                        <td class="px-4 py-3 text-center">
                            {!! $search
                                ? preg_replace(
                                    '/(' . preg_quote($search, '/') . ')/i',
                                    '<span class="bg-yellow-200 font-semibold">$1</span>',
                                    e($role->name),
                                )
                                : e($role->name) !!}
                        </td>

                        <!-- Email con resaltado -->



                        <!-- Otros campos -->
                        <td class="px-4 py-3 text-center">{{ $role->permissions->pluck('display_name')->implode(', ') }}</td>




                        <!-- Acciones -->
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <!-- Ver -->
                                <a href="{{ route('admin.roles.show', $role) }}"
                                    class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-blue-600 transition">
                                    <i class="fa-solid fa-eye"></i>
                                    <span
                                        class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Ver</span>
                                </a>
                                <!-- Editar -->
                                <a href="{{ route('admin.roles.edit', $role) }}" class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-green-600 transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span
                                        class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Editar</span>
                                </a>
                                <!-- Eliminar -->
                                {{-- <button onclick="confirmDeleteSingle({{ $role->id }})" --}}
                                <button onclick="confirmDeletesimple({{ $role->id }}, @js($role->name), 'deleteRole', 'El Rol  {{ $role->name }} con ID {{ $role->id }} será eliminado.')"
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
    <div class="mt-4">{{ $roles->links() }}</div>

    @push('scripts')
        <script>
            /* function confirmDelete() {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡Esta acción eliminará los usuarios seleccionados!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('confirmDeleteSelected'); // Emitimos evento
                    }
                });
            } */

            // Escucha cuando Livewire confirme que se eliminó
            /* document.addEventListener('livewire:navigated', () => {
                Livewire.on('usersDeleted', () => {
                    Swal.fire(
                        '¡Eliminado!',
                        'Los usuarios seleccionados han sido eliminados.',
                        'success'
                    );
                });
            }); */



            /* function confirmDeleteSingle(userId) {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Este usuario será eliminado.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('deleteSingle', {
                            id: userId
                        });
                    }
                });
            } */


            // Escucha cuando Livewire confirme que se eliminó
            /* document.addEventListener('livewire:navigated', () => {
                Livewire.on('userDeleted', () => {
                    Swal.fire(
                        '¡Eliminado!',
                        'El usuario fue eliminado con éxito.',
                        'success'
                    );
                });
            }); */


        </script>
    @endpush
</div>

