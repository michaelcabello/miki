<div class="space-y-6">

    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Gestión de Almacenes</h1>
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





    <!-- Tabla -->


    <!-- Paginación -->










    @push('scripts')
        <script>

        </script>
    @endpush
</div>

