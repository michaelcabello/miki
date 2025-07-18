<x-layouts.app :title="__('Dashboard')">
    <div class="flex flex-col gap-6">

        {{-- Breadcrumb en una tarjeta --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
            <x-breadcrumb :links="[
                'Dashboard' => route('dashboard'),
            ]" />
        </div>

        {{-- Grilla de tarjetas --}}
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                {{-- Tarjeta Genérica --}}
                @php
                    $items = [
                        ['title' => 'Configuraciones', 'img' => 'configuraciones.jpg', 'route' => route('dashboardconfiguraciones')],
                        ['title' => 'Compras', 'img' => 'compras.jpg', 'route' => route('dashboardcompras')],
                        ['title' => 'Ventas', 'img' => 'customers.jpg', 'route' => route('dashboardventas')],
                        ['title' => 'Punto de Venta', 'img' => 'pos.jpg', 'route' => '#'],
                        ['title' => 'Web', 'img' => 'ecommerce.jpg', 'route' => '#'],
                        ['title' => 'Inventarios', 'img' => 'inventario.jpg', 'route' => '#'],
                        ['title' => 'CRM', 'img' => 'crm.jpg', 'route' => '#'],
                        ['title' => 'Contable', 'img' => 'contable.jpg', 'route' => '#'],
                        ['title' => 'Empleados', 'img' => 'contable.jpg', 'route' => '#'],
                    ];
                @endphp

                @foreach ($items as $item)
                    <a href="{{ $item['route'] }}"
                       class="group bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition overflow-hidden flex flex-col items-center text-center p-4">
                        <img src="{{ asset('img/'.$item['img']) }}"
                             alt="{{ $item['title'] }}"
                             class="rounded-lg object-cover w-40 h-28 mb-4 group-hover:scale-105 transition duration-300">
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
