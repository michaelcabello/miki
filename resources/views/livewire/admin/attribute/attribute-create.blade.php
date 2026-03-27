<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => route('dashboard'),
            'Atributos' => route('admin.attributes.index'),
            'Crear' => '#',
        ]" />
    </div>

    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Crear atributo</h1>
                <p class="text-sm text-gray-500 dark:text-gray-300">
                    Define atributos como <span class="font-medium">Talla</span>,
                    <span class="font-medium">Color</span>,
                    <span class="font-medium">Material</span>, etc.
                    Luego podrás agregar sus valores.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.attributes.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                           bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold transition
                           hover:bg-gray-200 dark:hover:bg-gray-600">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    {{-- Tabs + Content --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">

        {{-- Tabs --}}
        <div class="px-6 pt-5">
            <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700">
                <button type="button" wire:click="setTab('general')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'general'
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-regular fa-file-lines mr-2"></i> Datos
                </button>
            </div>
        </div>

        {{-- Base de clases para inputs --}}
        @php
            $controlBase = "w-full h-12 px-4 rounded-xl border border-gray-300 dark:border-gray-600
                            bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                            focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition";
        @endphp

        {{-- Content --}}
        <div class="p-6 space-y-6">

            @if ($tab === 'general')
                <div class="grid grid-cols-1 md:grid-cols-12 gap-5">

                    {{-- Nombre --}}
                    <div class="md:col-span-6">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input wire:model.live="name" type="text"
                            placeholder="Ej: Talla, Color, Material"
                            class="{{ $controlBase }} mt-2">
                        <p class="text-xs text-gray-500 dark:text-gray-300 mt-1">
                            Se capitaliza automáticamente. Ej: "color" → "Color"
                        </p>
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Orden --}}
                    <div class="md:col-span-3">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Orden <span class="text-red-500">*</span>
                        </label>
                        <input wire:model.defer="order" type="number" min="0" step="1"
                            class="{{ $controlBase }} mt-2" placeholder="0">
                        <p class="text-xs text-gray-500 dark:text-gray-300 mt-1">
                            Orden de presentación en el POS y catálogo.
                        </p>
                        @error('order')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Estado --}}
                    <div class="md:col-span-3">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Estado</label>
                        <div class="mt-2 h-12 flex items-center justify-between gap-4 px-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                <input type="radio" wire:model.defer="state" value="1">
                                <span>Activo</span>
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                <input type="radio" wire:model.defer="state" value="0">
                                <span>Inactivo</span>
                            </label>
                        </div>
                        @error('state')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tip informativo --}}
                    <div class="md:col-span-12">
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                <i class="fa-solid fa-circle-info text-indigo-500 mr-2"></i>
                                ¿Cómo funcionan los atributos?
                            </p>
                            <div class="flex flex-wrap gap-2 text-xs text-gray-600 dark:text-gray-300">
                                <span class="px-2 py-1 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                                    Talla → S, M, L, XL
                                </span>
                                <span class="px-2 py-1 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                                    Color → Rojo, Negro, Blanco
                                </span>
                                <span class="px-2 py-1 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                                    Material → Algodón, Poliéster
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                Al guardar, podrás agregar los valores desde la lista de atributos.
                            </p>
                        </div>
                    </div>

                </div>

                @can('Attribute Create')
                    <div class="pt-2 flex justify-end">
                        <button wire:click="save"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl
                                   bg-gradient-to-r from-indigo-600 to-sky-600 hover:from-indigo-700 hover:to-sky-700
                                   text-white font-semibold shadow-sm transition">
                            <i class="fa-regular fa-floppy-disk"></i> Guardar atributo
                        </button>
                    </div>
                @endcan

            @endif

        </div>
    </div>
</div>
