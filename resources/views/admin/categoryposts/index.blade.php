<x-layouts.app :title="__('Categoria de Los Posts')">
    <div class="flex flex-col gap-6">

        <!-- Breadcrumb -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
            <x-breadcrumb :links="[
                'Dashboard' => 'dashboard',
                'Categoría de los Posts' => '#',
            ]" />
        </div>

        <!-- Contenido -->
        <div class="space-y-6">

            <!-- Encabezado -->



            @livewire('admin.categorypost-list')


        </div>
    </div>
</x-layouts.app>
