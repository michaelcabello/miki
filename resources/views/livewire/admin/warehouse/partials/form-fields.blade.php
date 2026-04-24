@php
    $controlBase = "w-full h-12 px-4 rounded-xl border border-gray-300 dark:border-gray-600
                    bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                    focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition";
    $textareaBase = "w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                     bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                     focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition resize-none";
@endphp

<div class="grid grid-cols-1 md:grid-cols-12 gap-5">

    {{-- Código --}}
    <div class="md:col-span-3">
        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
            Código <span class="text-red-500">*</span>
        </label>
        <input wire:model="form.code" type="text" placeholder="Ej: WH01, WH_LIMA" class="{{ $controlBase }} mt-2"
            maxlength="10"
            x-on:input="$event.target.value = $event.target.value.toUpperCase(); $wire.set('form.code', $event.target.value)">
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
            Máx. 10 caracteres. Solo mayúsculas, números y "_".
        </p>
        @error('form.code')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Nombre --}}
    <div class="md:col-span-6">
        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
            Nombre <span class="text-red-500">*</span>
        </label>
        <input wire:model="form.name" type="text" placeholder="Ej: Almacén Central, Depósito Norte"
            class="{{ $controlBase }} mt-2">
        @error('form.name')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Orden --}}
    <div class="md:col-span-3">
        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
            Orden <span class="text-red-500">*</span>
        </label>
        <input wire:model="form.order" type="number" min="0" max="65535" class="{{ $controlBase }} mt-2"
            placeholder="0">
        @error('form.order')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Dirección --}}
    <div class="md:col-span-8">
        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
            Dirección
        </label>
        <input wire:model="form.address" type="text" placeholder="Ej: Av. Industrial 123, Ate, Lima"
            class="{{ $controlBase }} mt-2">
        @error('form.address')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Estado --}}
    <div class="md:col-span-4">
        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Estado</label>
        <div
            class="mt-2 h-12 flex items-center justify-around rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="radio" wire:model="form.state" value="1" class="text-indigo-600">
                <span class="text-sm text-gray-700 dark:text-gray-200">Activo</span>
            </label>
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="radio" wire:model="form.state" value="0" class="text-indigo-600">
                <span class="text-sm text-gray-700 dark:text-gray-200">Inactivo</span>
            </label>
        </div>
        @error('form.state')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Descripción --}}
    <div class="md:col-span-8">
        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
            Descripción
        </label>
        <textarea wire:model="form.description" rows="3"
            placeholder="Descripción opcional del almacén, zona de cobertura, notas..." class="{{ $textareaBase }} mt-2"></textarea>
        @error('form.description')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- ¿Es almacén principal? (Odoo style) --}}
    <div class="md:col-span-4">
        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
            Almacén Principal
        </label>
        <div class="mt-2 p-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" wire:model="form.is_main" value="1"
                    class="mt-0.5 rounded border-gray-300 text-amber-500 focus:ring-amber-400">
                <div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                        Centro de distribución
                    </span>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Solo puede haber uno. Al marcarlo, se desmarca el anterior automáticamente.
                    </p>
                </div>
            </label>
        </div>
        @error('form.is_main')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Aviso informativo sobre lot_stock_id (Odoo style) --}}
    <div class="md:col-span-12">
        <div
            class="flex items-start gap-3 p-4 rounded-xl border border-blue-100 dark:border-blue-800/50 bg-blue-50 dark:bg-blue-900/20">
            <i class="fa-solid fa-circle-info text-blue-500 mt-0.5 text-sm"></i>
            <div class="text-xs text-blue-700 dark:text-blue-300">
                <strong>Ubicación de Stock Principal</strong> — Se asigna automáticamente al crear las ubicaciones
                del almacén desde el módulo <em>Almacén → Ubicaciones</em>. Este campo no se edita desde aquí,
                igual que el flujo de Odoo.
            </div>
        </div>
    </div>

</div>
