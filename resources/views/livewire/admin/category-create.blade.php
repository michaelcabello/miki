<div class="flex flex-col gap-6">
    <!-- Breadcrumb -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-4">
        <x-breadcrumb :links="[
            'Dashboard' => route('dashboard'),
            'Categorías' => route('admin.category.list'),
            'Crear Categoría' => '#',
        ]" />
    </div>

    <!-- Encabezado -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
            <i class="fa-solid fa-layer-group text-primary"></i> Creando Nueva Categoría
        </h1>
    </div>

    <!-- Datos de la Categoría -->
    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm p-6 space-y-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Datos de la Categoría</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Columna izquierda -->
            <div class="space-y-4">
                <div>
                    <x-label for="name">Nombre de la categoría:</x-label>
                    <flux:input icon="tag" placeholder="Categoría" wire:model.defer="name" />
                    <x-input-error for="name" />
                </div>

                <div>
                    <x-label for="shortdescription">Descripción corta:</x-label>
                    <textarea rows="2" wire:model.defer="shortdescription"
                        class="block w-full rounded-lg border dark:bg-gray-900"></textarea>
                </div>

                <div>
                    <x-label for="longdescription">Descripción larga:</x-label>
                    <textarea rows="4" wire:model.defer="longdescription"
                        class="block w-full rounded-lg border dark:bg-gray-900"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-label for="order">Orden:</x-label>
                        <flux:input type="number" icon="hashtag" wire:model.defer="order" placeholder="0" />
                    </div>

                    <div>
                        <x-label for="image">Imagen:</x-label>
                        <flux:input type="file" wire:model="image" />
                        @if ($image)
                            <img class="mt-2 rounded-lg shadow max-h-48" src="{{ $image->temporaryUrl() }}" />
                        @endif
                    </div>
                </div>
            </div>

            <!-- Columna derecha -->
            <div class="space-y-3">
                <h4 class="text-base font-semibold text-gray-800 dark:text-white">Categoría padre</h4>

                <div class="rounded-xl border bg-white dark:bg-gray-900 p-4">
                    <p class="text-sm">Ruta: {{ $breadcrumbs }}</p>

                    @if (count($categories))
                        <ul class="space-y-2">
                            @foreach ($categories as $category)
                                <livewire:admin.category-item :category="$category"
                                    wire:model="selectedParentCategory"
                                    :key="$category->id" />
                            @endforeach
                        </ul>
                    @else
                        <p>No hay categorías disponibles.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-2">
            <x-button wire:click="save">
                <i class="fa-regular fa-floppy-disk mr-2"></i> Crear Categoría
            </x-button>
        </div>
    </div>
</div>
