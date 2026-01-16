<x-layouts.app :title="__('Warehouses')">
    <div class="flex flex-col gap-6">

        <!-- Breadcrumb -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
            <x-breadcrumb :links="[
                'Dashboard' => 'dashboard',
                'Almacenes' => '#',
            ]" />
        </div>

        <!-- Contenido -->
        <div class="space-y-6">

            <!-- Encabezado -->

            @livewire('admin.warehouses.index')


        </div>
    </div>
</x-layouts.app>
