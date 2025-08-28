<div class="space-y-6">

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => 'dashboard',
            'Permisos' => '#',
        ]" />
    </div>


    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Gestión de Permisos</h1>
            {{--   <p class="text-gray-500 text-sm">Administra cuentas y roles del sistema</p> --}}
        </div>


    </div>
    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-wrap gap-4 justify-between items-center">
        <!-- Buscar -->
        <div class="relative w-full md:w-1/3">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar permiso..."
                class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring focus:ring-blue-500" />
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fa-solid fa-search"></i>
            </span>
        </div>
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
                        Display</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Modelo</th>

                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                        Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($permissions as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">

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
                        <!-- Otros campos -->
                        <td class="px-4 py-3 text-center">{{ $user->display_name }}</td>
                        <td class="px-4 py-3 text-center">{{ $user->model_name }}</td>
                        <!-- Acciones -->
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <!-- Editar -->



                                <button wire:click="editt({{ $user->id }})"
                                    class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-green-600 transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span
                                        class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Editar</span>
                                </button>



                                {{-- <flux:modal.trigger name="edit-profile">
                                    <flux:button wire:click="edit({{ $user->id }})" variant="ghost"
                                        class="text-green-600 hover:text-green-800 p-2">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </flux:button>
                                </flux:modal.trigger> --}}

                                <flux:modal.trigger name="edit-profile">
                                    <flux:button wire:click="edit({{ $user->id }})" variant="ghost"
                                        class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-green-600 transition">
                                        <i class="fa-solid fa-pen-to-square text-green-600"></i>
                                        <span
                                            class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">
                                            Editar
                                        </span>
                                    </flux:button>
                                </flux:modal.trigger>
                                <!-- Eliminar -->
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
    <div class="mt-4">{{ $permissions->links() }}</div>


    <x-modal wire:model="showEditModal">
        <x-slot name="title">Editando Permiso</x-slot>

        <div class="space-y-4">
            <div>
                <x-label value="Display Name" />
                <x-input wire:model="display_name" type="text" class="w-full" />
                <x-input-error for="display_name" />
            </div>
        </div>


        <x-slot name="footer">
            <button type="button" wire:click="updateDisplayName"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                Actualizar
            </button>

            <button type="button" wire:click="closeModal"
                class="ml-2 px-4 py-2 bg-black hover:bg-gray-800 text-white rounded">
                Cancelar
            </button>
        </x-slot>
    </x-modal>



    <flux:modal name="edit-profile" class="md:w-96" @close="clearErrors">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Actualizar Permiso</flux:heading>
            </div>

            <flux:input label="Permiso" wire:model.live="display_name" />

            <!-- Botón Cancelar -->
            <div class="flex gap-2">
                <flux:spacer />

                <!-- Botón Cancelar -->
                <flux:modal.close>
                    <flux:button variant="primary">Cancelar</flux:button>
                </flux:modal.close>

                <!-- Botón Guardar -->
                <flux:button type="button" wire:click="update" variant="danger">Guardar Cambios</flux:button>
            </div>
        </div>
    </flux:modal>



    @push('scripts')
        <script>
            document.addEventListener('livewire:navigated', () => {
                Livewire.on('close-modal', ({
                    name
                }) => {
                    Flux.modal(name).close();
                });

                Livewire.on('notify', ({
                    type,
                    message
                }) => {
                    Swal.fire({
                        icon: type,
                        title: message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                    });
                });
            });


            /* document.addEventListener('livewire:navigated', () => {
                Livewire.on('show-swal', ({
                    title,
                    text,
                    icon
                }) => {
                    Swal.fire({
                        title: title,
                        text: text,
                        icon: icon,
                        confirmButtonColor: '#3085d6',
                    });
                });
            }); */


            document.addEventListener('livewire:navigated', () => {
                Livewire.on('show-swal', ({
                    title,
                    text,
                    icon
                }) => {
                    Swal.fire({
                        title: title ?? 'Permiso actualizado',
                        text: text ?? '',
                        icon: icon ?? 'success',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#3085d6',
                    });
                });
            });

            /*  Livewire.on('show-toast', ({
                 icon,
                 title
             }) => {
                 Swal.fire({
                     toast: true,
                     position: 'top-end',
                     icon: icon,
                     title: title,
                     showConfirmButton: false,
                     timer: 3000,
                     timerProgressBar: true,
                 });
             }); */
        </script>
    @endpush


</div>
