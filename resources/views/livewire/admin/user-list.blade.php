<div class="space-y-6">

    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Gestión de Usuarios</h1>
            {{--   <p class="text-gray-500 text-sm">Administra cuentas y roles del sistema</p> --}}
        </div>
        <div class="flex flex-wrap gap-2">
            <!-- Nuevo -->
            <a href="{{ route('admin.users.create') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                <i class="fa-solid fa-plus mr-2"></i> Nuevo Usuario
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

                            {{--  <a href="{{ route('users.export.excel') }}" target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                <i class="fa-solid fa-file-excel mr-2"></i> Exportar Excel
                            </a> --}}

                        </li>
                        <li>
                            {{-- <button
                                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                <i class="fa-solid fa-file-pdf mr-2 text-gray-600"></i> Reporte PDF
                            </button> --}}

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



            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition">
                    <i class="fa-solid fa-eye mr-2"></i> Mostrar
                </button>

                <!-- Dropdown de checkboxes -->
                <div x-show="open" @click.away="open = false" x-transition
                    class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-3 z-50">
                    <p class="text-gray-700 dark:text-gray-300 font-semibold mb-2">Campos</p>
                    <label class="flex items-center mb-1">
                        <input type="checkbox" wire:model.live="columns.address" class="mr-2 rounded border-gray-300">
                        Dirección
                    </label>
                    <label class="flex items-center mb-1">
                        <input type="checkbox" wire:model.live="columns.gender" class="mr-2 rounded border-gray-300">
                        Género
                    </label>
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

    <!-- Indicador de carga -->

    {{-- <div wire:loading class="text-blue-500 text-sm mt-2 flex items-center gap-2">
        <i class="fa-solid fa-spinner fa-spin"></i> Cargando resultados...
    </div> --}}

    <!-- Acciones masivas -->
    {{-- @if (count($selectedUsers) > 0)
        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg flex justify-between items-center">
            <span class="text-sm text-gray-700 dark:text-gray-200">
                {{ count($selectedUsers) }} seleccionados
            </span>
            <button wire:click="deleteSelected" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                <i class="fa-solid fa-trash"></i> Eliminar seleccionados
            </button>
        </div>
    @endif --}}

    {{-- @if (count($selectedUsers) > 0)
        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg flex justify-between items-center">
            <span class="text-sm text-gray-700 dark:text-gray-200">
                {{ count($selectedUsers) }} seleccionados
            </span>
            <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700" onclick="confirmDelete()">
                <i class="fa-solid fa-trash"></i> Eliminar seleccionados
            </button>

        </div>
    @endif --}}

    @if ($this->selectedCount > 0)
        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg flex justify-between items-center sticky top-2 z-50">
            <span class="text-sm text-gray-700 dark:text-gray-200">
                {{-- {{ count($selectedUsers) }} --}} {{ $this->selectedCount }} seleccionados seleccionados
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
                    {{-- <th wire:click="sortBy('id')"
                        class="px-4 py-3 text-center cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        ID
                        @if ($sortField === 'id')
                            <i class="fa-solid {{ $sortDirection === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                        @endif
                    </th> --}}
                    <th wire:click="sortBy('id')"
                        class="px-4 py-3 text-center cursor-pointer text-xs font-semibold uppercase
                        {{ $sortField === 'id' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'text-gray-600 dark:text-gray-300' }}">
                        ID
                        @if ($sortField === 'id')
                            <i class="fa-solid {{ $sortDirection === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                        @endif
                    </th>

                    {{-- <th wire:click="sortBy('name')"
                        class="px-4 py-3 text-center cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Nombre
                        @if ($sortField === 'name')
                            <i class="fa-solid {{ $sortDirection === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                        @endif
                    </th> --}}
                    <th wire:click="sortBy('name')"
                        class="px-4 py-3 text-center cursor-pointer text-xs font-semibold uppercase
                        {{ $sortField === 'name' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'text-gray-600 dark:text-gray-300' }}">
                        Nombre
                        @if ($sortField === 'name')
                            <i class="fa-solid {{ $sortDirection === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                        @endif
                    </th>

                    <th wire:click="sortBy('email')"
                        class="px-4 py-3 text-center cursor-pointer text-xs font-semibold uppercase
                        {{ $sortField === 'email' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'text-gray-600 dark:text-gray-300' }}">
                        Email
                        @if ($sortField === 'email')
                            <i class="fa-solid {{ $sortDirection === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                        @endif
                    </th>

                    {{-- <th wire:click="sortBy('email')"
                        class="px-4 py-3 text-center cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Email
                        @if ($sortField === 'email')
                            <i class="fa-solid {{ $sortDirection === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                        @endif
                    </th> --}}

                    @if ($columns['address'])
                        <th class="px-4 py-3 text-center">Dirección</th>
                    @endif

                    @if ($columns['gender'])
                        <th class="px-4 py-3 text-center">Genero</th>
                    @endif

                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        DNI</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Teléfono</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Local</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Estado</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <!-- Checkbox -->
                        <td class="px-4 py-3 text-center">
                            {{-- Qué hace wire:key Livewire renderiza la interfaz en el navegador y, cuando actualizas datos, vuelve a renderizar la parte afectada. --}}
                            {{-- tambien puedes usar esto --}}
                            {{-- <input type="checkbox" id="user-{{ $user->id }}" wire:key="user-{{ $user->id }}"> --}}
                            <input type="checkbox" wire:model.live="selectedUsers.{{ $user->id }}"
                                wire:key="user-checkbox-{{ $user->id }}">
                        </td>


                        <!-- ID -->
                        <td class="px-4 py-3 text-center">{{ $user->id }}</td>

                        <!-- Nombre con resaltado -->
                        <td class="px-4 py-3 text-center">
                            {!! $search
                                ? preg_replace(
                                    '/(' . preg_quote($search, '/') . ')/i',
                                    '<span class="bg-yellow-200 font-semibold">$1</span>',
                                    e($user->name),
                                )
                                : e($user->name) !!}
                        </td>

                        <!-- Email con resaltado -->
                        <td class="px-4 py-3 text-center">
                            {!! $search
                                ? preg_replace(
                                    '/(' . preg_quote($search, '/') . ')/i',
                                    '<span class="bg-yellow-200 font-semibold">$1</span>',
                                    e($user->email),
                                )
                                : e($user->email) !!}
                        </td>

                        @if ($columns['address'])
                            <td class="px-4 py-3 text-center">{{ $user->employee->address ?? '---' }}</td>
                        @endif

                        @if ($columns['gender'])
                            {{-- <td class="px-4 py-3 text-center"> {{ $user->employee->gender == 1 ? 'Femenino' : 'Masculino' }}</td> --}}
                            {{-- <td class="px-4 py-3 text-center"> {{ $user->employee->gender == 1 ? 'F' : 'M' }}</td> --}}
                            <td class="px-4 py-3 text-center">{{ $user->employee->gender_text }}</td>
                        @endif

                        <!-- Otros campos -->
                        <td class="px-4 py-3 text-center">{{ $user->employee->dni ?? '---' }}</td>
                        <td class="px-4 py-3 text-center">{{ $user->employee->movil ?? '---' }}</td>
                        <td class="px-4 py-3 text-center">{{ $user->employee->local->name ?? '---' }}</td>

                        <!-- Estado -->
                        <td class="px-4 py-3 text-center">
                            @if ($user->employee && $user->employee->state)
                                <span wire:click="toggleStatus({{ $user->id }})"
                                    class="cursor-pointer px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Activo</span>
                            @else
                                <span wire:click="toggleStatus({{ $user->id }})"
                                    class="cursor-pointer px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Inactivo</span>
                            @endif
                        </td>

                        <!-- Acciones -->
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">

                                <!-- Ver -->
                                {{-- recomendable usar button u no la etiqueta a --}}
                                <button wire:click="editpassword({{ $user->id }})"
                                    class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-blue-600 transition">
                                    <i class="fa-solid fa-key"></i>
                                    <span
                                        class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Password</span>
                                </button>
                                <!-- Ver -->
                                <a href="{{ route('admin.users.show', $user) }}"
                                    class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-blue-600 transition">
                                    <i class="fa-solid fa-eye"></i>
                                    <span
                                        class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Ver</span>
                                </a>
                                <!-- Editar -->
                                <a href="{{ route('admin.users.edit', $user) }}"
                                    class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-green-600 transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span
                                        class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Editar</span>
                                </a>
                                <!-- Eliminar -->
                                {{-- <button onclick="confirmDeleteSingle({{ $user->id }})" --}}
                                <button
                                    onclick="confirmDeletesimple({{ $user->id }}, @js($user->name), 'deleteSingle', 'Este usuario {{ $user->name }} con ID {{ $user->id }} será eliminado.')"
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
    <div class="mt-4">{{ $users->links() }}</div>


