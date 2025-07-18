


<x-layouts.app :title="__('Dashboard')">
    <div class="flex flex-col gap-6">
        {{-- Título del dashboard --}}
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 text-center">
             <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
            <x-breadcrumb :links="[
                'Dashboard' => route('dashboard'),
                'Compras' => route('dashboardcompras'),
            ]" />
            </div>
        </h2>

        {{-- Grilla de tarjetas --}}
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                {{-- Tarjeta Genérica --}}
                @php
                    $items = [
                        ['title' => 'Proveedores', 'img' => 'configuraciones.jpg', 'route' => route('dashboardconfiguraciones')],
                        ['title' => 'Presupuestos', 'img' => 'compras.jpg', 'route' => route('dashboardcompras')],
                        ['title' => 'Factura Compras', 'img' => 'customers.jpg', 'route' => route('dashboardventas')],
                        ['title' => 'Productos', 'img' => 'pos.jpg', 'route' => '#'],
                        ['title' => 'Reportes', 'img' => 'ecommerce.jpg', 'route' => '#'],
                        ['title' => 'Configuración Compras', 'img' => 'inventario.jpg', 'route' => '#'],

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
        <div class="mt-4 bg-white dark:bg-gray-900 rounded-xl shadow">
            <p class="p-4 text-center text-gray-600 dark:text-gray-300">
                TICOM SOFTWARE - FACTURACIÓN ELECTRÓNICA
            </p>
        </div>
    </div>
</x-layouts.app>
