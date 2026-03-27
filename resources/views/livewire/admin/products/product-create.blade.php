<div class="space-y-6">

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => route('dashboard'),
            'Productos' => route('admin.products.index'),
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

                <button type="button" wire:click="setTab('pdv')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'pdv'
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-solid fa-layer-group mr-2"></i> Punto de venta
                </button>
                <button type="button" wire:click="setTab('accounting')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'accounting'
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-solid fa-layer-group mr-2"></i> Contabilidad
                </button>

                <button type="button" wire:click="setTab('subscriptions')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'subscriptions'
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-solid fa-layer-group mr-2"></i> Suscripciones
                </button>


            </div>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">

            {{-- TAB: GENERAL --}}
            @if ($tab === 'general')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- 1) Nombre --}}
                    <div class="md:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Nombre</label>
                        <input wire:model.defer="name" type="text" placeholder="Ej: Agua mineral 500ml"
                            class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                   bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                   focus:ring-4 focus:ring-indigo-500/25 focus:border-indigo-400 shadow-sm">
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 2) Tipo + Precios (3 columnas) --}}
                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Tipo</label>
                            <select wire:model.live="type"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                <option value="goods">Bienes</option>
                                <option value="service">Servicio</option>
                                <option value="combo">Combo</option>
                            </select>
                            @error('type')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
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
                                Costo
                            </label>
                            <input wire:model.defer="base_cost" type="number" step="0.01" min="0"
                                placeholder="0.00"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('base_cost')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- 3) FILA 1 (4 campos): UoM Venta/Stock, UoM Compra, SKU Prefijo, Categoría --}}
                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-4 gap-4">

                        {{-- UoM base --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                U. Medida (Venta / Stock)
                            </label>
                            <select wire:model.live="uom_id"
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
                        </div>

                        {{-- UoM compra --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                U. Compra (opcional)
                            </label>

                            <select wire:model.defer="uom_po_id" wire:key="uom-po-{{ $uom_id }}"
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

                            @error('uom_po_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- SKU prefijo --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                SKU Prefijo (opcional)
                            </label>
                            <input wire:model.defer="sku_prefix" type="text" placeholder="Ej: AGUA"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('sku_prefix')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Categoría --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Categoría
                            </label>

                            <select wire:model.defer="category_id"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                <option value="">-- Seleccionar --</option>

                                @foreach ($categoryOptions as $c)
                                    <option value="{{ $c['id'] }}">{{ $c['label'] }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror

                        </div>


                    </div>

                    {{-- 4) FILA 2 (4 campos): Impuesto, Detracción, Código de barras, Referencia --}}
                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-5 gap-4">

                        {{-- Impuesto --}}
                        {{--  <div>

                            <div>
                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Impuestos de
                                    venta</label>

                                <select wire:model.defer="sale_tax_ids" multiple
                                    class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                         bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                    @foreach ($taxOptions as $t)
                                        <option value="{{ $t['id'] }}">
                                            {{ $t['name'] }}
                                            ({{ rtrim(rtrim(number_format($t['amount'], 2), '0'), '.') }}{{ $t['amount_type'] === 'percent' ? '%' : '' }})
                                        </option>
                                    @endforeach
                                </select>

                                @error('sale_tax_ids')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                                @error('sale_tax_ids.*')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror

                                <p class="text-xs text-gray-500 mt-1">Tip: mantén presionado Ctrl (Windows) o ⌘ (Mac)
                                    para seleccionar varios.</p>
                            </div>
                        </div> --}}

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Impuestos de venta
                            </label>

                            <div
                                class="mt-2 space-y-2 rounded-xl border border-gray-300 dark:border-gray-600 p-3 bg-white dark:bg-gray-700">
                                @foreach ($taxOptions as $t)
                                    <label class="flex items-center gap-3 text-sm text-gray-800 dark:text-gray-200">
                                        <input type="checkbox" value="{{ $t['id'] }}"
                                            wire:model.defer="sale_tax_ids"
                                            class="rounded border-gray-300 dark:border-gray-600" />
                                        <span>
                                            {{ $t['name'] }}
                                            <span class="text-gray-500">
                                                ({{ rtrim(rtrim(number_format($t['amount'], 2), '0'), '.') }}{{ $t['amount_type'] === 'percent' ? '%' : '' }})
                                            </span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                            @error('sale_tax_ids')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                            @error('sale_tax_ids.*')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>



                        {{-- <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Impuestos de
                                compra</label>

                            <select wire:model.defer="purchase_tax_ids" multiple
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                     bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                @foreach ($taxOptions as $t)
                                    <option value="{{ $t['id'] }}">
                                        {{ $t['name'] }}
                                        ({{ rtrim(rtrim(number_format($t['amount'], 2), '0'), '.') }}{{ $t['amount_type'] === 'percent' ? '%' : '' }})
                                    </option>
                                @endforeach
                            </select>

                            @error('purchase_tax_ids')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                            @error('purchase_tax_ids.*')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        --}}


                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Impuestos de compra
                            </label>

                            <div
                                class="mt-2 space-y-2 rounded-xl border border-gray-300 dark:border-gray-600 p-3 bg-white dark:bg-gray-700">
                                @foreach ($taxOptions as $t)
                                    <label class="flex items-center gap-3 text-sm text-gray-800 dark:text-gray-200">
                                        <input type="checkbox" value="{{ $t['id'] }}"
                                            wire:model.defer="purchase_tax_ids"
                                            class="rounded border-gray-300 dark:border-gray-600" />
                                        <span>
                                            {{ $t['name'] }}
                                            <span class="text-gray-500">
                                                ({{ rtrim(rtrim(number_format($t['amount'], 2), '0'), '.') }}{{ $t['amount_type'] === 'percent' ? '%' : '' }})
                                            </span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                            @error('purchase_tax_ids')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                            @error('purchase_tax_ids.*')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Detracción --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Detracción</label>

                            {{-- Detracción --}}
                            <select wire:model.defer="detraction_id"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                <option value="">-- No aplica --</option>
                                @foreach ($detractionOptions as $d)
                                    <option value="{{ $d['id'] }}">
                                        {{ $d['code'] }} - {{ $d['name'] }}
                                        ({{ rtrim(rtrim(number_format($d['rate'], 2), '0'), '.') }}%)
                                    </option>
                                @endforeach
                            </select>
                            @error('detraction_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Código de barras --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Código de
                                barras</label>
                            <input wire:model.defer="barcode" type="text" placeholder="Ej: 7751234567890"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('barcode')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Referencia --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Referencia</label>
                            <input wire:model.defer="reference" type="text"
                                placeholder="Ej: SKU proveedor / código interno"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('reference')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- 5) FILA 3: Marca, Modelo, Rastrear inventario, Cantidad a la mano --}}
                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Marca</label>

                            <select wire:model.live="brand_id"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                <option value="">-- Seleccionar --</option>
                                @foreach ($brandOptions as $b)
                                    <option value="{{ $b['id'] }}">{{ $b['name'] }}</option>
                                @endforeach
                            </select>
                            @error('brand_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Modelo --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Modelo</label>
                            <select wire:model.defer="modello_id"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                                @disabled(empty($brand_id))>
                                <option value="">
                                    {{ empty($brand_id) ? '-- Primero selecciona Marca --' : '-- Seleccionar --' }}
                                </option>

                                @foreach ($modelloOptions as $m)
                                    <option value="{{ $m['id'] }}">{{ $m['name'] }}</option>
                                @endforeach
                            </select>

                            @error('modello_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>



                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Rastrear
                                inventario</label>

                            <select wire:model.defer="tracking"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                <option value="">-- Seleccionar --</option>
                                <option value="quantity">Por cantidad</option>
                                <option value="serial">Por serie</option>
                                <option value="lot">Por lote</option>
                            </select>

                        </div>

                        {{-- temporada --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Temporada</label>

                            <select wire:model.live="season_id"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                <option value="">-- Seleccionar --</option>
                                @foreach ($seasonOptions as $season)
                                    <option value="{{ $season['id'] }}">{{ $season['name'] }}</option>
                                @endforeach
                            </select>
                            @error('season_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- 6) Checks finales --}}
                    <div class="md:col-span-2 flex flex-wrap items-center gap-4 pt-2">
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



            @if ($tab === 'precios')
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Listas de precios</h3>
                        <button type="button" wire:click="openPriceRuleModal"
                            class="text-indigo-600 hover:underline text-sm font-semibold">
                            + Agregar un precio
                        </button>
                    </div>

                    <div class="border rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Lista
                                        de precios</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                        Cantidad mínima</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Precio
                                    </th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase w-10">
                                        <i class="fa-solid fa-cog"></i>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($this->productPriceRules as $index => $rule)
                                    <tr wire:key="price-rule-{{ $index }}" class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-sm text-gray-700">
                                            {{ is_array($rule['pricelist']) ? $rule['pricelist']['name'] : $rule['pricelist']->name }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-600">
                                            {{ number_format($rule['min_qty'], 0) }}
                                        </td>
                                        <td class="px-4 py-2 text-sm font-bold text-gray-900">
                                            @if ($rule['compute_method'] === 'fixed')
                                                {{ number_format($rule['fixed_price'], 2) }}
                                            @else
                                                {{ $rule['percent_discount'] }}% Descuento
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            {{-- El botón de eliminar --}}
                                            <button type="button"
                                                wire:click="removePriceRule({{ $index }}, {{ $rule['id'] ?? 'null' }})"
                                                wire:confirm="¿Estás seguro de eliminar esta regla de precio?"
                                                class="text-gray-400 hover:text-red-600 transition-colors">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    {{-- ... tu tr de tabla vacía ... --}}
                                @endforelse
                            </tbody>
                        </table>


                    </div>
                </div>

                {{-- Aquí incluyes el Modal que te envié antes --}}

                @if ($showPriceModal)
                    <div class="fixed inset-0 z-[100] overflow-y-auto">
                        <div class="fixed inset-0 bg-black/50 transition-opacity"></div>

                        <div class="flex min-h-full items-center justify-center p-4">
                            <div
                                class="relative w-full max-w-2xl transform rounded-lg bg-white shadow-2xl transition-all">

                                <div class="flex items-center justify-between border-b px-4 py-3">
                                    <h3 class="text-lg font-semibold text-gray-900">Crear Reglas de lista de precios
                                    </h3>
                                    <button wire:click="$set('showPriceModal', false)"
                                        class="text-gray-400 hover:text-gray-600">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="p-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Producto</label>
                                                <input type="text" value="{{ $name }}" disabled
                                                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 text-sm shadow-sm">
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Tipo de
                                                    precio</label>
                                                <div class="mt-2 space-x-4">
                                                    <label class="inline-flex items-center">
                                                        <input type="radio"
                                                            wire:model.live="modalRule.compute_method"
                                                            value="discount"
                                                            class="text-indigo-600 focus:ring-indigo-500">
                                                        <span class="ml-2 text-sm">Descuento</span>
                                                    </label>
                                                    <label class="inline-flex items-center">
                                                        <input type="radio"
                                                            wire:model.live="modalRule.compute_method" value="fixed"
                                                            class="text-indigo-600 focus:ring-indigo-500">
                                                        <span class="ml-2 text-sm">Precio fijo</span>
                                                    </label>
                                                </div>
                                            </div>

                                            @if ($modalRule['compute_method'] === 'fixed')
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Precio
                                                        fijo</label>
                                                    <div class="mt-1 flex rounded-md shadow-sm">
                                                        <input type="number" step="0.01"
                                                            wire:model="modalRule.fixed_price"
                                                            class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                        <span
                                                            class="inline-flex items-center px-3 text-gray-500 sm:text-sm">por
                                                            Unidades</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Descuento
                                                        (%)</label>
                                                    <input type="number" step="0.01"
                                                        wire:model="modalRule.percent_discount"
                                                        class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                </div>
                                            @endif
                                        </div>

                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Cantidad
                                                    mínima</label>
                                                <input type="number" wire:model="modalRule.min_qty"
                                                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Lista de
                                                    precios</label>
                                                <select wire:model="modalRule.pricelist_id"
                                                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    <option value="">Seleccionar...</option>
                                                    @foreach ($allPricelists as $pl)
                                                        <option value="{{ $pl['id'] }}">{{ $pl['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="rounded-md bg-blue-50 p-3">
                                                <p class="text-xs text-blue-700">
                                                    Por fórmula o precio fijo, el precio original no se muestra en
                                                    órdenes de ventas.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex items-center justify-start space-x-2 border-t px-4 py-3 bg-gray-50 rounded-b-lg">
                                    <button wire:click="savePriceRule"
                                        class="rounded-md bg-purple-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-700">
                                        Guardar y cerrar
                                    </button>
                                    <button wire:click="$set('showPriceModal', false)"
                                        class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                        Descartar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif




            @endif




            @if ($tab === 'pdv')
                <div class="mt-4">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" wire:model.live="pos_ok" class="rounded border-gray-300">
                        <span class="font-medium">Disponible en Punto de Venta</span>
                    </label>
                </div>

                @if ($pos_ok)
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <h3 class="text-sm font-semibold text-slate-800">Categorías POS</h3>
                        <p class="mt-1 text-xs text-slate-500">
                            Busca y selecciona una o varias categorías para el punto de venta.
                        </p>

                        {{-- Seleccionadas --}}
                        @if (count($this->selectedPosCategoryItems))
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($this->selectedPosCategoryItems as $item)
                                    <span
                                        class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">
                                        {{ $item['complete_name'] }}
                                        <button type="button" wire:click="removePosCategory({{ $item['id'] }})"
                                            class="text-blue-500 hover:text-blue-700">
                                            ×
                                        </button>
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Buscador --}}
                        <div class="mt-4">
                            <input type="text" wire:model.live.debounce.250ms="posCategorySearch"
                                placeholder="Buscar categoría POS..."
                                class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        {{-- Resultados --}}
                        <div class="mt-3 max-h-64 overflow-y-auto rounded-xl border border-slate-200">
                            @forelse($filteredPosCategories as $item)
                                <button type="button" wire:click="addPosCategory({{ $item['id'] }})"
                                    class="flex w-full items-center justify-between border-b border-slate-100 px-3 py-2 text-left text-sm hover:bg-slate-50">
                                    <span class="text-slate-700">{{ $item['label'] }}</span>
                                    <span class="text-xs text-slate-400">Agregar</span>
                                </button>
                            @empty
                                <div class="px-3 py-3 text-sm text-slate-500">
                                    No se encontraron categorías.
                                </div>
                            @endforelse
                        </div>

                        @error('pos_category_ids')
                            <div class="mt-2 text-xs text-red-500">{{ $message }}</div>
                        @enderror



                        @if ($this->canCreatePosCategory)
                            <div class="mt-3">
                                <button type="button" wire:click="createPosCategory"
                                    class="inline-flex items-center rounded-xl border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100">
                                    + Crear categoría POS "{{ $posCategorySearch }}"
                                </button>
                            </div>
                        @endif

                        @error('posCategorySearch')
                            <div class="mt-2 text-xs text-red-500">{{ $message }}</div>
                        @enderror





                    </div>
                @endif

                @if ($pos_ok)
                    <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <h3 class="text-sm font-semibold text-slate-800">Productos adicionales</h3>
                        <p class="mt-1 text-xs text-slate-500">
                            Busca productos para ofrecer como adicionales o sugerencias.
                        </p>

                        {{-- Seleccionados --}}
                        @if (count($this->selectedAdditionalProductItems))
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($this->selectedAdditionalProductItems as $item)
                                    <span
                                        class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                                        {{ $item['name'] }}
                                        <button type="button"
                                            wire:click="removeAdditionalProduct({{ $item['id'] }})"
                                            class="text-emerald-500 hover:text-emerald-700">
                                            ×
                                        </button>
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Buscador --}}
                        <div class="mt-4">
                            <input type="text" wire:model.live.debounce.250ms="additionalProductSearch"
                                placeholder="Buscar producto adicional..."
                                class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        {{-- Resultados --}}
                        <div class="mt-3 max-h-64 overflow-y-auto rounded-xl border border-slate-200">
                            @forelse($filteredAdditionalProducts as $item)
                                <button type="button" wire:click="addAdditionalProduct({{ $item['id'] }})"
                                    class="flex w-full items-center justify-between border-b border-slate-100 px-3 py-2 text-left text-sm hover:bg-slate-50">
                                    <span class="text-slate-700">{{ $item['name'] }}</span>
                                    <span class="text-xs text-slate-400">Agregar</span>
                                </button>
                            @empty
                                <div class="px-3 py-3 text-sm text-slate-500">
                                    No se encontraron productos.
                                </div>
                            @endforelse
                        </div>

                        @error('additional_product_ids')
                            <div class="mt-2 text-xs text-red-500">{{ $message }}</div>
                        @enderror
                    </div>
                @endif


            @endif





            @php
                $controlBase = "w-full h-12 px-4 rounded-xl border border-gray-300 dark:border-gray-600
                            bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                            focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition";
            @endphp

            @if ($tab === 'accounting')
                <div class="space-y-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Contabilidad</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-300">
                            Define la cuenta por defecto para el producto.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Cuenta contable
                                Ventas </label>
                            <select wire:key="sell-{{ $type }}" wire:model.live="account_sell_id"
                                class="{{ $controlBase }} mt-2">
                                <option value="">— Seleccionar —</option>
                                @foreach ($accountOptions as $a)
                                    <option value="{{ $a['id'] }}">{{ $a['label'] }}</option>
                                @endforeach
                            </select>
                            @error('account_sell_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Cuenta contable
                                Compras </label>
                            <select wire:key="buy-{{ $type }}" wire:model.live="account_buy_id"
                                class="{{ $controlBase }} mt-2">
                                <option value="">— Seleccionar —</option>
                                @foreach ($accountOptions as $a)
                                    <option value="{{ $a['id'] }}">{{ $a['label'] }}</option>
                                @endforeach
                            </select>
                            @error('account_buy_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button wire:click="save"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl
                                   bg-gradient-to-r from-indigo-600 to-sky-600 hover:from-indigo-700 hover:to-sky-700
                                   text-white font-semibold shadow-sm transition">
                            <i class="fa-regular fa-floppy-disk"></i> Guardar partner
                        </button>
                    </div>
                </div>
            @endif


            {{-- TAB: SUBSCRIPTIONS --}}
            @if ($tab === 'subscriptions')
                <div class="space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Política de Suscripción</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-300">
                            Configure las reglas por defecto para cuando este producto sea vendido bajo modalidad de
                            suscripción.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label
                                class="flex items-center gap-3 cursor-pointer p-4 rounded-xl border border-indigo-100 bg-indigo-50/30 dark:border-indigo-900/30 dark:bg-indigo-900/10">
                                <input type="checkbox" wire:model.live="is_subscription"
                                    class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                <div>
                                    <span class="block text-sm font-bold text-gray-700 dark:text-gray-200">Se puede
                                        suscribir</span>
                                    <span class="text-xs text-gray-500">Si se marca, este producto generará contratos
                                        recurrentes al venderse.</span>
                                </div>
                            </label>
                        </div>

                        @if ($is_subscription)
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Plan
                                    Sugerido</label>
                                <select wire:model.defer="subscription_plan_id"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                                    <option value="">Seleccione el intervalo...</option>
                                    @foreach ($this->plans as $plan)
                                        <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Precio de
                                    Renovación Base</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-3 text-gray-400">$</span>
                                    <input wire:model.defer="recurring_price" type="number" step="0.01"
                                        class="w-full pl-8 pr-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                                </div>
                            </div>

                            <div
                                class="md:col-span-2 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/30 flex gap-3">
                                <i class="fa-solid fa-lightbulb text-amber-600 mt-1"></i>
                                <p class="text-xs text-amber-800 dark:text-amber-200 leading-relaxed">
                                    <b>Nota de Arquitectura:</b> Las fechas y estados se definirán automáticamente en la
                                    <b>Orden de Venta</b> cuando se asigne un cliente a este servicio.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if ($tab === 'images')
            @endif

        </div>
    </div>
</div>
