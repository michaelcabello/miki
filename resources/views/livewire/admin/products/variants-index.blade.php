<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => route('dashboard'),
            'Productos' => route('admin.products.index'),
            'Variantes: '.$product_template->name => '#',
        ]" />
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                Variantes de: <span class="text-indigo-600">{{ $product_template->name }}</span>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-300">
                La variante <b>Default</b> es la base cuando no hay combinación.
            </p>
        </div>

        <a href="{{ route('admin.products.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700
                  bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-wrap gap-4 justify-between items-center">
        <div class="relative w-full md:w-1/3">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por SKU o nombre..."
                class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700
                       text-gray-800 dark:text-gray-200 focus:ring focus:ring-indigo-500" />
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fa-solid fa-search"></i>
            </span>
        </div>

        <div>
            <select wire:model.live="perPage"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">SKU</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Nombre</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Precio venta</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Default</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($variants as $v)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100">{{ $v->sku }}</td>
                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100">{{ $v->variant_name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100 text-right">
                            {{ number_format((float)($v->price_sale ?? 0), 2) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($v->is_default)
                                <span class="px-2 py-1 rounded-full text-xs bg-indigo-100 text-indigo-700">Sí</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-600">No</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                            No hay variantes.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $variants->links() }}</div>
</div>

