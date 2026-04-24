<div class="space-y-6">
    {{-- Encabezado --}}
    <div class="bg-white p-5 rounded-xl shadow flex justify-between items-center">
       {{--  <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $picking->name }}</h1>
            <p class="text-sm text-gray-500">Origen: {{ $picking->purchaseOrder->name ?? 'N/A' }}</p>
        </div> --}}


        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold text-gray-800">{{ $picking->name }}</h1>
                {{-- Badge de Destino Estilo Odoo --}}
                <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded text-xs font-bold">
                    📥 {{ $picking->locationTo->name }}
                </span>
            </div>
            <p class="text-sm text-gray-500">
                Origen: <span class="font-medium text-gray-700">{{ $picking->purchaseOrder->name ?? 'N/A' }}</span>
                | <span class="text-indigo-600 font-bold">Destino Final: {{ $picking->locationTo->name }}</span>
            </p>
        </div>



        <div class="flex items-center gap-4">
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $this->status_color }}">
                {{ $this->picking->state }}
            </span>




            @if ($picking->state !== 'done')
                <button wire:click="validatePicking"
                    class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700">
                    Validar
                </button>
            @else
                <div class="flex items-center text-green-600 font-bold">
                    <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Entrada Completada
                </div>
            @endif
        </div>
    </div>

    {{-- Información General --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white p-6 rounded-xl shadow">
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase">Proveedor</p>
            <p class="font-semibold text-gray-700">{{ $picking->partner->name }}</p>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase">Ubicación de Destino</p>
            <p class="font-semibold text-gray-700">{{ $picking->locationTo->name }}</p>
        </div>
    </div>

    {{-- Tabla de Operaciones --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-4 text-xs font-bold uppercase text-gray-500">Producto</th>
                    <th class="p-4 text-xs font-bold uppercase text-gray-500 text-center">Demanda</th>
                    <th class="p-4 text-xs font-bold uppercase text-gray-500 text-center w-40">Hecho</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($lines as $index => $line)
                    <tr>
                        <td class="p-4">
                            <span class="font-bold text-gray-700">{{ $line['product_name'] }}</span>
                        </td>
                        <td class="p-4 text-center text-gray-500">{{ $line['qty_demand'] }}</td>
                        <td class="p-4">
                            <input type="number" wire:model.live="lines.{{ $index }}.qty_done"
                                class="w-full text-center border-gray-300 rounded-lg font-bold text-indigo-600 focus:ring-indigo-500">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
