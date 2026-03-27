<div class="space-y-6">

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => route('dashboard'),
            'Listas de precios' => route('admin.pricelists.index'),
            'Reglas: ' . $pricelist->name => '#',
        ]" />
    </div>

    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                Reglas de precios: <span class="text-indigo-600">{{ $pricelist->name }}</span>
            </h1>
            <p class="text-gray-500 dark:text-gray-300 text-sm">
                {{ isset($new['id']) ? 'Editando regla seleccionada.' : 'Crea reglas como Odoo: por cantidad, fechas, producto o variante.' }}
            </p>
        </div>

        <a href="{{ route('admin.pricelists.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700
                  bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col gap-4 border-2 transition-all duration-300 {{ isset($new['id']) ? 'border-indigo-500 ring-1 ring-indigo-500 shadow-lg' : 'border-transparent' }}">

        <div class="flex flex-wrap gap-4 justify-between items-center">
            <div class="relative w-full md:w-1/3">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar regla..."
                    class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                           bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring focus:ring-indigo-500" />
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fa-solid fa-search"></i>
                </span>
            </div>

            <div class="flex items-center gap-3">
                <input type="text" wire:model.live.debounce.400ms="productSearch"
                    placeholder="Buscar producto/SKU..."
                    class="rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm px-3 py-2" />

                <select wire:model.live="perPage"
                    class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                    <option value="10">10 registros</option>
                    <option value="25">25 registros</option>
                    <option value="50">50 registros</option>
                </select>
            </div>
        </div>

        <hr class="dark:border-gray-700">

        <div class="grid grid-cols-1 md:grid-cols-12 gap-x-4 gap-y-3 items-end">

            <div class="md:col-span-2">
                <x-label>Aplicar a</x-label>
                <select wire:model.live="new.applied_on"
                    class="w-full px-3 py-2.5 rounded-xl border @error('new.applied_on') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                    <option value="all">Todos</option>
                    <option value="category">Categoría</option>
                    <option value="template">Producto</option>
                    <option value="variant">Variante</option>
                </select>
                @error('new.applied_on')
                    <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span>
                @enderror
            </div>



            <div class="md:col-span-3">
                <x-label>Selección</x-label>
                @if ($new['applied_on'] === 'category')
                    <select wire:model.live="new.category_id"
                        class="w-full px-3 py-2.5 rounded-xl border @error('new.category_id') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                        <option value="">-- Seleccionar categoría --</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                        @endforeach
                    </select>
                    @error('new.category_id')
                        <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span>
                    @enderror
                @elseif($new['applied_on'] === 'template')
                    <select wire:model.live="new.product_template_id"
                        class="w-full px-3 py-2.5 rounded-xl border @error('new.product_template_id') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                        <option value="">-- Seleccionar producto --</option>
                        @foreach ($templates as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                    @error('new.product_template_id')
                        <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span>
                    @enderror
                @elseif($new['applied_on'] === 'variant')
                    <select wire:model.live="new.product_variant_id"
                        class="w-full px-3 py-2.5 rounded-xl border @error('new.product_variant_id') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                        <option value="">-- Seleccionar variante --</option>
                        @foreach ($variants as $v)
                            <option value="{{ $v->id }}">{{ $v->sku }} {{ $v->variant_name }}</option>
                        @endforeach
                    </select>
                    @error('new.product_variant_id')
                        <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span>
                    @enderror
                @else
                    <div
                        class="px-3 py-2.5 rounded-xl border border-dashed border-gray-300 dark:border-gray-600 text-sm text-gray-500 bg-gray-50 dark:bg-gray-800/50">
                        Aplica a todo el catálogo
                    </div>
                @endif
            </div>

            <div class="md:col-span-1">
                <x-label>Seq</x-label>
                <input type="number" wire:model.defer="new.sequence"
                    class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm" />
            </div>

            <div class="md:col-span-1">
                <x-label>Mín. Cant</x-label>
                <input type="number" step="0.01" wire:model.defer="new.min_qty"
                    class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm" />
            </div>

            <div class="md:col-span-2">
                <x-label>Método</x-label>
                <select wire:model.live="new.compute_method"
                    class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                    <option value="fixed">Precio fijo</option>
                    <option value="discount">% Descuento</option>
                    <option value="formula">Fórmula</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <x-label>Valor / Base</x-label>
                @if ($new['compute_method'] === 'fixed')
                    <input type="number" step="0.01" wire:model.defer="new.fixed_price" placeholder="0.00"
                        class="w-full px-3 py-2.5 rounded-xl border @error('new.fixed_price') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror dark:bg-gray-700 dark:text-gray-200" />
                    @error('new.fixed_price')
                        <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span>
                    @enderror
                @elseif($new['compute_method'] === 'discount')
                    <input type="number" step="0.01" wire:model.defer="new.percent_discount" placeholder="10%"
                        class="w-full px-3 py-2.5 rounded-xl border @error('new.percent_discount') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror dark:bg-gray-700 dark:text-gray-200" />
                    @error('new.percent_discount')
                        <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span>
                    @enderror
                @else
                    <select wire:model.live="new.base"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                        <option value="price_sale">Precio venta</option>
                        <option value="cost">Costo</option>
                        <option value="other_pricelist">Otra lista</option>
                    </select>
                @endif
            </div>

            <div class="md:col-span-1 flex gap-2 justify-end">
                @if (isset($new['id']))
                    <button wire:click="resetForm"
                        class="p-2.5 rounded-xl bg-gray-500 hover:bg-gray-600 text-white transition shadow-sm"
                        title="Cancelar">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <button wire:click="addLine"
                        class="flex-1 md:flex-none px-4 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white font-bold transition shadow-sm">
                        <i class="fa-solid fa-check"></i>
                    </button>
                @else
                    <button wire:click="addLine"
                        class="w-full px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition shadow-sm">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-4">
                <x-label>Vigencia (Opcional)</x-label>
                <div class="flex gap-2">
                    <input type="date" wire:model.defer="new.date_start"
                        class="w-1/2 px-3 py-2 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" />
                    <input type="date" wire:model.defer="new.date_end"
                        class="w-1/2 px-3 py-2 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" />
                </div>
            </div>

            @if ($new['compute_method'] === 'formula')
                <div class="md:col-span-8 grid grid-cols-2 md:grid-cols-5 gap-2">
                    <div>
                        <x-label class="text-[10px]">Mult.</x-label>
                        <input type="number" step="0.0001" wire:model.defer="new.price_multiplier"
                            class="w-full px-2 py-2 rounded-lg border dark:bg-gray-700 dark:text-gray-200" />
                    </div>
                    <div>
                        <x-label class="text-[10px]">Recargo</x-label>
                        <input type="number" step="0.01" wire:model.defer="new.price_surcharge"
                            class="w-full px-2 py-2 rounded-lg border dark:bg-gray-700 dark:text-gray-200" />
                    </div>
                    <div>
                        <x-label class="text-[10px]">Redondeo</x-label>
                        <input type="number" step="0.01" wire:model.defer="new.rounding"
                            class="w-full px-2 py-2 rounded-lg border dark:bg-gray-700 dark:text-gray-200" />
                    </div>
                    <div>
                        <x-label class="text-[10px]">Min Marg.</x-label>
                        <input type="number" step="0.01" wire:model.defer="new.min_margin"
                            class="w-full px-2 py-2 rounded-lg border dark:bg-gray-700 dark:text-gray-200" />
                    </div>
                    <div>
                        <x-label class="text-[10px]">Max Marg.</x-label>
                        <input type="number" step="0.01" wire:model.defer="new.max_margin"
                            class="w-full px-2 py-2 rounded-lg border dark:bg-gray-700 dark:text-gray-200" />
                    </div>
                </div>
            @endif

            @if ($new['compute_method'] === 'formula' && $new['base'] === 'other_pricelist')
                <div class="md:col-span-4">
                    <x-label>Lista de Precios Base</x-label>
                    <select wire:model.defer="new.base_pricelist_id"
                        class="w-full px-3 py-2 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                        <option value="">-- Elegir lista base --</option>
                        @foreach ($allPricelists as $pl)
                            <option value="{{ $pl->id }}">{{ $pl->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>

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
                        <tr wire:key="item-row-{{ $it->id }}"
                            class="border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50/60 dark:hover:bg-gray-900/30">

                            <td class="px-4 py-3">
                                <span
                                    class="text-xs px-2 py-1 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-200">
                                    {{ $it->applied_on }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                @if ($it->applied_on === 'category')
                                    <span class="font-medium">{{ optional($it->category)->name }}</span>
                                @elseif($it->applied_on === 'template')
                                    <span class="font-medium">{{ optional($it->productTemplate)->name }}</span>
                                @elseif($it->applied_on === 'variant')
                                    <span class="font-medium">{{ optional($it->productVariant)->sku }}</span>
                                    @if ($it->productVariant?->variant_name)
                                        <span class="text-xs text-gray-500"> ·
                                            {{ $it->productVariant->variant_name }}</span>
                                    @endif
                                @else
                                    <span class="text-gray-500">Todos</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-right">{{ number_format((float) $it->min_qty, 2) }}</td>

                            <td class="px-4 py-3">
                                <span
                                    class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                    {{ $it->compute_method }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-right">
                                @if ($it->compute_method === 'fixed')
                                    {{ number_format((float) $it->fixed_price, 2) }}
                                @elseif($it->compute_method === 'discount')
                                    {{ number_format((float) $it->percent_discount, 2) }}%
                                @else
                                    <span class="text-xs text-gray-500">Fórmula</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-center">
                                <span
                                    class="inline-flex px-2 py-1 rounded-full text-xs {{ $it->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $it->active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-right">
                                <button wire:click="startEdit({{ $it->id }})"
                                    class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-green-600 transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span
                                        class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Editar</span>
                                </button>

                                <button
                                    onclick="confirmDeletesimple({{ $it->id }}, @js('Regla'), 'deleteSingle', 'Esta regla será eliminada.')"
                                    class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-red-600 transition ml-2">
                                    <i class="fa-solid fa-trash"></i>
                                    <span
                                        class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Eliminar</span>
                                </button>
                            </td>
                        </tr>
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
