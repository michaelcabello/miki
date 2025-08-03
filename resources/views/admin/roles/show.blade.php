<x-layouts.app :title="__('Detalle del Rol')">
    <div class="flex flex-col gap-6">
        <!-- Breadcrumb -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-4">
            <x-breadcrumb :links="[
                'Dashboard' => route('dashboard'),
                'Roles' => route('admin.roles.index'),
                'Detalle del Rol' => '#',
            ]" />
        </div>

        <!-- Encabezado -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                <i class="fa-solid fa-user-shield text-primary"></i> Detalle del Rol: {{ $role->display_name }}
            </h1>
        </div>

        <!-- Información del Rol -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Información del Rol</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-label value="Nombre del sistema (name):" />
                    <x-input type="text" value="{{ $role->name }}" disabled
                        class="w-full bg-gray-100 dark:bg-gray-800" />
                </div>
                <div>
                    <x-label value="Nombre para mostrar (display_name):" />
                    <x-input type="text" value="{{ $role->display_name }}" disabled
                        class="w-full bg-gray-100 dark:bg-gray-800" />
                </div>
            </div>
        </div>

        <!-- Permisos asignados -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Permisos Asignados</h3>

            @forelse ($permissions as $module => $modulePermissions)
                <div
                    class="mb-6 border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-gray-50 dark:bg-gray-800">
                    <h4 class="text-base font-semibold text-gray-800 dark:text-white mb-4">
                        {{ ucfirst($module) }}
                    </h4>

                    <ul class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2">
                        @foreach ($modulePermissions as $permission)
                            <li class="text-sm text-gray-700 dark:text-gray-300">
                                <i class="fa-solid fa-check-circle text-green-500 mr-1"></i>
                                {{ $permission->display_name ?? $permission->name }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">Este rol no tiene permisos asignados.</p>
            @endforelse
        </div>

        <!-- Botón de regreso -->
        {{--  <div class="flex justify-end">
            <flux:button variant="secondary" href="{{ route('admin.roles.index') }}">
                <i class="fa-solid fa-arrow-left mr-2"></i> Volver a la lista
            </flux:button>
        </div> --}}
        <div class="flex justify-end">
            <a href="{{ route('admin.roles.index') }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                <i class="fa-solid fa-arrow-left mr-2"></i> Volver a la lista
            </a>
        </div>

    </div>
</x-layouts.app>
