<x-layouts.app :title="__('Productos')">
    <div class="flex flex-col gap-6">

        <!-- Breadcrumb -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
            <x-breadcrumb :links="[
                'Dashboard' => 'dashboard',
                'Productos' => '#',
            ]" />
        </div>

        <!-- Contenido -->
        <div class="space-y-6">

            <!-- Encabezado -->

             @livewire('admin.products.index')


        </div>
    </div>
</x-layouts.app>
