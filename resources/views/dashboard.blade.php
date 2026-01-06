<x-layouts.app :title="__('Dashboard')">
    <div class="flex flex-col gap-6">

        {{-- Breadcrumb en una tarjeta --}}
        {{-- <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
            <x-breadcrumb :links="[
                'Dashboard' => route('dashboard'),
            ]" />
        </div> --}}

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-3 py-2 sm:px-6 sm:py-3 w-full overflow-x-auto">
            <div class="flex items-center space-x-2 text-sm sm:text-base whitespace-nowrap">
                <x-breadcrumb :links="[
                    'Dashboard' => route('dashboard'),
                ]" />
            </div>
        </div>

        {{-- Grilla de tarjetas --}}
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-8">
                {{-- Tarjeta Genérica --}}
                @php
                    $items = [
                        [
                            'title' => 'Configuraciones',
                            'img' => 'configuraciones.jpg',
                            'route' => route('dashboardconfiguraciones'),
                        ],
                        ['title' => 'Usuarios', 'img' => 'compras.jpg', 'route' => route('admin.users.index')],
                        ['title' => 'Permisos', 'img' => 'customers.jpg', 'route' => route('admin.permissions.list')],
                        ['title' => 'Roles', 'img' => 'pos.jpg', 'route' => route('admin.roles.index')],
                        ['title' => 'Web', 'img' => 'ecommerce.jpg', 'route' => '#'],
                        ['title' => 'Inventarios', 'img' => 'inventario.jpg', 'route' => '#'],
                        ['title' => 'CRM', 'img' => 'crm.jpg', 'route' => '#'],
                        ['title' => 'Contable', 'img' => 'contable.jpg', 'route' => '#'],
                        ['title' => 'Empleados', 'img' => 'contable.jpg', 'route' => '#'],
                        ['title' => 'Categoría del Post', 'img' => 'contable.jpg', 'route' => route('admin.categoryposts.index')],
                        ['title' => 'Productos', 'img' => 'contable.jpg', 'route' => route('admin.categoryposts.index')],
                        ['title' => 'Categorías', 'img' => 'contable.jpg', 'route' => route('admin.category.list')],
                        ['title' => 'Marca', 'img' => 'contable.jpg', 'route' => route('admin.categoryposts.index')],
                        ['title' => 'Modelo', 'img' => 'contable.jpg', 'route' => route('admin.categoryposts.index')],
                        ['title' => 'Temporada', 'img' => 'contable.jpg', 'route' => route('admin.categoryposts.index')],
                        ['title' => 'Plan Contable', 'img' => 'contable.jpg', 'route' => route('admin.accounts.index')],
                        ['title' => 'Categoría de Posts', 'img' => 'contable.jpg', 'route' => route('admin.categoryposts.index')],

                    ];
                @endphp

                @foreach ($items as $item)
                    {{--  <a href="{{ $item['route'] }}"
                       class="group bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition overflow-hidden flex flex-col items-center text-center p-2 border-none">
                        <img src="{{ asset('img/'.$item['img']) }}"
                             alt="{{ $item['title'] }}"
                             class="rounded-lg object-cover w-40 h-28 mb-4 group-hover:scale-105 transition duration-300">
                        <h1 class="text-lg font-semibold text-gray-700 dark:text-gray-200 group-hover:text-blue-600">
                            {{ $item['title'] }}
                        </h1>
                    </a> --}}

                    <a href="{{ $item['route'] }}"
                        class="group bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-xl hover:scale-105 transition transform overflow-hidden flex flex-col items-center text-center p-2 border-none focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2">
                        {{-- <img src="{{ asset('img/' . $item['img']) }}" alt="{{ $item['title'] }}"
                            class="rounded-lg object-cover w-40 h-28 mb-4 group-hover:scale-110 transition duration-300"> --}}
                        <img src="{{ asset('img/' . $item['img']) }}" alt="{{ $item['title'] }}"
                            class="rounded-lg object-cover w-52 h-36 sm:w-40 sm:h-28 mb-4 group-hover:scale-110 transition duration-300">

                        <h1 class="text-lg font-semibold text-gray-700 dark:text-gray-200 group-hover:text-blue-600">
                            {{ $item['title'] }}
                        </h1>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Pie de página --}}
        <div class="mt-6 bg-white dark:bg-gray-900 rounded-xl shadow">
            <p class="p-4 text-center text-gray-600 dark:text-gray-300">
                TICOM SOFTWARE - FACTURACIÓN ELECTRÓNICA
            </p>
        </div>
    </div>
</x-layouts.app>