<x-modal wire:model="showEditPassword">
    <x-slot name="title">Cambio de Contraseña</x-slot>

    <!-- Datos del usuario -->
    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4 mb-4">
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-1">
            <i class="fa-solid fa-user text-blue-500 mr-1"></i>
            <span class="font-semibold text-gray-800 dark:text-white">Nombre:</span>
            {{ $name }}
        </p>
        <p class="text-sm text-gray-600 dark:text-gray-300">
            <i class="fa-solid fa-envelope text-blue-500 mr-1"></i>
            <span class="font-semibold text-gray-800 dark:text-white">Email:</span>
            {{ $email }}
        </p>
    </div>

    <!-- Nueva contraseña -->
    <div class="space-y-4">
        <div>
            <x-label for="password">Nueva Contraseña</x-label>
            <flux:input type="password" name="password" viewable  wire:model.defer="password" />
            <flux:error name="password" />
        </div>

        <div>
            <x-label for="password_confirmation">Repetir Contraseña</x-label>
            <flux:input type="password" name="password_confirmation" viewable
                wire:model.defer="password_confirmation" />
            <flux:error name="password_confirmation" />
        </div>

        <!-- Checkbox para enviar correo -->
        <div class="flex items-center mt-2">
            <input type="checkbox" wire:model.defer="sendEmail" id="sendEmail" class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
            <label for="sendEmail" class="ml-2 text-sm text-gray-700 dark:text-gray-200">
                Enviar correo de notificación
            </label>
        </div>
    </div>

    <x-slot name="footer">
        <button type="button" wire:click="updatePassword"
            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
            Cambiar Contraseña
        </button>

        <button type="button" wire:click="closeModal"
            class="ml-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded">
            Cancelar
        </button>
    </x-slot>
</x-modal>



    {{--     <x-modal wire:model="showEditPassword">
        <x-slot name="title">Cambio de Password</x-slot>

        <div class="space-y-4">
            <div>
                <x-label value="name" />
                <x-input wire:model="name" type="text" disabled class="w-full" />
                <x-input-error for="name" />
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <x-label value="email" />
                <x-input wire:model="email" type="text" disabled class="w-full" />
                <x-input-error for="email" />
            </div>
        </div>

        <div>
            <x-label for="password">Contraseña:</x-label>
            <flux:input type="password" name="password" viewable />
            <flux:error name="password" />

        </div>

        <div>
            <x-label for="password">Repetir Contraseña:</x-label>
            <flux:input type="password" name="password_confirmation" viewable />

        </div>

        <x-slot name="footer">
            <button type="button" wire:click="updatePassword"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                Cambiar Password
            </button>

            <button type="button" wire:click="closeModal"
                class="ml-2 px-4 py-2 bg-black hover:bg-gray-800 text-white rounded">
                Cancelar
            </button>
        </x-slot>
    </x-modal> --}}


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
