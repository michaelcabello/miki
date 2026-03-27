<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard'  => route('dashboard'),
            'Productos'  => route('admin.products.index'),
            'Editar'     => '#',
        ]" />
    </div>

    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Editar producto</h1>
                <p class="text-sm text-gray-500 dark:text-gray-300">
                    <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $product_template->name }}</span>
                    &nbsp;·&nbsp; Slug:
                    <span class="font-mono text-xs text-gray-400">{{ $product_template->slug }}</span>
                    &nbsp;·&nbsp; ID: {{ $product_template->id }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.products.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                           bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold transition
                           hover:bg-gray-200 dark:hover:bg-gray-600">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>
                <a href="{{ route('admin.products.variants', $product_template) }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                           bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300
                           border border-indigo-200 dark:border-indigo-800 font-semibold transition text-sm">
                    <i class="fa-solid fa-cubes-stacked"></i> Ver variantes
                </a>
                @can('Product Update')
                    <button wire:click="update"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                               bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition">
                        <i class="fa-regular fa-floppy-disk"></i> Guardar
                    </button>
                @endcan
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="px-6 pt-5">
            <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700">
                <button type="button" wire:click="setTab('general')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'general' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-regular fa-file-lines mr-2"></i> General
                </button>
                <button type="button" wire:click="setTab('variants')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'variants' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-solid fa-cubes-stacked mr-2"></i>
                    Variantes y Precios
                    <span class="ml-1 px-1.5 py-0.5 rounded-full text-xs bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200">
                        {{ count($variants) }}
                    </span>
                </button>
                <button type="button" wire:click="setTab('accounting')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'accounting' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-solid fa-receipt mr-2"></i> Fiscal / Contable
                </button>
            </div>
        </div>

        @php
            $controlBase = "w-full h-12 px-4 rounded-xl border border-gray-300 dark:border-gray-600
                            bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                            focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition";
        @endphp

        <div class="p-6 space-y-6">

            {{-- ══════════════════════════════════════════════
                 TAB: GENERAL
            ══════════════════════════════════════════════ --}}
            @if ($tab === 'general')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- Nombre --}}
                    <div class="md:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input wire:model.defer="name" type="text"
                            class="{{ $controlBase }} mt-2"
                            placeholder="Nombre del producto">
                        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tipo --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Tipo</label>
                        <select wire:model.defer="type" class="{{ $controlBase }} mt-2">
                            <option value="goods">Bienes</option>
                            <option value="service">Servicio</option>
                            <option value="combo">Combo</option>
                        </select>
                        @error('type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Categoría --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Categoría</label>
                        <select wire:model.defer="category_id" class="{{ $controlBase }} mt-2">
                            <option value="">— Sin categoría —</option>
                            @foreach ($categoryOptions as $c)
                                <option value="{{ $c['id'] }}">{{ $c['label'] }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- UoM Venta --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Unidad de Medida (Venta)</label>
                        <select wire:model.live="uom_id" class="{{ $controlBase }} mt-2">
                            <option value="">— Seleccionar —</option>
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
                    </div>

                    {{-- UoM Compra --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Unidad de Medida (Compra)</label>
                        <select wire:model.defer="uom_po_id" class="{{ $controlBase }} mt-2">
                            <option value="">— Seleccionar —</option>
                            @foreach ($uomPurchaseOptions as $uom)
                                <option value="{{ $uom['id'] }}">
                                    {{ $uom['name'] }}{{ $uom['symbol'] ? ' (' . $uom['symbol'] . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Marca --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Marca</label>
                        <select wire:model.live="brand_id" class="{{ $controlBase }} mt-2">
                            <option value="">— Sin marca —</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand['id'] }}">{{ $brand['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Modelo --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Modelo</label>
                        <select wire:model.defer="modello_id" class="{{ $controlBase }} mt-2"
                            @if (!$brand_id) disabled @endif>
                            <option value="">— Sin modelo —</option>
                            @foreach ($modelloOptions as $m)
                                <option value="{{ $m['id'] }}">{{ $m['name'] }}</option>
                            @endforeach
                        </select>
                        @if (!$brand_id)
                            <p class="text-xs text-gray-400 mt-1">Selecciona una marca primero.</p>
                        @endif
                    </div>

                    {{-- Temporada --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Temporada</label>
                        <select wire:model.defer="season_id" class="{{ $controlBase }} mt-2">
                            <option value="">— Sin temporada —</option>
                            @foreach ($seasons as $season)
                                <option value="{{ $season['id'] }}">{{ $season['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Checks --}}
                    <div class="md:col-span-2">
                        <div class="flex flex-wrap gap-6 p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                <input type="checkbox" wire:model.defer="sale_ok" class="w-5 h-5 rounded">
                                <span>Se vende</span>
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                <input type="checkbox" wire:model.defer="purchase_ok" class="w-5 h-5 rounded">
                                <span>Se compra</span>
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                <input type="checkbox" wire:model.defer="pos_ok" class="w-5 h-5 rounded">
                                <span>Punto de Venta</span>
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                <input type="checkbox" wire:model.defer="active" class="w-5 h-5 rounded">
                                <span>Activo</span>
                            </label>
                        </div>
                    </div>

                    {{-- Suscripción --}}
                    <div class="md:col-span-2">
                        <div class="rounded-xl border {{ $is_recurring ? 'border-indigo-300 dark:border-indigo-700' : 'border-gray-200 dark:border-gray-700' }}
                                    bg-gray-50 dark:bg-gray-900/30 p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="font-semibold text-sm text-gray-700 dark:text-gray-200">
                                        <i class="fa-solid fa-rotate mr-2 text-indigo-500"></i>
                                        Producto recurrente (Suscripción)
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        Activa para generar facturas periódicas automáticas.
                                    </p>
                                </div>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model.live="is_recurring" class="sr-only peer">
                                    <div class="relative w-12 h-7 bg-gray-300 dark:bg-gray-600 rounded-full
                                                peer peer-checked:bg-indigo-600 transition">
                                        <div class="absolute top-1 left-1 w-5 h-5 bg-white rounded-full
                                                    peer-checked:translate-x-5 transition"></div>
                                    </div>
                                </label>
                            </div>

                            @if ($is_recurring)
                                <div>
                                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                        Plan de suscripción <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.defer="subscription_plan_id" class="{{ $controlBase }} mt-2">
                                        <option value="">— Seleccionar plan —</option>
                                        @foreach ($subscriptionPlans as $sp)
                                            <option value="{{ $sp['id'] }}">
                                                {{ $sp['name'] }}
                                                (Cada {{ $sp['interval_count'] }}
                                                {{ match($sp['interval_unit']) {
                                                    'day'   => 'día(s)',
                                                    'week'  => 'semana(s)',
                                                    'month' => 'mes(es)',
                                                    'year'  => 'año(s)',
                                                    default => $sp['interval_unit']
                                                } }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subscription_plan_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

                @can('Product Update')
                    <div class="pt-2 flex justify-end">
                        <button wire:click="update"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl
                                   bg-gradient-to-r from-indigo-600 to-sky-600 hover:from-indigo-700 hover:to-sky-700
                                   text-white font-semibold shadow-sm transition">
                            <i class="fa-regular fa-floppy-disk"></i> Guardar producto
                        </button>
                    </div>
                @endcan
            @endif

            {{-- ══════════════════════════════════════════════
                 TAB: VARIANTES Y PRECIOS
            ══════════════════════════════════════════════ --}}
            @if ($tab === 'variants')
                <div class="space-y-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Precios por variante</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-300">
                            Los atributos y combinaciones no se pueden cambiar desde aquí.
                            Edita precios, código de barras y activa/desactiva cada variante.
                        </p>
                    </div>

                    {{-- Tabla de variantes --}}
                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-900/40">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Variante</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">SKU</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">P. Venta</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">P. Mayoreo</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">P. Compra</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Cód. Barras</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Activa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($variants as $variantId => $variant)
                                    <tr class="border-t border-gray-100 dark:border-gray-700
                                               {{ $variant['is_default'] ? 'bg-indigo-50/50 dark:bg-indigo-950/20' : 'hover:bg-gray-50/60 dark:hover:bg-gray-900/30' }}">

                                        {{-- Nombre variante --}}
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                @if ($variant['is_default'])
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 font-semibold">
                                                        Default
                                                    </span>
                                                @endif
                                                <span class="font-medium text-gray-800 dark:text-gray-100">
                                                    {{ $variant['label'] }}
                                                </span>
                                            </div>
                                        </td>

                                        {{-- SKU (solo lectura) --}}
                                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 font-mono">
                                            {{ $variant['sku'] }}
                                        </td>

                                        {{-- Precio venta --}}
                                        <td class="px-4 py-3">
                                            <input type="number" step="0.01" min="0"
                                                wire:model.defer="variants.{{ $variantId }}.price_sale"
                                                placeholder="0.00"
                                                class="w-28 text-right px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                                                       bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200
                                                       focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
                                            @error("variants.{$variantId}.price_sale") <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                        </td>

                                        {{-- Precio mayoreo --}}
                                        <td class="px-4 py-3">
                                            <input type="number" step="0.01" min="0"
                                                wire:model.defer="variants.{{ $variantId }}.price_wholesale"
                                                placeholder="0.00"
                                                class="w-28 text-right px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                                                       bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200
                                                       focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
                                        </td>

                                        {{-- Precio compra --}}
                                        <td class="px-4 py-3">
                                            <input type="number" step="0.01" min="0"
                                                wire:model.defer="variants.{{ $variantId }}.price_purchase"
                                                placeholder="0.00"
                                                class="w-28 text-right px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                                                       bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200
                                                       focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
                                        </td>

                                        {{-- Código de barras --}}
                                        <td class="px-4 py-3">
                                            <input type="text"
                                                wire:model.defer="variants.{{ $variantId }}.barcode"
                                                placeholder="Ej: 7751234567890"
                                                class="w-36 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                                                       bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200
                                                       focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 font-mono text-xs">
                                        </td>

                                        {{-- Activa --}}
                                        <td class="px-4 py-3 text-center">
                                            <input type="checkbox"
                                                wire:model.defer="variants.{{ $variantId }}.active"
                                                class="w-5 h-5 rounded border-gray-300 text-indigo-600">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if (empty($variants))
                        <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400 border border-dashed border-gray-300 dark:border-gray-700 rounded-xl">
                            <i class="fa-solid fa-cubes-stacked text-4xl mb-2 block text-gray-300"></i>
                            Este producto no tiene variantes.
                        </div>
                    @endif

                    @can('Product Update')
                        <div class="pt-2 flex justify-end">
                            <button wire:click="update"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl
                                       bg-gradient-to-r from-indigo-600 to-sky-600 hover:from-indigo-700 hover:to-sky-700
                                       text-white font-semibold shadow-sm transition">
                                <i class="fa-regular fa-floppy-disk"></i> Guardar precios
                            </button>
                        </div>
                    @endcan
                </div>
            @endif

            {{-- ══════════════════════════════════════════════
                 TAB: FISCAL / CONTABLE
            ══════════════════════════════════════════════ --}}
            @if ($tab === 'accounting')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- Detracción --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Detracción</label>
                        <select wire:model.defer="detraction_id" class="{{ $controlBase }} mt-2">
                            <option value="">— No aplica —</option>
                            @foreach ($detractions as $d)
                                <option value="{{ $d['id'] }}">
                                    {{ $d['code'] }} – {{ $d['name'] }} ({{ $d['rate'] }}%)
                                </option>
                            @endforeach
                        </select>
                        @error('detraction_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-end">
                        <div class="rounded-lg border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-3 text-sm text-amber-700 dark:text-amber-300 w-full">
                            <i class="fa-solid fa-triangle-exclamation mr-2"></i>
                            Las cuentas contables (compra/venta) se configuran desde el Plan Contable.
                        </div>
                    </div>

                </div>

                @can('Product Update')
                    <div class="pt-2 flex justify-end">
                        <button wire:click="update"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl
                                   bg-gradient-to-r from-indigo-600 to-sky-600 hover:from-indigo-700 hover:to-sky-700
                                   text-white font-semibold shadow-sm transition">
                            <i class="fa-regular fa-floppy-disk"></i> Guardar
                        </button>
                    </div>
                @endcan
            @endif

        </div>
    </div>
</div>
