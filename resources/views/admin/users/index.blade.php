<x-layouts.app :title="__('Usuarios')">
    <div class="flex flex-col gap-6">

        <!-- Breadcrumb -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
            <x-breadcrumb :links="[
                'Dashboard' => '#',
                'Usuarios' => '#',
            ]" />
        </div>

        <!-- Contenido -->
        <div class="space-y-6">

            <!-- Encabezado -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Gestión de Usuarios</h1>
                    {{--   <p class="text-gray-500 text-sm">Administra cuentas y roles del sistema</p> --}}
                </div>
                <div class="flex flex-wrap gap-2">
                    <!-- Nuevo -->
                    <a href="#"
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
                                    <button
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        <i class="fa-solid fa-trash mr-2 text-red-600"></i> Eliminar seleccionados
                                    </button>
                                </li>
                                <li>
                                    <button
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        <i class="fa-solid fa-file-export mr-2 text-green-600"></i> Exportar
                                    </button>
                                </li>
                                <li>
                                    <button
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        <i class="fa-solid fa-file-pdf mr-2 text-gray-600"></i> Reporte PDF
                                    </button>
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



                    <div x-data="{ open: false, columns: { direccion: false, telefono: false, dni: false } }" class="relative">
                        <button @click="open = !open"
                            class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition">
                            <i class="fa-solid fa-eye mr-2"></i> Mostrar
                        </button>

                        <!-- Dropdown de checkboxes -->
                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-3 z-50">
                            <p class="text-gray-700 dark:text-gray-300 font-semibold mb-2">Campos</p>
                            <label class="flex items-center mb-1">
                                <input type="checkbox" x-model="columns.direccion" class="mr-2 rounded border-gray-300">
                                Dirección
                            </label>
                            <label class="flex items-center mb-1">
                                <input type="checkbox" x-model="columns.telefono" class="mr-2 rounded border-gray-300">
                                Teléfono
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" x-model="columns.dni" class="mr-2 rounded border-gray-300"> DNI
                            </label>
                        </div>
                    </div>



                </div>
            </div>


            @livewire('admin.user-list')


        </div>
    </div>
</x-layouts.app>
