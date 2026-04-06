<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Solicitudes de Cotización</h1>
            <p class="text-sm text-gray-500">Listado oficial de compras - TICOM</p>
        </div>
        <a href="{{ route('purchase.order.create') }}" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-indigo-700 transition">
            <i class="fa-solid fa-plus mr-2"></i> Nueva RFQ
        </a>
    </div>

    {{-- Filtros --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 border border-gray-100 dark:border-gray-700">
        <div class="relative w-full md:w-96">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por referencia o proveedor..."
                class="w-full h-10 pl-11 rounded-xl border-gray-200 dark:bg-gray-700 dark:border-gray-600 text-sm focus:ring-indigo-500">
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-900/50 text-[11px] font-black text-gray-500 uppercase tracking-wider border-b">
                    <th class="p-4 px-6">Referencia</th>
                    <th class="p-4">Proveedor</th>
                    <th class="p-4 text-center">Fecha</th>
                    <th class="p-4 text-right">Total</th>
                    <th class="p-4 text-center">Estado</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($orders as $order)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="p-4 px-6 font-bold text-indigo-600">{{ $order->name }}</td>
                    <td class="p-4">
                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $order->partner->name }}</div>
                        <div class="text-[10px] text-gray-400">{{ $order->partner->document_number }}</div>
                    </td>
                    <td class="p-4 text-center text-xs text-gray-500">{{ $order->date_order->format('d/m/Y') }}</td>
                    <td class="p-4 text-right font-black">
                        {{ $order->currency->abbreviation }} {{ number_format($order->amount_total, 2) }}
                    </td>
                    <td class="p-4 text-center">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest
                            {{ $order->state == 'draft' ? 'bg-gray-100 text-gray-500' : 'bg-emerald-100 text-emerald-600' }}">
                            {{ $order->state }}
                        </span>
                    </td>
                    <td class="p-4 text-center">
                        <div class="flex justify-center gap-2">
                            {{-- PDF --}}
                            <button wire:click="viewPdf({{ $order->id }})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="Ver PDF">
                                <i class="fa-solid fa-file-pdf text-lg"></i>
                            </button>

                            {{-- WhatsApp --}}
                            <button wire:click="sendWhatsApp({{ $order->id }})" class="p-2 text-emerald-500 hover:bg-emerald-50 rounded-lg transition" title="Enviar por WhatsApp">
                                <i class="fa-brands fa-whatsapp text-xl"></i>
                            </button>

                            {{-- Email --}}
                            <button wire:click="sendEmail({{ $order->id }})" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition" title="Enviar por Email">
                                <i class="fa-solid fa-envelope text-lg"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $orders->links() }}</div>
    </div>
</div>
