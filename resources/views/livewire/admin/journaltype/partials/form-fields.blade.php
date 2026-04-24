@php
    $controlBase = "w-full h-12 px-4 rounded-xl border border-gray-300 dark:border-gray-600
                    bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                    focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition";
@endphp

<div class="grid grid-cols-1 md:grid-cols-12 gap-5">
    {{-- Código --}}
    <div class="md:col-span-4">
        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
            Código <span class="text-red-500">*</span>
        </label>
        <input wire:model.live="form.code" type="text" placeholder="Ej: SALE, PURCH..."
            class="{{ $controlBase }} mt-2">
        <p class="text-xs text-gray-500 dark:text-gray-300 mt-1">
            Solo mayúsculas, números y “_”.
        </p>
        @error('form.code') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Nombre --}}
    <div class="md:col-span-6">
        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
            Nombre <span class="text-red-500">*</span>
        </label>
        <input wire:model="form.name" type="text" placeholder="Ej: Ventas, Compras..."
            class="{{ $controlBase }} mt-2">
        @error('form.name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Orden --}}
    <div class="md:col-span-2">
        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
            Orden <span class="text-red-500">*</span>
        </label>
        <input wire:model="form.order" type="number" class="{{ $controlBase }} mt-2" placeholder="0">
        @error('form.order') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Estado --}}
    <div class="md:col-span-4">
        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Estado</label>
        <div class="mt-2 h-12 flex items-center justify-around rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="radio" wire:model="form.state" value="1" class="text-indigo-600">
                <span class="text-sm text-gray-700 dark:text-gray-200">Activo</span>
            </label>
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="radio" wire:model="form.state" value="0" class="text-indigo-600">
                <span class="text-sm text-gray-700 dark:text-gray-200">Inactivo</span>
            </label>
        </div>
    </div>

    {{-- Helper cards (Sugerencias) --}}
    <div class="md:col-span-8">
        <div class="mt-6 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Sugerencias estilo Odoo</p>
            <div class="flex flex-wrap gap-2 text-xs">
                @foreach(['SALE' => 'Ventas', 'PURCH' => 'Compras', 'BANK' => 'Banco', 'CASH' => 'Caja'] as $c => $n)
                    <span class="px-2 py-1 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                        {{ $c }} = {{ $n }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>
</div>
