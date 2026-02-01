<div class="space-y-6">

    <!-- Breadcrumb -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => route('dashboard'),
            'Atributos' => route('admin.attributes.index'),
            'Valores: ' . $attribute->name => '#',
        ]" />
    </div>

    <!-- Header -->
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                Valores del atributo: <span class="text-indigo-600">{{ $attribute->name }}</span>
            </h1>
            <p class="text-gray-500 dark:text-gray-300 text-sm">
                Agrega valores como S, M, L, XL o Rojo, Negro, etc. (estilo Odoo).
            </p>
        </div>

        <a href="{{ route('admin.attributes.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700
                  bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    <!-- Filtros + Agregar línea (como Odoo) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col gap-4">
        <div class="flex flex-wrap gap-4 justify-between items-center">
            <!-- Buscar -->
            <div class="relative w-full md:w-1/3">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar valor..."
                    class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring focus:ring-indigo-500" />
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fa-solid fa-search"></i>
                </span>
            </div>

            <div class="flex items-center gap-3">
                <select wire:model.live="perPage"
                    class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                    <option value="10">10 registros</option>
                    <option value="25">25 registros</option>
                    <option value="50">50 registros</option>
                </select>
            </div>
        </div>

        <!-- Agregar una línea (Odoo-like) -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-x-6 gap-y-4 items-end">
            <div class="md:col-span-7">
                <x-label>Valor</x-label>
                <input type="text" wire:model.defer="newName" placeholder="Ej: S / M / L / XL"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
           bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
           focus:ring-4 focus:ring-indigo-500/25 focus:border-indigo-400
           shadow-sm" />

                @error('newName')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-3">
                <x-label>Precio adicional</x-label>
                <input type="number" step="0.01" min="0" wire:model.defer="newExtraPrice" placeholder="0.00"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
           bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
           focus:ring-4 focus:ring-indigo-500/25 focus:border-indigo-400
           shadow-sm" />
            </div>

            <div class="md:col-span-2 flex justify-end">
                <button wire:click="addLine"
                    class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg
                           bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition">
                    <i class="fa-solid fa-plus"></i> Agregar
                </button>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/40 text-gray-700 dark:text-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left">Valor</th>
                        <th class="px-4 py-3 text-right">Precio adicional</th>
                        <th class="px-4 py-3 text-center">Activo</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($values as $v)
                        @php $isEditing = !empty($editing[$v->id]); @endphp

                        <tr
                            class="border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50/60 dark:hover:bg-gray-900/30">
                            <!-- Valor -->
                            <td class="px-4 py-3">
                                @if ($isEditing)
                                    <input type="text" wire:model.defer="name.{{ $v->id }}"
                                        class="w-full py-2 rounded-lg border border-gray-300 dark:border-gray-600
                                                  bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                @else
                                    <span
                                        class="font-medium text-gray-800 dark:text-gray-100">{{ $v->name }}</span>
                                @endif
                            </td>

                            <!-- Precio adicional -->
                            <td class="px-4 py-3 text-right">
                                @if ($isEditing)
                                    <input type="number" step="0.01" min="0"
                                        wire:model.defer="extra_price.{{ $v->id }}"
                                        class="w-32 text-right py-2 rounded-lg border border-gray-300 dark:border-gray-600
                                                  bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                @else
                                    <span class="text-gray-700 dark:text-gray-200">
                                        {{ number_format((float) $v->extra_price, 2) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Activo -->
                            <td class="px-4 py-3 text-center">
                                @if ($isEditing)
                                    <input type="checkbox" wire:model.defer="active.{{ $v->id }}"
                                        class="w-5 h-5">
                                @else
                                    <span
                                        class="inline-flex px-2 py-1 rounded-full text-xs
                                        {{ $v->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $v->active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                @endif
                            </td>

                            <!-- Acciones -->
                            <td class="px-4 py-3 text-right">
                                @if ($isEditing)
                                    <button wire:click="save({{ $v->id }})"
                                        class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition">
                                        <i class="fa-solid fa-check"></i>
                                    </button>

                                    <button wire:click="cancelEdit({{ $v->id }})"
                                        class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 transition ml-2">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                @else
                                    {{-- <button wire:click="startEdit({{ $v->id }})"
                                        class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition">
                                        <i class="fa-solid fa-pen"></i>
                                    </button> --}}


                                    <button wire:click="startEdit({{ $v->id }})"
                                    class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-green-600 transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span
                                        class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Editar</span>
                                    </button>

                                    {{-- <button wire:click="delete({{ $v->id }})"
                                        class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white transition ml-2">
                                        <i class="fa-solid fa-trash"></i>
                                    </button> --}}

                                    <button
                                        onclick="confirmDeletesimple({{ $v->id }}, @js($v->name), 'deleteSingle', 'Este valor {{ $v->name }} con ID {{ $v->id }} será eliminado.')"
                                        class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-red-600 transition">
                                        <i class="fa-solid fa-trash"></i>
                                        <span
                                            class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Eliminar</span>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                No hay valores. Agrega el primero arriba.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
            {{ $values->links() }}
        </div>
    </div>

</div>
