<x-layouts.app :title="__('Crear Rol')">
    <div class="flex flex-col gap-6">

        <!-- Breadcrumb -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-4">
            <x-breadcrumb :links="[
                'Dashboard' => route('dashboard'),
                'Roles' => route('admin.roles.index'),
                'Crear Rol' => '#',
            ]" />
        </div>

        <!-- Encabezado -->
        {{--  <div class="px-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-user-shield text-primary"></i> Crear Nuevo Rol
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">Completa los datos y asigna los permisos al nuevo
                rol.</p>
        </div> --}}

        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100"><i
                        class="fa-solid fa-user-shield text-primary"></i> Creando Nuevo Rol</h1>
                {{--   <p class="text-gray-500 text-sm">Administra cuentas y roles del sistema</p> --}}
            </div>
            {{-- <div class="flex flex-wrap gap-2">

                <a href="{{ route('admin.roles.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                    <i class="fa-solid fa-plus mr-2"></i> Guardar
                </a>

            </div> --}}
        </div>


        <!-- Formulario -->
        <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-6">
            @csrf

            <!-- Información del Rol -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Información del Rol</h3>
                <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">Completa los datos y asigna los permisos al
                    nuevo
                    rol.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-label value="Nombre del sistema (name):" />
                        <x-input name="name" value="{{ old('name') }}" placeholder="Ej: admin" class="w-full" />
                        <x-input-error for="name" />
                    </div>

                    <div>
                        <x-label value="Nombre para mostrar (display_name):" />
                        <x-input name="display_name" value="{{ old('display_name') }}" placeholder="Ej: Administrador"
                            class="w-full" />
                        <x-input-error for="display_name" />
                    </div>
                </div>
            </div>

            <!-- Permisos -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Permisos Asociados</h3>
                {{-- <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"> --}}
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4">
                    @include('admin.permissions.checkboxes', ['model' => $role])
                </div>
            </div>

            <!-- Botón de acción -->
            <div class="flex justify-end">
                <x-button type="submit" class="w-full md:w-auto">
                    <i class="fa-regular fa-floppy-disk mr-2"></i> Guardar Rol
                </x-button>
                <flux:button variant="danger" type="submit"><i class="fa-regular fa-floppy-disk mr-2"></i>Guardar Rol</flux:button>

            </div>



        </form>

    </div>
</x-layouts.app>
