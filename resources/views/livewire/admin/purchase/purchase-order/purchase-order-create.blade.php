<div class="space-y-6">
    {{-- 1. Breadcrumb --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <div class="flex items-center justify-between">


            <x-breadcrumb :links="[
                'Dashboard' => route('dashboard'),
                'Compras' => route('purchase.order.index'),
                $order && $order->name ? $order->name : 'Nueva Solicitud de Cotización' => '#',
            ]" />


            {{-- BARRA DE ACCIONES ODOO STYLE --}}
            <div class="flex flex-wrap items-center gap-2 bg-gray-50 p-3 rounded-t-xl border-b border-gray-200">
                @if ($state === 'draft')
                    <button wire:click="confirmOrder"
                        class="bg-indigo-600 text-white px-4 py-1.5 rounded-md font-bold text-sm hover:bg-indigo-700 transition shadow-sm">
                        Confirmar Pedido
                    </button>
                    <button wire:click="sendEmailPO"
                        class="bg-white border border-gray-300 text-gray-700 px-4 py-1.5 rounded-md font-bold text-sm hover:bg-gray-50 transition">
                        Enviar por correo
                    </button>
                @endif

                @if ($state === 'purchase')
                    {{-- @if ($this->picking_count > 0)
                        <button wire:click="receiveProducts"
                            class="bg-indigo-600 text-white px-4 py-1.5 rounded-md font-bold text-sm hover:bg-indigo-700 transition shadow-sm">
                            Recibir Productos
                        </button>
                    @endif --}}

                    @if ($this->pending_pickings_count > 0)
                        <button wire:click="receiveProducts" class="bg-indigo-600 text-white ...">
                            Recibir Productos
                        </button>
                    @else
                        <button disabled class="bg-gray-200 text-gray-500 cursor-not-allowed ...">
                            Productos Recibidos
                        </button>
                    @endif

                    <button wire:click="createBill"
                        class="bg-white border border-indigo-600 text-indigo-600 px-4 py-1.5 rounded-md font-bold text-sm hover:bg-indigo-50 transition">
                        Crear Factura
                    </button>

                    <button wire:click="sendEmailPO"
                        class="bg-white border border-gray-300 text-gray-700 px-4 py-1.5 rounded-md font-bold text-sm hover:bg-gray-50 transition">
                        Enviar Orden de Compra
                    </button>
                @endif

                @if ($order && $order->exists)
                    <button wire:click="viewPdf"
                        class="bg-white border border-gray-300 text-gray-700 px-4 py-1.5 rounded-md font-bold text-sm hover:bg-gray-50 transition">
                        Imprimir
                    </button>

                    @if ($state !== 'cancel' && $state !== 'done')
                        <button wire:confirm="¿Estás seguro de cancelar esta orden?" wire:click="cancelOrder"
                            class="bg-white border border-gray-300 text-gray-700 px-4 py-1.5 rounded-md font-bold text-sm hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition">
                            Cancelar
                        </button>
                    @endif
                @endif
            </div>




            @if ($currency_id && $moneda_base_id && $currency_id != $moneda_base_id)
                @php
                    $tc_actual = \App\Models\CurrencyRate::where('currency_id', $currency_id)
                        ->orderBy('date', 'desc')
                        ->first();
                @endphp
                <div
                    class="hidden md:flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold border
                    {{ $tc_actual && $tc_actual->date->isToday() ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-amber-50 border-amber-200 text-amber-700' }}">
                    @if ($tc_actual)
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>TC ({{ $tc_actual->date->format('d/m') }}): C {{ number_format($tc_actual->buy_rate, 3) }}
                            | V {{ number_format($tc_actual->sell_rate, 3) }}</span>
                    @else
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>TC no sincronizado hoy</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- 2. Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            {{-- <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Nueva Solicitud de Cotización</h1>
                <p class="text-sm text-gray-500">Gestión de precios y proveedores para TICOM.</p>
            </div> --}}
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                    {{ $order && $order->name ? $order->name : 'Nueva Solicitud de Cotización' }}
                </h1>
                <p class="text-sm text-gray-500">
                    {{ $order && $order->name ? 'Detalles de la solicitud de compra.' : 'Gestión de precios y proveedores.' }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{-- {{ route('admin.purchase.index') }} --}}"
                    class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-800 font-semibold transition hover:bg-gray-200">
                    Cancelar
                </a>
                <button wire:click="save"
                    class="px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-sky-600 text-white font-semibold shadow-sm transition active:scale-95">
                    Guardar Orden
                </button>
            </div>
        </div>
    </div>

    {{-- 3. Formulario (Sin overflow-hidden en el contenedor principal) --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-6 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                {{-- Proveedor --}}
                <div class="md:col-span-7">
                    <label class="text-xs font-bold text-gray-500 uppercase block mb-2">Proveedor</label>
                    <div class="relative">
                        @if ($partner_id)
                            <div
                                class="flex items-center justify-between bg-indigo-50 p-2.5 rounded-xl border border-indigo-100">
                                <span class="font-bold text-indigo-700 ml-2"><i class="fa-solid fa-user-check mr-2"></i>
                                    {{ $partner_name }}</span>
                                <button type="button" wire:click="$set('partner_id', null)"
                                    class="text-xs font-bold text-red-600 hover:underline">Cambiar</button>
                            </div>
                        @else
                            <div class="relative">
                                <i
                                    class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="text" wire:model.live.debounce.300ms="search_partner"
                                    placeholder="Buscar por nombre o RUC..."
                                    class="w-full h-11 pl-11 rounded-xl border border-gray-300 focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition text-sm">
                            </div>
                            {{-- Resultados de Proveedor con wire:key --}}
                            @if (count($partner_results) > 0)
                                <div
                                    class="absolute z-50 w-full bg-white shadow-2xl rounded-xl mt-2 border border-gray-100 overflow-hidden">
                                    @foreach ($partner_results as $res)
                                        <button type="button" wire:key="partner-{{ $res['id'] }}"
                                            wire:click="selectPartner({{ $res['id'] }}, '{{ addslashes($res['name']) }}')"
                                            class="w-full p-3 hover:bg-indigo-50 text-left flex justify-between items-center border-b last:border-0 transition">
                                            <span
                                                class="text-sm font-semibold text-gray-700">{{ $res['name'] }}</span>
                                            <span class="text-xs text-gray-400">{{ $res['document_number'] }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>
                    @error('partner_id')
                        <span class="text-red-500 text-[10px] font-bold mt-1">{{ $message }}</span>
                    @enderror
                </div>

                @error('lines')
                    <span class="text-red-500 text-xs font-bold block mt-2">{{ $message }}</span>
                @enderror

                {{-- Moneda y Fecha --}}
                <div class="md:col-span-3">
                    <label class="text-xs font-bold text-gray-500 uppercase block mb-2">Moneda</label>
                    <select wire:model.live="currency_id"
                        class="w-full h-11 px-4 rounded-xl border border-gray-300 text-sm font-bold text-indigo-700">
                        @foreach ($currencies as $curr)
                            <option value="{{ $curr->id }}">{{ $curr->name }} ({{ $curr->abbreviation }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs font-bold text-gray-500 uppercase block mb-2">Fecha Límite</label>
                    <input type="date" wire:model="date_approve"
                        class="w-full h-11 px-4 rounded-xl border border-gray-300 text-sm">
                </div>
            </div>

            {{-- Tabla de Productos (Contenedor con overflow-visible) --}}
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <i class="fa-solid fa-list-ul text-indigo-500"></i> Líneas de productos
                </h3>

                {{-- CAMBIO CRÍTICO: Eliminado overflow-hidden aquí --}}
                <div class="border border-gray-200 rounded-2xl overflow-visible">
                    <table class="w-full text-left">
                        <thead>
                            <tr
                                class="bg-gray-50 text-[11px] font-black text-gray-500 uppercase tracking-wider border-b">
                                <th class="py-4 px-4">Producto</th>
                                <th class="py-4 px-2 w-24 text-center">UoM</th>
                                <th class="py-4 px-2 w-32 text-center">Cantidad</th>
                                <th class="py-4 px-2 w-32 text-right">Precio Unit.</th>
                                <th class="py-4 px-4 w-40 text-right">Subtotal</th>
                                <th class="py-4 px-4 w-12"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($lines as $index => $line)
                                {{-- wire:key vital para que Livewire no rompa la búsqueda en bucles --}}
                                <tr wire:key="line-{{ $index }}"
                                    class="group hover:bg-gray-50/50 transition-colors">
                                    <td class="py-3 px-4 relative">
                                        <input type="text"
                                            wire:model.live.debounce.300ms="lines.{{ $index }}.product_search"
                                            placeholder="Buscar producto..."
                                            class="w-full bg-transparent border-0 focus:ring-0 text-sm font-semibold p-0">

                                        @if (count($line['product_results']) > 0)
                                            <div
                                                class="absolute z-40 left-0 w-full bg-white shadow-2xl rounded-xl mt-2 border border-gray-200 max-h-60 overflow-y-auto">
                                                @foreach ($line['product_results'] as $res)
                                                    <button type="button"
                                                        wire:key="prod-{{ $res['id'] }}-{{ $index }}"
                                                        wire:click="selectProduct({{ $index }}, {{ $res['id'] }})"
                                                        class="w-full p-3 hover:bg-indigo-50 text-left border-b last:border-0 transition">
                                                        <p class="text-sm font-bold text-gray-800">{{ $res['name'] }}
                                                        </p>
                                                        <p class="text-[10px] text-gray-400">Ref: S/
                                                            {{ number_format($res['price_purchase'] ?? 0, 2) }}</p>
                                                    </button>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    {{-- <td class="py-3 px-2 text-center text-xs text-gray-500">{{ $line['uom_name'] }}</td> --}}
                                    <td class="py-3 px-2">
                                        <select wire:model.live="lines.{{ $index }}.uom_id"
                                            class="w-full bg-transparent border-0 focus:ring-0 text-xs text-gray-500 p-0 text-center cursor-pointer hover:text-indigo-600 transition-colors">
                                            @foreach ($uoms as $uom)
                                                <option value="{{ $uom->id }}">{{ $uom->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>




                                    {{-- Input de Cantidad --}}
                                    <td>
                                        <input type="number" wire:model.live="lines.{{ $index }}.product_qty"
                                            min="0" step="any" {{-- 🚀 Bloquea la tecla 'Menos' (-) y 'e' (exponencial) --}}
                                            onkeydown="if(['-', 'e', 'E'].includes(event.key)) event.preventDefault();"
                                            {{-- 🛡️ Si pegan un valor negativo, lo convierte en positivo o 0 inmediatamente --}} oninput="this.value = Math.abs(this.value)"
                                            class="w-full bg-transparent border-0 focus:ring-0 text-center font-bold text-indigo-600 p-0">
                                    </td>

                                    {{-- Input de Precio (Misma lógica) --}}
                                    <td>
                                        <input type="number" wire:model.live="lines.{{ $index }}.price_unit"
                                            min="0" step="0.01"
                                            onkeydown="if(['-', 'e', 'E'].includes(event.key)) event.preventDefault();"
                                            oninput="this.value = Math.abs(this.value)"
                                            class="w-full bg-transparent border-0 focus:ring-0 text-right font-medium p-0">
                                    </td>

                                    <td class="py-3 px-4 text-right font-bold text-gray-700">
                                        {{ $currency_symbol ?? 'S/' }} {{ number_format($line['price_subtotal'], 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <button type="button" wire:click="removeLine({{ $index }})"
                                            class="text-gray-300 hover:text-red-500 transition-colors">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button type="button" wire:click="addLine"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-indigo-600 font-bold text-xs hover:bg-indigo-50 transition-all">
                    <i class="fa-solid fa-plus-circle text-lg"></i> Añadir producto o servicio
                </button>
            </div>

            {{-- Totales --}}
            <div class="flex justify-end pt-6 border-t border-gray-100">
                <div class="w-full md:w-80 space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 font-medium">Base Imponible</span>
                        <span class="text-gray-800 font-bold">{{ $currency_symbol ?? 'S/' }}
                            {{ number_format($amount_untaxed, $precision, '.', ',') }}</span>
                    </div>
                    @foreach ($tax_group as $taxName => $taxAmount)
                        @if ($taxAmount > 0)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">{{ $taxName }}</span>
                                <span class="text-indigo-600 font-bold">{{ $currency_symbol ?? 'S/' }}
                                    {{ number_format($taxAmount, $precision, '.', ',') }}</span>
                            </div>
                        @endif
                    @endforeach
                    <div class="pt-4 border-t-2 border-indigo-50">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-black text-gray-800 uppercase">Total</span>
                            <span
                                class="text-3xl font-black text-indigo-600 tracking-tighter">{{ $currency_symbol ?? 'S/' }}
                                {{ number_format($amount_total, $precision, '.', ',') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
