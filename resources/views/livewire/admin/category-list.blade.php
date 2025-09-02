<div class="space-y-6">

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => 'dashboard',
            'Categorias' => '#',
        ]" />
    </div>

    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Gestión de Categorias</h1>
        </div>

        <div class="flex flex-wrap gap-2">
            <!-- Nuevo -->
            <a href="{{ route('category.create') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                <i class="fa-solid fa-plus mr-2"></i> Nueva categoría
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
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-wrap gap-4 justify-between items-center">
        <!-- Buscar -->
        <div class="relative w-full md:w-1/3">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar Categoría..."
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
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-hidden">
        {{-- <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto"> --}}

        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

            <!-- Encabezado de la tabla -->
            <div class="items-center px-6 py-4 bg-gray-200 sm:flex">
            </div>

            <!-- Comprobación de existencia de categorías -->
            @if (count($categories))
                <div class="mt-4">
                    <ul>
                        <!-- Iteración sobre las categorías -->
                        @foreach ($categories as $category)
                            <!-- Div contenedor con estado -->
                            <div x-data="{ open: false }">
                                <!-- Elemento de categoría con iconos de expansión y contracción -->
                                <div @click="open = !open" class="flex items-center justify-between cursor-pointer">
                                    <div class="flex">
                                        <i class="fas fa-plus" x-show="!open"></i>
                                        <i class="fas fa-minus" x-show="open"></i>
                                        <div class="flex-shrink-0 h-10 ml-2 mr-2 w-15 ">
                                            @if ($category->image)
                                                <img class="object-cover w-16 h-8 rounded-sm"
                                                    src="{{ Storage::disk('s3')->url($category->image) }}"
                                                    alt="{{ $category->name }}">
                                            @else
                                                <img class="object-cover h-8 w-16"
                                                    src="{{ asset('img/categorydefault.jpg') }}"
                                                    alt="{{ $category->name }}" class="m-2">
                                            @endif
                                        </div>
                                        {{ $category->name }}
                                        ({{ $category->children->count() }})
                                        <!-- Nombre de la categoría -->
                                    </div>



                                    <div
                                        class="px-6 text-sm font-medium text-right whitespace-nowrap flex items-center gap-2">
                                        <!-- State -->
                                        <span
                                            class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Activo</span>

                                        <!-- Editar -->
                                        <a href="{{ route('category.edit', $category->id) }}" class="btn btn-green">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <!-- Eliminar -->
                                        {{-- <form method="POST" action="" style="display:inline">
                                            {{ csrf_field() }} {{ method_field('DELETE') }}
                                            <button class="btn btn-red"
                                                onclick="return confirm('¿Estas seguro de querer eliminar la Categoria?')">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form> --}}



                                        <button
                                            onclick="confirmDeletesimple({{ $category->id }}, @js($category->name), 'deleteSingle', 'La Categoría {{ $category->name }} con ID {{ $category->id }} será eliminado.')"
                                            class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-red-600 transition">
                                            <i class="fa-solid fa-trash"></i>
                                            <span
                                                class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Eliminar</span>
                                        </button>



                                    </div>

                                </div>


                                <hr class="w-full my-2 border-t border-gray-300 dotted">
                                <!-- Lista de hijos de la categoría, mostrada si está abierta -->
                                <ul x-show="open">
                                    <!-- Iteración sobre los hijos de la categoría empesando por la categoria padre -->
                                    @foreach ($category->children as $child)
                                        <!-- Componente de categoría para cada hijo -->
                                        <livewire:admin.category-itemlist :category="$child" :key="$child->id" />
                                    @endforeach
                                </ul>

                            </div>
                        @endforeach
                    </ul>
                </div>
            @else
                <!-- Mensaje si no hay categorías -->
                <div class="px-6 py-4">
                    No hay ningún registro coincidente
                </div>
            @endif

            {{-- Comprobación de paginación de categorías --}}
            {{--  @if ($categories->hasPages())
                <div class="px-6 py-8">
                    {{ $categories->links() }}
                </div>
            @endif --}}
        </table>

    </div>


</div>
