<div class="space-y-6">

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => route('dashboard'),
            'Productos' => route('admin.users.index'),
            'Crear' => '#',
        ]" />
    </div>

    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Crear producto</h1>
                <p class="text-sm text-gray-500 dark:text-gray-300">
                    El producto se lista por template y se vende por variante (default o combinación), como Odoo.
                </p>
            </div>

            <button wire:click="save"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                       bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition">
                <i class="fa-regular fa-floppy-disk"></i> Guardar
            </button>
        </div>
    </div>

    <!-- Tabs + Content -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">

        <!-- Tabs -->
        <div class="px-6 pt-5">
            <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700">
                <button type="button" wire:click="setTab('general')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'general'
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-regular fa-file-lines mr-2"></i> Información general
                </button>

                <button type="button" wire:click="setTab('attributes')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'attributes'
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-solid fa-layer-group mr-2"></i> Atributos y variantes
                </button>

                <button type="button" wire:click="setTab('precios')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'precios'
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-solid fa-layer-group mr-2"></i> Precios
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">

            {{-- TAB: GENERAL --}}
            @if ($tab === 'general')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Nombre -->
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Nombre</label>
                        <input wire:model.defer="name" type="text" placeholder="Ej: Camisa de vestir"
                            class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                   bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                   focus:ring-4 focus:ring-indigo-500/25 focus:border-indigo-400 shadow-sm">
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipo / Precio base -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Tipo</label>
                            <select wire:model.defer="type"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                <option value="goods">Bienes</option>
                                <option value="service">Servicio</option>
                                <option value="combo">Combo</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Precio Venta base (variante default)
                            </label>
                            <input wire:model.defer="base_price_sale" type="number" step="0.01" min="0"
                                placeholder="0.00"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('base_price_sale')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>


                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Precio Costo
                            </label>
                            <input wire:model.defer="base_price_sale" type="number" step="0.01" min="0"
                                placeholder="0.00"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('base_price_sale')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>



                    {{-- UoM --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- UoM (venta/stock) --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Unidad de medida (Venta / Stock)
                            </label>

                            <select wire:model.defer="uom_id"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                         bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                <option value="">-- Seleccionar --</option>

                                @foreach ($uomCategories as $cat)
                                    <optgroup label="{{ $cat['name'] }}">
                                        @foreach ($cat['uoms'] as $uom)
                                            <option value="{{ $uom['id'] }}">
                                                {{ $uom['name'] }}{{ $uom['symbol'] ? ' (' . $uom['symbol'] . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>

                            @error('uom_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Esta es la unidad base para inventario y ventas.</p>
                        </div>

                        {{-- UoM compra --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Unidad de compra (opcional)
                            </label>

                            <select wire:model.defer="uom_po_id"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                <option value="">-- Seleccionar --</option>

                                @foreach ($uomPurchaseOptions as $uom)
                                    <option value="{{ $uom['id'] }}">
                                        {{ $uom['name'] }}{{ $uom['symbol'] ? ' (' . $uom['symbol'] . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>

                            @error('uom_po_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Debe ser de la misma categoría que la UoM base.</p>
                        </div>
                    </div>



                    <!-- SKU Prefijo -->
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">SKU Prefijo
                            (opcional)</label>
                        <input wire:model.defer="sku_prefix" type="text" placeholder="Ej: CAM"
                            class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                   bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                        <p class="text-xs text-gray-500 mt-1">Se usa para generar SKUs: CAM-12-XL-NEGRO</p>
                    </div>




                    {{-- UoM --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- UoM (venta/stock) --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Categoría
                            </label>

                            <select wire:model.defer="uom_id"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                         bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                <option value="">-- Seleccionar --</option>

                                @foreach ($uomCategories as $cat)
                                    <optgroup label="{{ $cat['name'] }}">
                                        @foreach ($cat['uoms'] as $uom)
                                            <option value="{{ $uom['id'] }}">
                                                {{ $uom['name'] }}{{ $uom['symbol'] ? ' (' . $uom['symbol'] . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>

                            @error('uom_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Esta es la unidad base para inventario y ventas.</p>
                        </div>

                        {{-- UoM compra --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Impuesto
                            </label>

                            <select wire:model.defer="uom_po_id"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                <option value="">-- Seleccionar --</option>

                                @foreach ($uomPurchaseOptions as $uom)
                                    <option value="{{ $uom['id'] }}">
                                        {{ $uom['name'] }}{{ $uom['symbol'] ? ' (' . $uom['symbol'] . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>

                            @error('uom_po_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Debe ser de la misma categoría que la UoM base.</p>
                        </div>
                    </div>




                    <!-- Checks -->
                    <div class="flex flex-wrap items-center gap-4 pt-7">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" class="w-5 h-5" wire:model.defer="sale_ok">
                            <span class="text-sm text-gray-700 dark:text-gray-200">Ventas</span>
                        </label>

                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" class="w-5 h-5" wire:model.defer="pos_ok">
                            <span class="text-sm text-gray-700 dark:text-gray-200">Punto de venta</span>
                        </label>

                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" class="w-5 h-5" wire:model.defer="purchase_ok">
                            <span class="text-sm text-gray-700 dark:text-gray-200">Compras</span>
                        </label>

                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" class="w-5 h-5" wire:model.defer="active">
                            <span class="text-sm text-gray-700 dark:text-gray-200">Activo</span>
                        </label>
                    </div>
                </div>
            @endif

            {{-- TAB: ATTRIBUTES --}}
            @if ($tab === 'attributes')
                <div class="space-y-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Atributos y variantes</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-300">
                            Marca atributos y selecciona valores. Si no seleccionas nada, se crea solo la variante
                            default.
                        </p>
                    </div>

                    <div class="mt-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl
                bg-indigo-50 text-indigo-700 border border-indigo-100
                dark:bg-indigo-950/40 dark:text-indigo-200 dark:border-indigo-900/40">
                            <i class="fa-solid fa-cubes-stacked"></i>
                            <span class="font-semibold">
                                Se crearán {{ $variants_count }} variante{{ $variants_count === 1 ? '' : 's' }}
                            </span>
                        </div>

                        <div class="text-xs text-gray-500 dark:text-gray-300">
                            * Si no seleccionas atributos/valores, se crea solo la variante <b>Default</b>.
                        </div>
                    </div>

                    @if (!empty($variant_preview))
                        <div
                            class="mt-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900/30">
                            <div
                                class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                <p class="font-semibold text-gray-800 dark:text-gray-100">
                                    Vista previa de variantes
                                </p>
                                <span class="text-xs text-gray-500">
                                    mostrando {{ count($variant_preview) }} de {{ $variants_count }}
                                </span>
                            </div>



                            <div class="mt-6 flex justify-end">
                                <button type="button" wire:click="addAttributeLine"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
               bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition">
                                    <i class="fa-solid fa-plus"></i> Agregar atributo
                                </button>
                            </div>

                            <div class="mt-4 space-y-4">
                                @forelse($attributeLines as $index => $line)
                                    <div
                                        class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
                                        <div class="flex flex-col md:flex-row md:items-start gap-4">

                                            {{-- Atributo --}}
                                            <div class="w-full md:w-1/3">
                                                <label
                                                    class="text-xs font-semibold text-gray-600 dark:text-gray-300">Atributo</label>

                                                @php
                                                    $used = collect($attributeLines)
                                                        ->pluck('attribute_id')
                                                        ->filter()
                                                        ->values()
                                                        ->all();
                                                    $currentAttr = (int) ($line['attribute_id'] ?? 0);
                                                @endphp

                                                <select
                                                    wire:model.live="attributeLines.{{ $index }}.attribute_id"
                                                    class="mt-2 w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                               bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                                                    <option value="">-- Seleccionar --</option>
                                                    @foreach ($catalogAttributes as $attr)
                                                        <option value="{{ $attr->id }}"
                                                            @disabled(in_array($attr->id, $used) && $currentAttr !== $attr->id)>
                                                            {{ $attr->name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <p class="text-xs text-gray-500 mt-2">Ej: Talla, Color…</p>
                                            </div>

                                            {{-- Valores --}}
                                            <div class="w-full md:flex-1">
                                                <label
                                                    class="text-xs font-semibold text-gray-600 dark:text-gray-300">Valores</label>

                                                @php
                                                    $selectedAttrId = (int) ($line['attribute_id'] ?? 0);
                                                    $attrObj = $catalogAttributes->firstWhere('id', $selectedAttrId);
                                                @endphp

                                                @if ($selectedAttrId && $attrObj)
                                                    <div class="mt-2 flex flex-wrap gap-2">
                                                        @foreach ($attrObj->values as $val)
                                                            <label
                                                                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl
                                              bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                                              hover:border-indigo-400 transition">
                                                                <input type="checkbox" value="{{ $val->id }}"
                                                                    wire:model.live="attributeLines.{{ $index }}.value_ids"
                                                                    class="w-4 h-4">
                                                                <span
                                                                    class="text-sm text-gray-800 dark:text-gray-100">{{ $val->name }}</span>

                                                                @if ((float) $val->extra_price > 0)
                                                                    <span class="text-xs text-gray-500">
                                                                        +{{ number_format((float) $val->extra_price, 2) }}
                                                                    </span>
                                                                @endif
                                                            </label>
                                                        @endforeach
                                                    </div>

                                                    <p class="text-xs text-gray-500 mt-2">
                                                        Selecciona valores para generar combinaciones.
                                                    </p>
                                                @else
                                                    <div
                                                        class="mt-2 px-4 py-3 rounded-xl border border-dashed border-gray-300 dark:border-gray-600 text-sm text-gray-500">
                                                        Selecciona un atributo para ver sus valores.
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Eliminar --}}
                                            <div class="md:w-16 flex md:flex-col items-center justify-end gap-2">
                                                <button type="button"
                                                    wire:click="removeAttributeLine({{ $index }})"
                                                    class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                               bg-red-600 hover:bg-red-700 text-white transition">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>

                                        </div>
                                    </div>

                                @empty
                                    <div
                                        class="px-4 py-4 rounded-xl border border-dashed border-gray-300 dark:border-gray-600 text-sm text-gray-500">
                                        Aún no has agregado atributos. Haz clic en <b>“Agregar atributo”</b>.
                                    </div>
                                @endforelse
                            </div>




                            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                @foreach ($variant_preview as $vp)
                                    <div
                                        class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700
                            bg-gray-50 dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-100">
                                        {{ $vp }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif


                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        @foreach ($catalogAttributes as $attr)
                            <div
                                class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900/30">
                                <div class="flex items-center justify-between">
                                    <label
                                        class="inline-flex items-center gap-2 font-semibold text-gray-800 dark:text-gray-100">
                                        <input type="checkbox" class="w-5 h-5"
                                            wire:model.live="selectedAttributes.{{ $attr['id'] }}"
                                            wire:change="toggleAttribute({{ $attr['id'] }})">
                                        {{ $attr['name'] }}
                                    </label>

                                    <span
                                        class="text-xs px-2 py-1 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-200">
                                        {{ count($attr['values'] ?? []) }} valores
                                    </span>
                                </div>

                                @if (!empty($selectedAttributes[$attr['id']]))
                                    <div class="mt-3 grid grid-cols-2 md:grid-cols-3 gap-2">
                                        @foreach ($attr['values'] ?? [] as $val)
                                            <label
                                                class="flex items-center gap-2 px-3 py-2 rounded-lg
                                      bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                                      hover:border-indigo-400 transition">
                                                <input type="checkbox" class="w-4 h-4"
                                                    wire:model.live="selectedValues.{{ $attr['id'] }}.{{ $val['id'] }}">
                                                <span
                                                    class="text-sm text-gray-800 dark:text-gray-100">{{ $val['name'] }}</span>

                                                @if ((float) ($val['extra_price'] ?? 0) > 0)
                                                    <span class="ml-auto text-xs text-gray-500">
                                                        +{{ number_format((float) $val['extra_price'], 2) }}
                                                    </span>
                                                @endif
                                            </label>
                                        @endforeach
                                    </div>

                                    <p class="text-xs text-gray-500 mt-2">
                                        La variante final suma el extra_price de cada valor.
                                    </p>
                                @else
                                    <p class="text-xs text-gray-500 mt-2">Marca el atributo para escoger valores.</p>
                                @endif
                            </div>
                        @endforeach
                    </div>






                    <div class="pt-2 flex justify-end">
                        <button wire:click="save"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl
                                bg-gradient-to-r from-indigo-600 to-sky-600 hover:from-indigo-700 hover:to-sky-700
                                text-white font-semibold shadow-sm transition">
                            <i class="fa-regular fa-floppy-disk"></i> Guardar producto
                        </button>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
