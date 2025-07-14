<x-layouts.app :title="__('Dashboard')">
    <div class="flex flex-col gap-6">
        {{-- Título del dashboard --}}
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 text-center">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
            <x-breadcrumb :links="[
                'Dashboard' => route('dashboard'),
                'Ventas' => route('dashboardventas'),
            ]" />
            </div>
        </h2>

        {{-- Grilla de tarjetas --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 px-4">
            {{-- Ejemplo de tarjeta de Configuración --}}

            <article>
                <figure>
                    <img class="object-cover w-full rounded-xl h-36" src="{{ asset('img/compras.jpg') }}" alt="Roles">
                </figure>
                <header class="mt-2">
                    <h1 class="text-xl text-center text-gray-700 dark:text-gray-200">
                        <a href="">Clientes</a>
                    </h1>
                </header>
            </article>
            <article>
                <figure>
                    <img class="object-cover w-full rounded-xl h-36" src="{{ asset('img/customers.jpg') }}"
                        alt="Roles">
                </figure>
                <header class="mt-2">
                    <h1 class="text-xl text-center text-gray-700 dark:text-gray-200">
                        <a href="">Cotizaciones</a>
                    </h1>
                </header>
            </article>

            <article>
                <figure>
                    <img class="object-cover w-full rounded-xl h-36" src="{{ asset('img/pos.jpg') }}" alt="Roles">
                </figure>
                <header class="mt-2">
                    <h1 class="text-xl text-center text-gray-700 dark:text-gray-200">
                        <a href="">Comprobante Ventas</a>
                    </h1>
                </header>
            </article>

            <article>
                <figure>
                    <img class="object-cover w-full rounded-xl h-36" src="{{ asset('img/ecommerce.jpg') }}"
                        alt="Roles">
                </figure>
                <header class="mt-2">
                    <h1 class="text-xl text-center text-gray-700 dark:text-gray-200">
                        <a href="">Productos</a>
                    </h1>
                </header>
            </article>

            <article>
                <figure>
                    <img class="object-cover w-full rounded-xl h-36" src="{{ asset('img/inventario.jpg') }}"
                        alt="Roles">
                </figure>
                <header class="mt-2">
                    <h1 class="text-xl text-center text-gray-700 dark:text-gray-200">
                        <a href="">Reportes</a>
                    </h1>
                </header>
            </article>

            <article>
                <figure>
                    <img class="object-cover w-full rounded-xl h-36" src="{{ asset('img/crm.jpg') }}" alt="Roles">
                </figure>
                <header class="mt-2">
                    <h1 class="text-xl text-center text-gray-700 dark:text-gray-200">
                        <a href="">Configuraciones Ventas</a>
                    </h1>
                </header>
            </article>



        </div>

        {{-- Pie de página --}}
        <div class="mt-4 bg-white dark:bg-gray-900 rounded-xl shadow">
            <p class="p-4 text-center text-gray-600 dark:text-gray-300">
                TICOM SOFTWARE - FACTURACIÓN ELECTRÓNICA
            </p>
        </div>
    </div>
</x-layouts.app>
