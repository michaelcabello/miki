<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- FORMULARIO -->
    <div
        class="space-y-6 p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
            ✏️ Editar Categoría
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
            <!-- FORMULARIO -->
            <form wire:submit.prevent="update"
                class="space-y-6 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm p-6">

                <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">
                    Editar Categoría
                </h1>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Nombre -->
                    <div>
                        <x-label for="name">Nombre:</x-label>
                        <flux:input wire:model="name" id="name" icon="tag"
                            placeholder="Nombre de la categoría" />
                        <flux:error name="name" />
                    </div>

                    <!-- Slug -->
                    <div>
                        <x-label for="slug">Slug:</x-label>
                        <flux:input wire:model="slug" id="slug" icon="link"
                            placeholder="ej: categoria-productos" />
                        <flux:error name="slug" />
                    </div>

                    <!-- Estado -->
                    <div class="flex items-center mt-6">
                        <input type="checkbox" wire:model="state" id="state" value="1"
                            class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="state" class="ml-2 text-sm text-gray-700 dark:text-gray-200">
                            Categoría activa
                        </label>
                    </div>

                    <!-- Orden -->
                    <div>
                        <x-label for="order">Orden:</x-label>
                        <flux:input type="number" wire:model="order" id="order" placeholder="0">
                            <x-slot:icon>
                                <i class="fa-solid fa-arrow-up-1-9"></i>
                            </x-slot:icon>
                        </flux:input>
                        <flux:error name="order" />
                    </div>

                    <!-- Categoría padre -->
                    <div class="col-span-2">
                        <x-label for="parent_id">Categoría Padre:</x-label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-sitemap absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <select wire:model="parent_id" id="parent_id"
                                class="block w-full pl-10 pr-3 py-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">
                                <option value="">-- Ninguna --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @if ($cat->children->count())
                                        @include('livewire.admin.partials.category-options', [
                                            'children' => $cat->children,
                                            'prefix' => '--',
                                        ])
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <flux:error name="parent_id" />
                    </div>

                    <!-- Imagen -->
                    <div class="col-span-2">
                        <x-label>Imagen:</x-label>
                        @if ($image)
                            <img src="{{ asset($image) }}" alt="Imagen actual" class="h-20 mb-2 rounded">
                        @endif
                        <flux:input type="file" wire:model="newImage" />
                        <flux:error name="newImage" />
                    </div>

                    <!-- Short Description -->
                    <div class="col-span-2">
                        <x-label for="shortdescription" class="font-medium text-gray-700 dark:text-gray-300">
                            Descripción corta:
                        </x-label>
                        <textarea wire:model="shortdescription" id="shortdescription" rows="2"
                            placeholder="Breve descripción de la categoría"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600
               bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200
               focus:border-blue-500 focus:ring-2 focus:ring-blue-500 p-2 resize-none shadow-sm"></textarea>
                        <flux:error name="shortdescription" class="text-red-500 text-sm mt-1" />
                    </div>


                    <!-- Long Description -->
                    <div class="col-span-2">
                        <x-label for="longdescription" class="font-medium text-gray-700 dark:text-gray-300">
                            Descripción larga:
                        </x-label>
                        <textarea wire:model="longdescription" id="longdescription" rows="4"
                            placeholder="Detalles completos de la categoría"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600
               bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200
               focus:border-blue-500 focus:ring-2 focus:ring-blue-500 p-2 resize-none shadow-sm"></textarea>
                        <flux:error name="longdescription" class="text-red-500 text-sm mt-1" />
                    </div>

                    <!-- SEO -->
                    <div class="col-span-2 mt-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">SEO</h3>

                        <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                            <!-- Campo Título -->
                            <div>
                                <x-label for="title">Título:</x-label>
                                <flux:input wire:model="title" id="title" placeholder="Título SEO">
                                    <x-slot:icon>
                                        <i class="fa-solid fa-heading"></i>
                                    </x-slot:icon>
                                </flux:input>
                                <flux:error name="title" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                            <div>
                                <x-label for="keywords">Palabras clave:</x-label>
                                <flux:input wire:model="keywords" id="keywords" icon="key"
                                    placeholder="ej: tienda, ofertas" />
                                <flux:error name="keywords" />
                            </div>
                        </div>

                        <div class="mt-3">
                            <x-label for="description" class="font-medium text-gray-700 dark:text-gray-300">
                                Descripción SEO:
                            </x-label>
                            <textarea wire:model="description" id="description" rows="3" placeholder="Texto que aparecerá en buscadores"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600
               bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200
               focus:border-blue-500 focus:ring-2 focus:ring-blue-500 p-2 resize-none shadow-sm"></textarea>
                            <flux:error name="description" class="text-red-500 text-sm mt-1" />
                        </div>
                    </div>
                </div>

                <!-- Botón -->
                <div class="flex justify-end mt-6">
                    <x-button type="submit" class="w-full md:w-auto">
                        <i class="fa-regular fa-floppy-disk mr-2"></i> Guardar cambios
                    </x-button>
                </div>
            </form>


        </div>

    </div>

    <!-- LISTADO DE CATEGORÍAS -->
    <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700">
        <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-100 flex items-center gap-2">
            📂 Árbol de Categorías
        </h2>

        <ul class="space-y-1 text-sm">
            {{-- @foreach ($categories as $cat)
                @livewire('admin.category-itemedit', ['category' => $cat, 'selectedParent' => $parent_id], key($cat->id))
            @endforeach --}}

            @foreach ($categories as $cat)
                @livewire('admin.category-itemedit', ['category' => $cat, 'selectedParent' => $parent_id, 'depth' => 0], key($cat->id))
            @endforeach

        </ul>
    </div>
</div>
