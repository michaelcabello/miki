<div class="space-y-6">

    {{-- ════════════════════════════════════════════════════════
         BREADCRUMB
    ════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard'  => route('dashboard'),
            'Productos'  => route('admin.products.index'),
            'Editar'     => '#',
        ]" />
    </div>

    {{-- ════════════════════════════════════════════════════════
         HEADER
    ════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Editar producto</h1>
                <p class="text-sm text-gray-500 dark:text-gray-300 mt-0.5">
                    <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $product_template->name }}</span>
                    &nbsp;·&nbsp;
                    <span class="font-mono text-xs text-gray-400">{{ $product_template->slug }}</span>
                    &nbsp;·&nbsp; ID: {{ $product_template->id }}
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('admin.products.index') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                           bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                           font-semibold transition hover:bg-gray-200 dark:hover:bg-gray-600">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>
                <a href="{{ route('admin.products.variants', $product_template) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                           bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300
                           border border-indigo-200 dark:border-indigo-800 font-semibold transition text-sm">
                    <i class="fa-solid fa-cubes-stacked"></i> Ver variantes
                </a>
                @can('Product Update')
                    <button wire:click="update" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                               bg-gradient-to-r from-indigo-600 to-sky-600
                               hover:from-indigo-700 hover:to-sky-700
                               text-white font-semibold shadow-sm transition">
                        <i class="fa-regular fa-floppy-disk" wire:loading.class="hidden" wire:target="update"></i>
                        <i class="fa-solid fa-spinner fa-spin hidden" wire:loading.class.remove="hidden" wire:target="update"></i>
                        Guardar
                    </button>
                @endcan
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════
         TABS + CONTENIDO
    ════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">

        {{-- TABS NAV --}}
        <div class="px-6 pt-5">
            <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700">

                @php
                    $tabs = [
                        'general'       => ['icon' => 'fa-regular fa-file-lines',    'label' => 'Información general'],
                        'attributes'    => ['icon' => 'fa-solid fa-layer-group',      'label' => 'Atributos y variantes'],
                        'precios'       => ['icon' => 'fa-solid fa-tag',              'label' => 'Precios'],
                        'pdv'           => ['icon' => 'fa-solid fa-cash-register',    'label' => 'Punto de venta'],
                        'accounting'    => ['icon' => 'fa-solid fa-receipt',          'label' => 'Contabilidad'],
                        'subscriptions' => ['icon' => 'fa-solid fa-rotate',           'label' => 'Suscripciones'],
                        'web'           => ['icon' => 'fa-solid fa-globe',            'label' => 'Web / SEO'],
                    ];
                @endphp

                @foreach ($tabs as $key => $meta)
                    <button type="button" wire:click="setTab('{{ $key }}')"
                        class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                            {{ $tab === $key
                                ? 'bg-indigo-600 text-white'
                                : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        <i class="{{ $meta['icon'] }} mr-1.5"></i>{{ $meta['label'] }}
                        @if ($key === 'attributes')
                            <span class="ml-1 px-1.5 py-0.5 rounded-full text-xs
                                {{ $tab === 'attributes'
                                    ? 'bg-white/20 text-white'
                                    : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200' }}">
                                {{ count($this->existingAttributeLines) }}
                            </span>
                        @endif
                        @if ($key === 'precios')
                            <span class="ml-1 px-1.5 py-0.5 rounded-full text-xs
                                {{ $tab === 'precios'
                                    ? 'bg-white/20 text-white'
                                    : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200' }}">
                                {{ count($dbPricelistItems) }}
                            </span>
                        @endif
                    </button>
                @endforeach

            </div>
        </div>

        @php
            $ctrl = "w-full h-11 px-4 rounded-xl border border-gray-300 dark:border-gray-600
                     bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                     focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition";
        @endphp

        <div class="p-6 space-y-6">

            {{-- ══════════════════════════════════════════════
                 TAB: INFORMACIÓN GENERAL
            ══════════════════════════════════════════════ --}}
            @if ($tab === 'general')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- Nombre --}}
                    <div class="md:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input wire:model.defer="name" type="text"
                            placeholder="Ej: Zapatilla Running Pro"
                            class="{{ $ctrl }} mt-2">
                        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tipo + Precio base --}}
                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Tipo</label>
                            <select wire:model.live="type" class="{{ $ctrl }} mt-2">
                                <option value="goods">Bienes</option>
                                <option value="service">Servicio</option>
                                <option value="combo">Combo</option>
                            </select>
                            @error('type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Precio venta base
                            </label>
                            <input wire:model.defer="base_price_sale" type="number" step="0.01" min="0"
                                placeholder="0.00" class="{{ $ctrl }} mt-2 text-right">
                            @error('base_price_sale') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Rastreo de inventario
                            </label>
                            <select wire:model.defer="tracking" class="{{ $ctrl }} mt-2">
                                <option value="quantity">Por cantidad</option>
                                <option value="serial">Por número de serie</option>
                                <option value="lot">Por lote</option>
                            </select>
                        </div>
                    </div>

                    {{-- Opciones de venta (checkboxes) --}}
                    <div class="md:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200 block mb-3">
                            Disponible en
                        </label>
                        <div class="flex flex-wrap gap-6">
                            @foreach ([
                                ['sale_ok',     'fa-solid fa-handshake',       'Ventas',          'Visible en cotizaciones y órdenes'],
                                ['purchase_ok', 'fa-solid fa-cart-shopping',   'Compras',         'Se puede comprar a proveedores'],
                                ['pos_ok',      'fa-solid fa-cash-register',   'Punto de venta',  'Visible en el POS'],
                                ['active',      'fa-solid fa-circle-check',    'Activo',          'Desactiva para archivar el producto'],
                            ] as [$prop, $icon, $label, $hint])
                                <label class="flex items-start gap-2.5 cursor-pointer group">
                                    <input type="checkbox" wire:model.live="{{ $prop }}"
                                        class="mt-0.5 w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                            <i class="{{ $icon }} mr-1 text-indigo-500"></i>{{ $label }}
                                        </span>
                                        <span class="block text-xs text-gray-400">{{ $hint }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Unidad de medida venta --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            UoM Venta
                        </label>
                        <select wire:model.live="uom_id" class="{{ $ctrl }} mt-2">
                            <option value="">— Seleccionar —</option>
                            @foreach ($uomCategories as $uomCat)
                                <optgroup label="{{ $uomCat['name'] }}">
                                    @foreach ($uomCat['uoms'] as $uom)
                                        <option value="{{ $uom['id'] }}">
                                            {{ $uom['name'] }}{{ $uom['symbol'] ? ' (' . $uom['symbol'] . ')' : '' }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    {{-- Unidad de medida compra --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            UoM Compra
                        </label>
                        @if ($uom_id && count($uomPurchaseOptions))
                            <select wire:model.defer="uom_po_id" class="{{ $ctrl }} mt-2">
                                <option value="">— Misma que venta —</option>
                                @foreach ($uomPurchaseOptions as $u)
                                    <option value="{{ $u['id'] }}">
                                        {{ $u['name'] }}{{ $u['symbol'] ? ' (' . $u['symbol'] . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <div class="mt-2 h-11 px-4 flex items-center rounded-xl border border-dashed
                                        border-gray-300 dark:border-gray-600 text-sm text-gray-400">
                                Selecciona primero la UoM de venta
                            </div>
                        @endif
                    </div>

                    {{-- Categoría --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Categoría</label>
                        <select wire:model.defer="category_id" class="{{ $ctrl }} mt-2">
                            <option value="">— Sin categoría —</option>
                            @foreach ($categoryOptions as $c)
                                <option value="{{ $c['id'] }}">{{ $c['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Temporada --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Temporada</label>
                        <select wire:model.defer="season_id" class="{{ $ctrl }} mt-2">
                            <option value="">— Sin temporada —</option>
                            @foreach ($seasons as $s)
                                <option value="{{ $s['id'] }}">{{ $s['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Marca --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Marca</label>
                        <select wire:model.live="brand_id" class="{{ $ctrl }} mt-2">
                            <option value="">— Sin marca —</option>
                            @foreach ($brands as $b)
                                <option value="{{ $b['id'] }}">{{ $b['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Modelo --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Modelo</label>
                        @if ($brand_id && count($modelloOptions))
                            <select wire:model.defer="modello_id" class="{{ $ctrl }} mt-2">
                                <option value="">— Sin modelo —</option>
                                @foreach ($modelloOptions as $m)
                                    <option value="{{ $m['id'] }}">{{ $m['name'] }}</option>
                                @endforeach
                            </select>
                        @else
                            <div class="mt-2 h-11 px-4 flex items-center rounded-xl border border-dashed
                                        border-gray-300 dark:border-gray-600 text-sm text-gray-400">
                                {{ $brand_id ? 'Sin modelos para esta marca' : 'Selecciona una marca primero' }}
                            </div>
                        @endif
                        @error('modello_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Código de barras (variante default) --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Código de barras (variante default)
                        </label>
                        <input wire:model.defer="barcode" type="text"
                            placeholder="Ej: 7751234567890"
                            class="{{ $ctrl }} mt-2 font-mono">
                        @error('barcode') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Referencia interna --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Referencia interna (SKU)
                        </label>
                        <input wire:model.defer="reference" type="text"
                            placeholder="Ej: SKU-001"
                            class="{{ $ctrl }} mt-2 font-mono">
                        @error('reference') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                </div>

                {{-- Botón guardar al final del tab --}}
                @can('Product Update')
                    <div class="pt-2 flex justify-end">
                        <button wire:click="update"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl
                                   bg-gradient-to-r from-indigo-600 to-sky-600
                                   hover:from-indigo-700 hover:to-sky-700
                                   text-white font-semibold shadow-sm transition">
                            <i class="fa-regular fa-floppy-disk"></i> Guardar cambios
                        </button>
                    </div>
                @endcan
            @endif

            {{-- ══════════════════════════════════════════════
                 TAB: ATRIBUTOS Y VARIANTES (read-only)
            ══════════════════════════════════════════════ --}}
            @if ($tab === 'attributes')

                {{-- Nota informativa --}}
                <div class="rounded-xl border border-amber-200 dark:border-amber-700
                            bg-amber-50 dark:bg-amber-900/20 p-4 flex gap-3">
                    <i class="fa-solid fa-circle-info text-amber-500 mt-0.5 text-lg flex-shrink-0"></i>
                    <div>
                        <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">
                            Atributos definidos al crear el producto
                        </p>
                        <p class="text-xs text-amber-700 dark:text-amber-300 mt-0.5">
                            Los atributos y sus combinaciones no se pueden modificar aquí.
                            Para agregar variantes usa el gestor de variantes.
                            Los <strong>precios individuales</strong> por variante se editan en la pestaña
                            <strong>Precios</strong>.
                        </p>
                    </div>
                </div>

                {{-- Tabla de atributos actuales --}}
                @if (count($this->existingAttributeLines) > 0)
                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                        <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/60">
                                <tr>
                                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">
                                        Atributo
                                    </th>
                                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">
                                        Valores
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 bg-white dark:bg-gray-800">
                                @foreach ($this->existingAttributeLines as $line)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                                        <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-200">
                                            {{ $line['attribute_name'] }}
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($line['values'] as $val)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                                                 bg-indigo-100 text-indigo-700
                                                                 dark:bg-indigo-900/40 dark:text-indigo-300 text-xs font-medium">
                                                        {{ $val['name'] }}
                                                        @if ($val['extra_price'] > 0)
                                                            <span class="text-indigo-400 dark:text-indigo-500">
                                                                (+{{ number_format($val['extra_price'], 2) }})
                                                            </span>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="py-12 text-center text-gray-400 dark:text-gray-500 border-2 border-dashed
                                border-gray-200 dark:border-gray-700 rounded-xl">
                        <i class="fa-solid fa-cubes text-4xl mb-3 block text-gray-300 dark:text-gray-600"></i>
                        <p class="font-medium">Sin variantes configuradas</p>
                        <p class="text-xs mt-1">Este producto es simple (sin combinaciones de atributos).</p>
                    </div>
                @endif

                {{-- Variantes actuales con precios editables --}}
                @if (count($variants) > 0)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">
                            <i class="fa-solid fa-cubes-stacked mr-2 text-indigo-500"></i>
                            Variantes ({{ count($variants) }}) — edita precios en la pestaña
                            <button wire:click="setTab('precios')" class="underline text-indigo-600 dark:text-indigo-400">
                                Precios
                            </button>
                        </h3>
                        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700/60">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Variante</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">SKU</th>
                                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">P. Venta</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Activa</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 bg-white dark:bg-gray-800">
                                    @foreach ($variants as $variantId => $variant)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    @if ($variant['is_default'])
                                                        <span class="px-1.5 py-0.5 rounded text-xs font-bold
                                                                     bg-indigo-100 text-indigo-600
                                                                     dark:bg-indigo-900/40 dark:text-indigo-300">
                                                            Default
                                                        </span>
                                                    @endif
                                                    <span class="font-medium text-gray-800 dark:text-gray-100">
                                                        {{ $variant['label'] }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">
                                                {{ $variant['sku'] }}
                                            </td>
                                            <td class="px-4 py-3 text-right text-gray-800 dark:text-gray-200">
                                                {{ number_format($variant['price_sale'] ?? 0, 2) }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if ($variant['active'])
                                                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                                @else
                                                    <i class="fa-solid fa-circle-xmark text-red-400"></i>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endif

            {{-- ══════════════════════════════════════════════
                 TAB: PRECIOS (variantes + listas de precios)
            ══════════════════════════════════════════════ --}}
            @if ($tab === 'precios')

                {{-- Precios individuales por variante --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-cubes-stacked text-indigo-500"></i>
                        Precios por variante
                        <span class="px-2 py-0.5 rounded-full text-xs bg-indigo-100 text-indigo-700
                                     dark:bg-indigo-900/40 dark:text-indigo-300">
                            {{ count($variants) }}
                        </span>
                    </h3>
                    @if (count($variants) > 0)
                        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700/60">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 min-w-[140px]">Variante</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">SKU</th>
                                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">P. Venta</th>
                                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">P. Mayoreo</th>
                                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">P. Compra</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Cód. barras</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Activa</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 bg-white dark:bg-gray-800">
                                    @foreach ($variants as $variantId => $variant)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-1.5">
                                                    @if ($variant['is_default'])
                                                        <span class="px-1.5 py-0.5 rounded text-xs font-bold
                                                                     bg-indigo-100 text-indigo-600
                                                                     dark:bg-indigo-900/40 dark:text-indigo-300">
                                                            Default
                                                        </span>
                                                    @endif
                                                    <span class="font-medium text-gray-800 dark:text-gray-100">
                                                        {{ $variant['label'] }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">
                                                {{ $variant['sku'] }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" step="0.01" min="0"
                                                    wire:model.defer="variants.{{ $variantId }}.price_sale"
                                                    placeholder="0.00"
                                                    class="w-28 text-right px-3 py-2 rounded-lg border border-gray-300
                                                           dark:border-gray-600 bg-white dark:bg-gray-800
                                                           text-gray-800 dark:text-gray-200
                                                           focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
                                                @error("variants.{$variantId}.price_sale")
                                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                                @enderror
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" step="0.01" min="0"
                                                    wire:model.defer="variants.{{ $variantId }}.price_wholesale"
                                                    placeholder="0.00"
                                                    class="w-28 text-right px-3 py-2 rounded-lg border border-gray-300
                                                           dark:border-gray-600 bg-white dark:bg-gray-800
                                                           text-gray-800 dark:text-gray-200
                                                           focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" step="0.01" min="0"
                                                    wire:model.defer="variants.{{ $variantId }}.price_purchase"
                                                    placeholder="0.00"
                                                    class="w-28 text-right px-3 py-2 rounded-lg border border-gray-300
                                                           dark:border-gray-600 bg-white dark:bg-gray-800
                                                           text-gray-800 dark:text-gray-200
                                                           focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="text"
                                                    wire:model.defer="variants.{{ $variantId }}.barcode"
                                                    placeholder="7751234567890"
                                                    class="w-36 px-3 py-2 rounded-lg border border-gray-300
                                                           dark:border-gray-600 bg-white dark:bg-gray-800
                                                           text-gray-800 dark:text-gray-200 font-mono text-xs
                                                           focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <input type="checkbox"
                                                    wire:model.defer="variants.{{ $variantId }}.active"
                                                    class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="py-8 text-center text-gray-400 border-2 border-dashed border-gray-200
                                    dark:border-gray-700 rounded-xl">
                            Sin variantes. Se aplicará precio único en la variante default.
                        </div>
                    @endif
                </div>

                {{-- Lista de precios --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                            <i class="fa-solid fa-tags text-indigo-500"></i>
                            Reglas de lista de precios
                        </h3>
                        @if (count($allPricelists) > 0)
                            <button wire:click="openPriceRuleModal"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                       bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition">
                                <i class="fa-solid fa-plus"></i> Agregar regla
                            </button>
                        @endif
                    </div>

                    @if (count($dbPricelistItems) > 0)
                        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                            <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/60">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Lista</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Método</th>
                                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Precio fijo</th>
                                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Descuento %</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Qty mín.</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Vigencia</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 bg-white dark:bg-gray-800">
                                    @foreach ($dbPricelistItems as $item)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">
                                                {{ $item['pricelist_name'] }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                                    {{ $item['compute_method'] === 'fixed'    ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : '' }}
                                                    {{ $item['compute_method'] === 'discount' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' : '' }}
                                                    {{ $item['compute_method'] === 'formula'  ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300' : '' }}">
                                                    {{ ucfirst($item['compute_method']) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono text-gray-700 dark:text-gray-300">
                                                {{ $item['fixed_price'] !== null ? number_format($item['fixed_price'], 2) : '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono text-gray-700 dark:text-gray-300">
                                                {{ $item['percent_discount'] !== null ? $item['percent_discount'] . '%' : '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">
                                                {{ $item['min_qty'] ?? 1 }}
                                            </td>
                                            <td class="px-4 py-3 text-center text-xs text-gray-500 dark:text-gray-400">
                                                @if ($item['date_start'] || $item['date_end'])
                                                    {{ $item['date_start'] ?? '∞' }} → {{ $item['date_end'] ?? '∞' }}
                                                @else
                                                    <span class="text-gray-300 dark:text-gray-600">Sin límite</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button wire:click="removePriceRule({{ $item['id'] }})"
                                                    wire:confirm="¿Eliminar esta regla de precio?"
                                                    class="text-red-500 hover:text-red-700 dark:hover:text-red-400 transition">
                                                    <i class="fa-solid fa-trash text-xs"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="py-10 text-center text-gray-400 border-2 border-dashed border-gray-200
                                    dark:border-gray-700 rounded-xl">
                            <i class="fa-solid fa-tags text-3xl mb-2 block text-gray-300 dark:text-gray-600"></i>
                            <p class="font-medium">Sin reglas de precio asignadas</p>
                            <p class="text-xs mt-1">Usa el botón "Agregar regla" para crear una.</p>
                        </div>
                    @endif
                </div>

                @can('Product Update')
                    <div class="pt-2 flex justify-end">
                        <button wire:click="update"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl
                                   bg-gradient-to-r from-indigo-600 to-sky-600
                                   hover:from-indigo-700 hover:to-sky-700
                                   text-white font-semibold shadow-sm transition">
                            <i class="fa-regular fa-floppy-disk"></i> Guardar precios
                        </button>
                    </div>
                @endcan
            @endif

            {{-- ══════════════════════════════════════════════
                 TAB: PUNTO DE VENTA
            ══════════════════════════════════════════════ --}}
            @if ($tab === 'pdv')
                <div class="space-y-6">

                    {{-- Toggle POS --}}
                    <div class="flex items-center gap-4 p-4 rounded-xl border
                                {{ $pos_ok ? 'border-indigo-200 bg-indigo-50 dark:border-indigo-800 dark:bg-indigo-900/20'
                                           : 'border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-700/20' }}">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" wire:model.live="pos_ok"
                                class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span>
                                <span class="font-semibold text-gray-800 dark:text-gray-100 text-sm">
                                    <i class="fa-solid fa-cash-register mr-1.5 text-indigo-500"></i>
                                    Disponible en Punto de Venta
                                </span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    Activa para que el producto aparezca en el POS.
                                </span>
                            </span>
                        </label>
                    </div>

                    @if ($pos_ok)
                        {{-- Categorías POS --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200 block mb-2">
                                Categorías POS
                            </label>

                            {{-- Tags seleccionadas --}}
                            @if (count($this->selectedPosCategories) > 0)
                                <div class="flex flex-wrap gap-2 mb-2">
                                    @foreach ($this->selectedPosCategories as $cat)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                                     bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40
                                                     dark:text-indigo-200 text-xs font-medium">
                                            <i class="fa-solid fa-tag text-indigo-500"></i>
                                            {{ $cat['label'] }}
                                            <button wire:click="removePosCategory({{ $cat['id'] }})"
                                                class="text-indigo-400 hover:text-red-500 transition ml-1">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Buscador --}}
                            <div class="relative">
                                <input wire:model.live.debounce.300ms="posCategorySearch" type="text"
                                    placeholder="Buscar o crear categoría POS..."
                                    class="{{ $ctrl }}">
                                @if (strlen(trim($posCategorySearch)) > 0 && count($filteredPosCategories) > 0)
                                    <div class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800
                                                border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden">
                                        @foreach ($filteredPosCategories as $c)
                                            <button wire:click="addPosCategory({{ $c['id'] }})"
                                                class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200
                                                       hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition">
                                                <i class="fa-solid fa-tag mr-2 text-indigo-400"></i>
                                                {{ $c['label'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                                @if ($this->canCreatePosCategory)
                                    <button wire:click="createPosCategory"
                                        class="absolute right-3 top-2.5 px-3 py-1.5 rounded-lg bg-indigo-600
                                               text-white text-xs font-semibold hover:bg-indigo-700 transition">
                                        <i class="fa-solid fa-plus mr-1"></i> Crear "{{ trim($posCategorySearch) }}"
                                    </button>
                                @endif
                            </div>
                            @error('posCategorySearch') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Productos adicionales (cross-sell) --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200 block mb-2">
                                Productos adicionales (cross-sell en POS)
                            </label>

                            @if (count($this->selectedAdditionalProducts) > 0)
                                <div class="flex flex-wrap gap-2 mb-2">
                                    @foreach ($this->selectedAdditionalProducts as $prod)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                                     bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40
                                                     dark:text-emerald-200 text-xs font-medium">
                                            <i class="fa-solid fa-box-open text-emerald-500"></i>
                                            {{ $prod['name'] }}
                                            <button wire:click="removeAdditionalProduct({{ $prod['id'] }})"
                                                class="text-emerald-400 hover:text-red-500 transition ml-1">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="relative">
                                <input wire:model.live.debounce.300ms="additionalProductSearch" type="text"
                                    placeholder="Buscar producto adicional..."
                                    class="{{ $ctrl }}">
                                @if (strlen(trim($additionalProductSearch)) > 0 && count($filteredAdditionalProducts) > 0)
                                    <div class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800
                                                border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden">
                                        @foreach ($filteredAdditionalProducts as $p)
                                            <button wire:click="addAdditionalProduct({{ $p['id'] }})"
                                                class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200
                                                       hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition">
                                                <i class="fa-solid fa-box mr-2 text-emerald-400"></i>
                                                {{ $p['name'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="py-8 text-center text-gray-400 border-2 border-dashed border-gray-200
                                    dark:border-gray-700 rounded-xl">
                            <i class="fa-solid fa-cash-register text-4xl mb-2 block text-gray-300 dark:text-gray-600"></i>
                            Activa "Disponible en Punto de Venta" para configurar las opciones POS.
                        </div>
                    @endif

                    @can('Product Update')
                        <div class="flex justify-end">
                            <button wire:click="update"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl
                                       bg-gradient-to-r from-indigo-600 to-sky-600
                                       hover:from-indigo-700 hover:to-sky-700
                                       text-white font-semibold shadow-sm transition">
                                <i class="fa-regular fa-floppy-disk"></i> Guardar POS
                            </button>
                        </div>
                    @endcan
                </div>
            @endif

            {{-- ══════════════════════════════════════════════
                 TAB: CONTABILIDAD
            ══════════════════════════════════════════════ --}}
            @if ($tab === 'accounting')
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- Cuenta de ventas --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Cuenta de ventas <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="account_sell_id" class="{{ $ctrl }} mt-2">
                                <option value="">— Seleccionar —</option>
                                @foreach ($accountOptions as $acc)
                                    <option value="{{ $acc['id'] }}">{{ $acc['label'] }}</option>
                                @endforeach
                            </select>
                            @error('account_sell_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Cuenta de compras --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Cuenta de compras <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="account_buy_id" class="{{ $ctrl }} mt-2">
                                <option value="">— Seleccionar —</option>
                                @foreach ($accountOptions as $acc)
                                    <option value="{{ $acc['id'] }}">{{ $acc['label'] }}</option>
                                @endforeach
                            </select>
                            @error('account_buy_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Impuestos de venta --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200 block mb-2">
                                Impuestos de venta
                            </label>
                            <div class="space-y-1.5 max-h-44 overflow-y-auto pr-1">
                                @foreach ($taxOptions as $tax)
                                    <label class="flex items-center gap-2.5 cursor-pointer group p-2 rounded-lg
                                                  hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                                        <input type="checkbox"
                                            wire:model="sale_tax_ids"
                                            value="{{ $tax['id'] }}"
                                            class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700 dark:text-gray-200">
                                            {{ $tax['name'] }}
                                            <span class="text-xs text-gray-400 ml-1">
                                                ({{ $tax['amount'] }}{{ $tax['amount_type'] === 'percent' ? '%' : '' }})
                                            </span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Impuestos de compra --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200 block mb-2">
                                Impuestos de compra
                            </label>
                            <div class="space-y-1.5 max-h-44 overflow-y-auto pr-1">
                                @foreach ($taxOptions as $tax)
                                    <label class="flex items-center gap-2.5 cursor-pointer group p-2 rounded-lg
                                                  hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                                        <input type="checkbox"
                                            wire:model="purchase_tax_ids"
                                            value="{{ $tax['id'] }}"
                                            class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700 dark:text-gray-200">
                                            {{ $tax['name'] }}
                                            <span class="text-xs text-gray-400 ml-1">
                                                ({{ $tax['amount'] }}{{ $tax['amount_type'] === 'percent' ? '%' : '' }})
                                            </span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Detracción (Perú) --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Detracción <span class="text-xs font-normal text-gray-400">(SUNAT)</span>
                            </label>
                            <select wire:model.defer="detraction_id" class="{{ $ctrl }} mt-2">
                                <option value="">— No aplica —</option>
                                @foreach ($detractionOptions as $d)
                                    <option value="{{ $d['id'] }}">
                                        {{ $d['code'] }} – {{ $d['name'] }} ({{ $d['rate'] }}%)
                                    </option>
                                @endforeach
                            </select>
                            @error('detraction_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-end">
                            <div class="w-full p-3 rounded-xl border border-blue-200 dark:border-blue-800
                                        bg-blue-50 dark:bg-blue-900/20 text-sm text-blue-700 dark:text-blue-300">
                                <i class="fa-solid fa-circle-info mr-2"></i>
                                Las cuentas por defecto se configuran en
                                <strong>Configuración → Contabilidad</strong>.
                                Si no asignas cuenta, el sistema usará la predeterminada según el tipo de producto.
                            </div>
                        </div>

                    </div>

                    @can('Product Update')
                        <div class="flex justify-end">
                            <button wire:click="update"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl
                                       bg-gradient-to-r from-indigo-600 to-sky-600
                                       hover:from-indigo-700 hover:to-sky-700
                                       text-white font-semibold shadow-sm transition">
                                <i class="fa-regular fa-floppy-disk"></i> Guardar contabilidad
                            </button>
                        </div>
                    @endcan
                </div>
            @endif

            {{-- ══════════════════════════════════════════════
                 TAB: SUSCRIPCIONES
            ══════════════════════════════════════════════ --}}
            @if ($tab === 'subscriptions')
                <div class="space-y-6">

                    {{-- Toggle recurrencia --}}
                    <div class="flex items-center gap-4 p-4 rounded-xl border
                                {{ $is_recurring ? 'border-purple-200 bg-purple-50 dark:border-purple-800 dark:bg-purple-900/20'
                                                 : 'border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-700/20' }}">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" wire:model.live="is_recurring"
                                class="w-5 h-5 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <span>
                                <span class="font-semibold text-gray-800 dark:text-gray-100 text-sm">
                                    <i class="fa-solid fa-rotate mr-1.5 text-purple-500"></i>
                                    Producto recurrente (suscripción)
                                </span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    Activa si este producto genera una suscripción al venderse.
                                </span>
                            </span>
                        </label>
                    </div>

                    @if ($is_recurring)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            {{-- Plan de suscripción --}}
                            <div>
                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                    Plan de suscripción <span class="text-red-500">*</span>
                                </label>
                                <select wire:model.defer="subscription_plan_id" class="{{ $ctrl }} mt-2">
                                    <option value="">— Seleccionar plan —</option>
                                    @foreach ($subscriptionPlans as $plan)
                                        <option value="{{ $plan['id'] }}">
                                            {{ $plan['name'] }}
                                            @if ($plan['interval_count'])
                                                (cada {{ $plan['interval_count'] }} {{ $plan['interval_unit'] }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('subscription_plan_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Precio de renovación --}}
                            <div>
                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                    Precio de renovación <span class="text-red-500">*</span>
                                </label>
                                <div class="relative mt-2">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">S/</span>
                                    <input wire:model.defer="recurring_price" type="number" step="0.01" min="0"
                                        placeholder="0.00"
                                        class="{{ $ctrl }} pl-9 text-right">
                                </div>
                                @error('recurring_price') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                        </div>

                        <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20
                                    border border-amber-200 dark:border-amber-800 flex gap-3">
                            <i class="fa-solid fa-lightbulb text-amber-500 mt-0.5 flex-shrink-0"></i>
                            <p class="text-sm text-amber-800 dark:text-amber-200 leading-relaxed">
                                <strong>Nota:</strong> Las fechas de inicio y fin de la suscripción se asignan
                                automáticamente al momento de la venta (Orden de Venta / Factura).
                                El precio de renovación puede diferir del precio de venta inicial.
                            </p>
                        </div>
                    @else
                        <div class="py-8 text-center text-gray-400 border-2 border-dashed border-gray-200
                                    dark:border-gray-700 rounded-xl">
                            <i class="fa-solid fa-rotate text-4xl mb-2 block text-gray-300 dark:text-gray-600"></i>
                            Activa la casilla para configurar la suscripción.
                        </div>
                    @endif

                    @can('Product Update')
                        <div class="flex justify-end">
                            <button wire:click="update"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl
                                       bg-gradient-to-r from-indigo-600 to-sky-600
                                       hover:from-indigo-700 hover:to-sky-700
                                       text-white font-semibold shadow-sm transition">
                                <i class="fa-regular fa-floppy-disk"></i> Guardar suscripción
                            </button>
                        </div>
                    @endcan
                </div>
            @endif

            {{-- ══════════════════════════════════════════════
                 TAB: WEB / SEO
            ══════════════════════════════════════════════ --}}
            @if ($tab === 'web')
                <div class="space-y-6">

                    {{-- Contenido web --}}
                    <div class="bg-white dark:bg-gray-700/30 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-align-left text-blue-500"></i> Contenido web
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                    Descripción corta
                                </label>
                                <input wire:model.defer="short_description" type="text"
                                    placeholder="Resumen rápido visible en listados..."
                                    class="{{ $ctrl }} mt-2">
                                @error('short_description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div wire:ignore x-data="{
                                content: @entangle('long_description'),
                                isFocused() { return document.activeElement === this.$refs.trix }
                            }" x-init="
                                $watch('content', value => {
                                    if (!isFocused() && value !== $refs.trix.editor.getDocument().toString()) {
                                        $refs.trix.editor.loadHTML(value ?? '');
                                    }
                                })"
                                x-on:trix-change="content = $event.target.value">

                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-200 block mb-2">
                                    Descripción larga (rich text)
                                </label>
                                <trix-editor x-ref="trix"
                                    class="trix-content border border-gray-300 dark:border-gray-600 rounded-xl
                                           shadow-sm min-h-[180px] focus:ring-indigo-500 bg-white dark:bg-gray-800"
                                    placeholder="Especificaciones, beneficios y detalles del producto...">
                                </trix-editor>
                            </div>
                        </div>
                    </div>

                    {{-- SEO / Google --}}
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800 p-5">
                        <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-4 flex items-center gap-2">
                            <i class="fab fa-google text-blue-500"></i> Optimización SEO (Google)
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <div class="flex justify-between items-baseline mb-1">
                                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                        Meta título
                                    </label>
                                    <span class="text-xs {{ strlen($title_google ?? '') > 60 ? 'text-amber-500' : 'text-gray-400' }}">
                                        {{ strlen($title_google ?? '') }}/70
                                    </span>
                                </div>
                                <input wire:model.live="title_google" type="text" maxlength="70"
                                    placeholder="Título para resultados de Google..."
                                    class="{{ $ctrl }}">
                                <p class="text-xs text-gray-400 mt-1">Ideal: 50-70 caracteres.</p>
                            </div>

                            <div class="md:col-span-2">
                                <div class="flex justify-between items-baseline mb-1">
                                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                        Meta descripción
                                    </label>
                                    <span class="text-xs {{ strlen($description_google ?? '') > 150 ? 'text-amber-500' : 'text-gray-400' }}">
                                        {{ strlen($description_google ?? '') }}/160
                                    </span>
                                </div>
                                <textarea wire:model.live="description_google" rows="3" maxlength="160"
                                    placeholder="Descripción persuasiva para atraer clics..."
                                    class="{{ $ctrl }} h-auto py-3"></textarea>
                                <p class="text-xs text-gray-400 mt-1">Ideal: 120-160 caracteres.</p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                    Palabras clave
                                </label>
                                <input wire:model.defer="keywords_google" type="text"
                                    placeholder="ej. zapatillas, running, deportivas, hombre"
                                    class="{{ $ctrl }} mt-1">
                            </div>
                        </div>

                        {{-- Preview Google --}}
                        @if (trim($title_google ?? '') || trim($description_google ?? ''))
                            <div class="mt-5 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                                <p class="text-xs font-semibold text-gray-400 mb-2 uppercase tracking-wide">
                                    Vista previa en Google
                                </p>
                                <p class="text-blue-700 dark:text-blue-400 text-lg font-medium leading-tight truncate">
                                    {{ $title_google ?: $product_template->name }}
                                </p>
                                <p class="text-green-700 dark:text-green-500 text-xs mt-0.5">
                                    tuerp.com/productos/{{ $product_template->slug }}
                                </p>
                                <p class="text-gray-600 dark:text-gray-300 text-sm mt-1 line-clamp-2">
                                    {{ $description_google ?: 'Sin meta descripción.' }}
                                </p>
                            </div>
                        @endif
                    </div>

                    @can('Product Update')
                        <div class="flex justify-end">
                            <button wire:click="update"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl
                                       bg-gradient-to-r from-indigo-600 to-sky-600
                                       hover:from-indigo-700 hover:to-sky-700
                                       text-white font-semibold shadow-sm transition">
                                <i class="fa-regular fa-floppy-disk"></i> Guardar web / SEO
                            </button>
                        </div>
                    @endcan
                </div>
            @endif

        </div>{{-- /p-6 --}}
    </div>{{-- /card tabs --}}

    {{-- ════════════════════════════════════════════════════════
         MODAL: AGREGAR REGLA DE PRECIO
    ════════════════════════════════════════════════════════ --}}
    @if ($showPriceModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
            {{-- Backdrop --}}
            <div wire:click="$set('showPriceModal', false)"
                class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

            {{-- Modal --}}
            <div class="relative z-10 w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <i class="fa-solid fa-tag text-indigo-500"></i> Nueva regla de precio
                    </h3>
                    <button wire:click="$set('showPriceModal', false)"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <div class="p-6 space-y-4">

                    {{-- Lista de precios --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Lista de precios <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.defer="modalRule.pricelist_id" class="{{ $ctrl }} mt-2">
                            <option value="">— Seleccionar —</option>
                            @foreach ($allPricelists as $pl)
                                <option value="{{ $pl['id'] }}">{{ $pl['name'] }}</option>
                            @endforeach
                        </select>
                        @error('modalRule.pricelist_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Método de cálculo --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Método de cálculo</label>
                        <select wire:model.live="modalRule.compute_method" class="{{ $ctrl }} mt-2">
                            <option value="fixed">Precio fijo</option>
                            <option value="discount">Descuento (%)</option>
                            <option value="formula">Fórmula</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        @if ($modalRule['compute_method'] === 'fixed')
                            <div class="col-span-2">
                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Precio fijo</label>
                                <input wire:model.defer="modalRule.fixed_price" type="number" step="0.01" min="0"
                                    placeholder="0.00" class="{{ $ctrl }} mt-2 text-right">
                            </div>
                        @elseif ($modalRule['compute_method'] === 'discount')
                            <div class="col-span-2">
                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Descuento (%)</label>
                                <input wire:model.defer="modalRule.percent_discount" type="number" step="0.01" min="0" max="100"
                                    placeholder="0" class="{{ $ctrl }} mt-2 text-right">
                            </div>
                        @endif

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Cantidad mínima</label>
                            <input wire:model.defer="modalRule.min_qty" type="number" min="0" step="1"
                                placeholder="1" class="{{ $ctrl }} mt-2">
                        </div>

                        <div></div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Fecha inicio</label>
                            <input wire:model.defer="modalRule.date_start" type="date" class="{{ $ctrl }} mt-2">
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Fecha fin</label>
                            <input wire:model.defer="modalRule.date_end" type="date" class="{{ $ctrl }} mt-2">
                        </div>
                    </div>

                </div>

                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                    <button wire:click="$set('showPriceModal', false)"
                        class="px-5 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-700
                               text-gray-800 dark:text-gray-200 font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        Cancelar
                    </button>
                    <button wire:click="savePriceRule"
                        class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700
                               text-white font-semibold shadow-sm transition">
                        <i class="fa-solid fa-plus mr-1.5"></i> Agregar regla
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
