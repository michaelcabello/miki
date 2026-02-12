<div class="space-y-6">

    <!-- Breadcrumb -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => route('dashboard'),
            'Listas de precios' => route('admin.pricelists.index'),
            'Reglas: ' . $pricelist->name => '#',
        ]" />
    </div>

    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                Reglas de precios: <span class="text-indigo-600">{{ $pricelist->name }}</span>
            </h1>
            <p class="text-gray-500 dark:text-gray-300 text-sm">
                Crea reglas como Odoo: por cantidad, fechas, producto o variante.
            </p>
        </div>

        <a href="{{ route('admin.pricelists.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700
                  bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    <!-- Filtros + Crear regla -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col gap-4">
        <div class="flex flex-wrap gap-4 justify-between items-center">
            <!-- Buscar -->
            <div class="relative w-full md:w-1/3">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar regla..."
                       class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                              bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring focus:ring-indigo-500" />
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fa-solid fa-search"></i>
                </span>
            </div>

            <div class="flex items-center gap-3">
                <input type="text" wire:model.live.debounce.400ms="productSearch" placeholder="Buscar producto/SKU..."
                       class="rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2" />

                <select wire:model.live="perPage"
                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                    <option value="10">10 registros</option>
                    <option value="25">25 registros</option>
                    <option value="50">50 registros</option>
                </select>
            </div>
        </div>

        <!-- Crear regla (Odoo-like line) -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-x-4 gap-y-3 items-end">

            <!-- Applied On -->
            <div class="md:col-span-2">
                <x-label>Aplicar a</x-label>
                <select wire:model.live="new.applied_on"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                               bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                    <option value="all">Todos</option>
                    <option value="template">Producto</option>
                    <option value="variant">Variante</option>
                </select>
                @error('new.applied_on') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Producto / Variante -->
            <div class="md:col-span-3">
                <x-label>Producto / Variante</x-label>

                @if($new['applied_on'] === 'template')
                    <select wire:model.live="new.product_template_id"
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                   bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                        <option value="">-- Seleccionar producto --</option>
                        @foreach($templates as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                    @error('new.product_template_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                @elseif($new['applied_on'] === 'variant')
                    <select wire:model.live="new.product_variant_id"
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                   bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                        <option value="">-- Seleccionar variante --</option>
                        @foreach($variants as $v)
                            <option value="{{ $v->id }}">{{ $v->sku }}{{ $v->variant_name ? ' · '.$v->variant_name : '' }}</option>
                        @endforeach
                    </select>
                    @error('new.product_variant_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                @else
                    <div class="px-3 py-2.5 rounded-xl border border-dashed border-gray-300 dark:border-gray-600 text-sm text-gray-500">
                        Aplica a todos los productos
                    </div>
                @endif
            </div>

            <!-- Secuencia -->
            <div class="md:col-span-1">
                <x-label>Seq</x-label>
                <input type="number" wire:model.defer="new.sequence"
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                              bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200" />
                @error('new.sequence') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Min Qty -->
            <div class="md:col-span-1">
                <x-label>Mín. Cant</x-label>
                <input type="number" step="0.01" min="1" wire:model.defer="new.min_qty"
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                              bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200" />
                @error('new.min_qty') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Método -->
            <div class="md:col-span-2">
                <x-label>Método</x-label>
                <select wire:model.live="new.compute_method"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                               bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                    <option value="fixed">Precio fijo</option>
                    <option value="discount">% Descuento</option>
                    <option value="formula">Fórmula</option>
                </select>
                @error('new.compute_method') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Valor según método -->
            <div class="md:col-span-2">
                <x-label>Valor</x-label>

                @if($new['compute_method'] === 'fixed')
                    <input type="number" step="0.01" min="0" wire:model.defer="new.fixed_price" placeholder="0.00"
                           class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200" />
                    @error('new.fixed_price') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror

                @elseif($new['compute_method'] === 'discount')
                    <input type="number" step="0.01" min="0" max="100" wire:model.defer="new.percent_discount" placeholder="10 = 10%"
                           class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200" />
                    @error('new.percent_discount') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror

                @else
                    <select wire:model.defer="new.base"
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                   bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                        <option value="price_sale">Base: Precio venta</option>
                        <option value="cost">Base: Costo</option>
                        <option value="other_pricelist">Base: Otra lista</option>
                    </select>

                    @if($new['base'] === 'other_pricelist')
                        <select wire:model.defer="new.base_pricelist_id"
                                class="mt-2 w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            <option value="">-- Elegir lista base --</option>
                            @foreach($allPricelists as $pl)
                                <option value="{{ $pl->id }}">{{ $pl->name }}</option>
                            @endforeach
                        </select>
                        @error('new.base_pricelist_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    @endif
                @endif
            </div>

            <!-- Fechas -->
            <div class="md:col-span-2">
                <x-label>Vigencia</x-label>
                <div class="flex gap-2">
                    <input type="date" wire:model.defer="new.date_start"
                           class="w-1/2 px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200" />
                    <input type="date" wire:model.defer="new.date_end"
                           class="w-1/2 px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200" />
                </div>
                @error('new.date_end') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Activo + botón -->
            <div class="md:col-span-1 flex items-center gap-2">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                    <input type="checkbox" wire:model.defer="new.active" class="w-5 h-5">
                    Activo
                </label>
            </div>

            <div class="md:col-span-1 flex justify-end">
                <button wire:click="addLine"
                        class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg
                               bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition">
                    <i class="fa-solid fa-plus"></i> Agregar
                </button>
            </div>
        </div>

        @if($new['compute_method'] === 'formula')
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 pt-2">
                <div class="md:col-span-3">
                    <x-label>Multiplicador</x-label>
                    <input type="number" step="0.000001" wire:model.defer="new.price_multiplier"
                           class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200" />
                </div>
                <div class="md:col-span-3">
                    <x-label>Recargo</x-label>
                    <input type="number" step="0.01" wire:model.defer="new.price_surcharge"
                           class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200" />
                </div>
                <div class="md:col-span-2">
                    <x-label>Redondeo</x-label>
                    <input type="number" step="0.000001" wire:model.defer="new.rounding"
                           class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200" />
                </div>
                <div class="md:col-span-2">
                    <x-label>Mín. Margen</x-label>
                    <input type="number" step="0.01" wire:model.defer="new.min_margin"
                           class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200" />
                </div>
                <div class="md:col-span-2">
                    <x-label>Máx. Margen</x-label>
                    <input type="number" step="0.01" wire:model.defer="new.max_margin"
                           class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200" />
                </div>
            </div>
        @endif
    </div>

    <!-- Tabla -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/40 text-gray-700 dark:text-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left">Ámbito</th>
                        <th class="px-4 py-3 text-left">Producto/Variante</th>
                        <th class="px-4 py-3 text-right">Mín. Cant</th>
                        <th class="px-4 py-3 text-left">Método</th>
                        <th class="px-4 py-3 text-right">Valor</th>
                        <th class="px-4 py-3 text-center">Activo</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($items as $it)
                        @php $isEditing = !empty($editing[$it->id]); @endphp

                        <tr class="border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50/60 dark:hover:bg-gray-900/30">

                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-200">
                                    {{ $it->applied_on }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                @if($it->applied_on === 'template')
                                    <span class="font-medium">{{ optional($it->productTemplate)->name }}</span>
                                @elseif($it->applied_on === 'variant')
                                    <span class="font-medium">{{ optional($it->productVariant)->sku }}</span>
                                    @if($it->productVariant?->variant_name)
                                        <span class="text-xs text-gray-500"> · {{ $it->productVariant->variant_name }}</span>
                                    @endif
                                @else
                                    <span class="text-gray-500">Todos</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-right">{{ number_format((float)$it->min_qty, 2) }}</td>

                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                    {{ $it->compute_method }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-right">
                                @if($it->compute_method === 'fixed')
                                    {{ number_format((float)$it->fixed_price, 2) }}
                                @elseif($it->compute_method === 'discount')
                                    {{ number_format((float)$it->percent_discount, 2) }}%
                                @else
                                    <span class="text-xs text-gray-500">Fórmula</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex px-2 py-1 rounded-full text-xs
                                    {{ $it->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $it->active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-right">
                                <button wire:click="startEdit({{ $it->id }})"
                                        class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full
                                               bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-green-600 transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Editar</span>
                                </button>

                                <button
                                    onclick="confirmDeletesimple({{ $it->id }}, @js('Regla'), 'deleteSingle', 'Esta regla será eliminada.')"
                                    class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full
                                           bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-red-600 transition ml-2">
                                    <i class="fa-solid fa-trash"></i>
                                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Eliminar</span>
                                </button>
                            </td>
                        </tr>

                        {{-- Edición simple (opcional): si quieres, te lo hago inline completo como AttributeValues --}}
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                No hay reglas. Agrega la primera arriba.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
            {{ $items->links() }}
        </div>
    </div>

</div>
