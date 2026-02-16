<div class="space-y-6">

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">POS Demo (Pruebas de Secuencia)</h2>
        <p class="text-sm text-gray-500 dark:text-gray-300">
            Elige Lista de precios, Variante y Cantidad. El sistema calcula el precio final y te muestra qué regla ganó.
        </p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
        <div class="md:col-span-4">
            <x-label>Lista de precios</x-label>
            <select wire:model.live="pricelist_id" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                <option value="">-- Seleccionar --</option>
                @foreach($pricelists as $pl)
                    <option value="{{ $pl->id }}">{{ $pl->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="md:col-span-6">
            <x-label>Producto / Variante</x-label>
            <select wire:model.live="product_variant_id" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                <option value="">-- Seleccionar --</option>
                @foreach($variants as $v)
                    <option value="{{ $v->id }}">
                        {{ $v->sku }}{{ $v->variant_name ? ' · '.$v->variant_name : '' }} — Base: {{ number_format((float)$v->price_sale, 2) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="md:col-span-2">
            <x-label>Cantidad</x-label>
            <input type="number" min="1" step="1" wire:model.live="qty"
                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"/>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-xs text-gray-500">Precio base (price_sale)</div>
            <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                {{ $basePrice !== null ? number_format($basePrice, 2) : '—' }}
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-xs text-gray-500">Precio final (lista aplicada)</div>
            <div class="text-2xl font-bold text-indigo-600">
                {{ $finalPrice !== null ? number_format($finalPrice, 2) : '—' }}
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-xs text-gray-500">Regla ganadora</div>

            @if($appliedRule)
                <div class="mt-1 text-sm text-gray-800 dark:text-gray-100 space-y-1">
                    <div><span class="font-semibold">ID:</span> {{ $appliedRule['id'] }}</div>
                    <div><span class="font-semibold">applied_on:</span> {{ $appliedRule['applied_on'] }}</div>
                    <div><span class="font-semibold">sequence:</span> {{ $appliedRule['sequence'] }}</div>
                    <div><span class="font-semibold">min_qty:</span> {{ number_format($appliedRule['min_qty'], 2) }}</div>
                    <div><span class="font-semibold">método:</span> {{ $appliedRule['compute_method'] }}</div>

                    @if($appliedRule['compute_method'] === 'discount')
                        <div><span class="font-semibold">%</span> {{ number_format((float)$appliedRule['percent_discount'], 2) }}</div>
                    @elseif($appliedRule['compute_method'] === 'fixed')
                        <div><span class="font-semibold">fijo:</span> {{ number_format((float)$appliedRule['fixed_price'], 2) }}</div>
                    @else
                        <div class="text-xs text-gray-500">Fórmula</div>
                    @endif
                </div>
            @else
                <div class="mt-2 text-sm text-gray-500">No hay regla: se usa precio base.</div>
            @endif
        </div>
    </div>

</div>
