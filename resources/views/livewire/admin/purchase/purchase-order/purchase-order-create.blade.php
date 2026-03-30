<div class="space-y-6 p-4">
    {{-- Breadcrumb al estilo de tu proyecto --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <span>Compras</span>
            <span>/</span>
            <span class="font-bold text-gray-800">Nueva Solicitud de Cotización</span>
        </div>
    </div>

    {{-- Formulario Principal --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        {{-- Cabecera Odoo Style --}}
        {{-- Parte del Formulario Superior --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="space-y-4">
                {{-- Búsqueda de Partner --}}
                <div class="relative">
                    <label class="text-xs font-bold text-gray-400 uppercase">Proveedor</label>
                    @if ($partner_id)
                        <div
                            class="flex items-center justify-between bg-gray-50 p-2 rounded-lg border border-indigo-200">
                            <span class="font-bold text-indigo-700">{{ $partner_name }}</span>
                            <button wire:click="$set('partner_id', null)" class="text-red-500 text-xs">Cambiar</button>
                        </div>
                    @else
                        <input type="text" wire:model.live.debounce.300ms="search_partner"
                            placeholder="Buscar por nombre o RUC..."
                            class="w-full rounded-xl border-gray-200 focus:ring-indigo-500 shadow-sm">

                        @if (count($partner_results) > 0)
                            <div
                                class="absolute z-50 w-full bg-white shadow-2xl rounded-xl mt-1 border border-gray-100 overflow-hidden">
                                @foreach ($partner_results as $res)
                                    <div wire:click="selectPartner({{ $res['id'] }}, '{{ $res['name'] }}')"
                                        class="p-3 hover:bg-indigo-50 cursor-pointer flex justify-between items-center border-b last:border-0">
                                        <span class="text-sm font-semibold text-gray-700">{{ $res['name'] }}</span>
                                        <span class="text-xs text-gray-400">{{ $res['document_number'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase">Entregar a (Almacén)</label>
                    <select wire:model="warehouse_id"
                        class="w-full rounded-xl border-gray-200 focus:ring-indigo-500 shadow-sm">
                        {{-- Opción por defecto (Placeholder) --}}
                        <option value="">Seleccione almacén de recepción...</option>
                        @foreach ($warehouses as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase">Fecha Límite (Orden)</label>
                    <input type="date" wire:model="date_approve" class="w-full rounded-xl border-gray-200 shadow-sm">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase">Referencia de Proveedor</label>
                    <input type="text" wire:model="notes" placeholder="Ej: Cotización #123"
                        class="w-full rounded-xl border-gray-200 shadow-sm">
                </div>
            </div>
        </div>


        {{-- Grilla de Productos --}}
        <div class="overflow-visible">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50">
                    <tr class="text-xs font-bold text-gray-400 uppercase border-b">
                        <th class="py-3 px-2">Producto</th>
                        <th class="py-3 px-2">UoM</th>
                        <th class="py-3 px-2">Cantidad</th>
                        <th class="py-3 px-2">Precio Unit.</th>
                        <th class="py-3 px-2 text-right">Subtotal</th>
                        <th class="py-3 px-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($lines as $index => $line)
                        <tr class="group">


                            <td class="py-4 px-2 relative w-1/3">
                                <input type="text"
                                    wire:model.live.debounce.300ms="lines.{{ $index }}.product_search"
                                    placeholder="Buscar producto..."
                                    class="w-full border-0 focus:ring-0 bg-transparent text-sm font-semibold p-0">

                                @if (count($line['product_results']) > 0)
                                    <div
                                        class="absolute z-40 left-0 w-full bg-white shadow-2xl rounded-lg mt-1 border border-gray-100 max-h-60 overflow-y-auto">
                                        @foreach ($line['product_results'] as $res)
                                            <div wire:click="selectProduct({{ $index }}, {{ $res['id'] }})"
                                                class="p-3 hover:bg-indigo-50 cursor-pointer text-xs border-b last:border-0 transition">
                                                <p class="font-bold text-gray-800">{{ $res['display_name'] }}</p>
                                                <p class="text-gray-400">Precio Ref: S/
                                                    {{ number_format($res['price_purchase'], 2) }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            <td class="py-4 px-2 text-sm text-gray-500">
                                {{ $line['uom_name'] }} {{-- Campo solicitado --}}
                            </td>

                            <td class="py-4 px-2">
                                <input type="number" wire:model.live="lines.{{ $index }}.product_qty"
                                    class="w-20 border-0 focus:ring-0 bg-transparent text-sm font-bold @error('lines.' . $index . '.product_qty') text-red-500 @enderror"
                                    placeholder="0.00">
                            </td>

                            <td class="py-4 px-2">
                                <input type="number" wire:model.live="lines.{{ $index }}.price_unit"
                                    class="w-28 border-0 focus:ring-0 bg-transparent text-sm @if ($line['price_unit'] == '') bg-yellow-50 @endif"
                                    placeholder="Llenar precio...">
                            </td>

                            <td class="py-4 px-2 text-right font-bold text-gray-700">
                                S/ {{ number_format($line['price_subtotal'], 2) }}
                            </td>

                            {{-- Botón Eliminar Corregido --}}
                            <td class="py-4 px-2 text-center">
                                <button wire:click="removeLine({{ $index }})"
                                    class="text-gray-300 hover:text-red-500 transition-colors">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button wire:click="addLine" class="mt-4 text-indigo-600 font-bold text-sm hover:underline">
                + Añadir línea
            </button>
        </div>





        {{-- Sección de Totales --}}
        <div class="mt-10 flex justify-end">
            <div class="w-full md:w-64 space-y-2 border-t pt-4">

                @php
                    // Obtenemos la precisión de la empresa actual
                    $precision = auth()->user()->company->decimal_purchase ?? 2;
                @endphp

                <div class="flex justify-between text-gray-600">
                    <span>Base Imponible:</span>
                    <span>S/ {{ number_format($amount_untaxed, $precision) }}</span>
                </div>



                {{-- En la grilla de productos --}}

                <div class="flex justify-between text-gray-600">
                    <span>Impuestos (IGV):</span>
                    <span class="text-indigo-600 font-bold">S/ {{ number_format($amount_tax, $precision) }}</span>
                </div>

                <div class="flex justify-between text-xl font-bold text-indigo-700 border-t pt-2">
                    <span>Total a Pagar:</span>
                    <span>S/ {{ number_format($amount_total, $precision) }}</span>
                </div>



            </div>
        </div>
    </div>
</div>
