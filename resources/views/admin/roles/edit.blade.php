<x-layouts.app :title="__('Editar Rol')">
    <div class="flex flex-col gap-6">
        <!-- Breadcrumb -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-4">
            <x-breadcrumb :links="[
                'Dashboard' => route('dashboard'),
                'Roles' => route('admin.roles.index'),
                'Editar Rol' => '#',
            ]" />
        </div>

        <!-- Encabezado -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                    <i class="fa-solid fa-user-shield text-primary"></i> Editando Rol: {{ $role->display_name }}
                </h1>
            </div>
        </div>

        <!-- Formulario -->
        <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Información del Rol -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Información del Rol</h3>
                <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">Modifica los datos del rol seleccionado.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-label value="Nombre del sistema (name):" />
                        <x-input name="name" value="{{ old('name', $role->name) }}" placeholder="Ej: admin" class="w-full" />
                        <x-input-error for="name" />
                    </div>

                    <div>
                        <x-label value="Nombre para mostrar (display_name):" />
                        <x-input name="display_name" value="{{ old('display_name', $role->display_name) }}" placeholder="Ej: Administrador" class="w-full" />
                        <x-input-error for="display_name" />
                    </div>
                </div>
            </div>

            <!-- Permisos -->
            @php
                $oldPermissions = collect(old('permissions', $role->permissions->pluck('name')->toArray()));
            @endphp

            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Permisos Asociados</h3>

                @foreach($permissions as $module => $modulePermissions)
                    <div class="mb-6 border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-gray-50 dark:bg-gray-800">
                        <h4 class="text-base font-semibold text-gray-800 dark:text-white mb-4">
                            {{ ucfirst($module) }}
                        </h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                            @foreach($modulePermissions as $permission)
                                <label class="flex items-start gap-3 text-sm text-gray-700 dark:text-gray-200">
                                    <input
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $permission->name }}"
                                        class="mt-1.5 w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        {{ $oldPermissions->contains($permission->name) ? 'checked' : '' }}
                                    >
                                    <span>{{ $permission->display_name ?? $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Botón de acción -->
            <div class="flex justify-end gap-2">
                <x-button type="submit" class="w-full md:w-auto">
                    <i class="fa-regular fa-floppy-disk mr-2"></i> Actualizar Rol
                </x-button>
                <flux:button variant="danger" href="{{ route('admin.roles.index') }}">Cancelar</flux:button>
            </div>
        </form>
    </div>
</x-layouts.app>
