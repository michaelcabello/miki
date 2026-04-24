<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => route('dashboard'),
            'Almacenes' => route('admin.warehouses.index'),
            'Editar'    => '#',
        ]" />
    </div>

    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Editar Almacén</h1>
            <p class="text-sm text-gray-500 dark:text-gray-300">
                Editando:
                <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $record->name }}</span>
                <span class="mx-2 text-gray-300">|</span>
                Código: <span class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded text-xs">{{ $record->code }}</span>
                @if ($record->is_main)
                    <span class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">
                        <i class="fa-solid fa-star text-[10px]"></i> Almacén Principal
                    </span>
                @endif
            </p>
        </div>
        <a href="{{ route('admin.warehouses.index') }}"
            class="px-5 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold hover:bg-gray-200 transition">
            <i class="fa-solid fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    {{-- Formulario --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        {{-- Tabs --}}
        <div class="px-6 pt-5 border-b border-gray-200 dark:border-gray-700">
            <button wire:click="setTab('general')"
                class="px-4 py-2 rounded-t-xl text-sm font-semibold {{ $tab === 'general' ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:text-gray-700' }}">
                <i class="fa-regular fa-file-lines mr-2"></i> Información General
            </button>
        </div>

        <div class="p-6">
            @include('livewire.admin.warehouse.partials.form-fields')

            {{-- Información de auditoría (Odoo style) --}}
            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                <div class="flex gap-6 text-xs text-gray-400">
                    <span><i class="fa-regular fa-clock mr-1"></i> Creado: {{ $record->created_at->format('d/m/Y H:i') }}</span>
                    <span><i class="fa-regular fa-pen-to-square mr-1"></i> Actualizado: {{ $record->updated_at->format('d/m/Y H:i') }}</span>
                    @if ($record->lot_stock_id)
                        <span><i class="fa-solid fa-warehouse mr-1"></i> Ubicación principal asignada</span>
                    @else
                        <span class="text-amber-400"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Sin ubicación principal — se asigna desde el módulo de ubicaciones</span>
                    @endif
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                <button wire:click="save" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-10 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold shadow-lg transition-all active:scale-95">
                    <i class="fa-regular fa-floppy-disk" wire:loading.remove wire:target="save"></i>
                    <i class="fa-solid fa-circle-notch animate-spin" wire:loading wire:target="save"></i>
                    <span wire:loading.remove wire:target="save">Actualizar Cambios</span>
                    <span wire:loading wire:target="save">Procesando...</span>
                </button>
            </div>
        </div>
    </div>

</div>

