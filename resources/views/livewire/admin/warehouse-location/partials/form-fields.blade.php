@php
    $controlBase = "w-full h-12 px-4 rounded-xl border border-gray-300 dark:border-gray-600
                    bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                    focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition";
@endphp

<div class="grid grid-cols-1 md:grid-cols-12 gap-5">

    {{-- Almacén --}}
    <div class="md:col-span-6">
        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
            Almacén
        </label>
        <select wire:model.live="form.warehouse_id" class="{{ $controlBase }} mt-2">
            <option value="">-- Sin almacén (ubicación virtual) --</option>
            @foreach($warehouses as $wh)
                <option value="{{ $wh->id }}">{{ $wh->code }} — {{ $wh->name }}</option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            Déjalo vacío para ubicaciones de tipo Vista o virtuales.
        </p>
        @error('form.warehouse_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Ubicación Padre --}}
    <div class="md:col-span-6">
        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
            Ubicación Padre
        </label>
        <select wire:model="form.parent_id" class="{{ $controlBase }} mt-2">
            <option value="">-- Raíz (sin padre) --</option>
            @foreach($parents as $parent)
                <option value="{{ $parent->id }}">
                    {{ $parent->complete_name ?? $parent->name }}
                </option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            El nombre completo se genera automáticamente: Padre / Nombre.
        </p>
        @error('form.parent_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Código --}}
    <div class="md:col-span-3">
        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
            Código <span class="text-red-500">*</span>
        </label>
        <input wire:model.live="form.code" type="text"
            placeholder="Ej: STOCK, A-1, IN"
            class="{{ $controlBase }} mt-2 uppercase"
            maxlength="50">
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            Único por almacén. Mayúsculas, números, -, _ y /.
        </p>
        @error('form.code') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Nombre --}}
    <div class="md:col-span-7">
        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
            Nombre <span class="text-red-500">*</span>
        </label>
        <input wire:model="form.name" type="text"
            placeholder="Ej: Stock, Entrada, Pasillo A"
            class="{{ $controlBase }} mt-2">
        @error('form.name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Orden --}}
    <div class="md:col-span-2">
        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
            Orden <span class="text-red-500">*</span>
        </label>
        <input wire:model="form.order" type="number" min="0" max="9999"
            class="{{ $controlBase }} mt-2" placeholder="0">
        @error('form.order') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Tipo de Ubicación (usage) --}}
    <div class="md:col-span-5">
        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
            Tipo de Ubicación <span class="text-red-500">*</span>
        </label>
        <select wire:model="form.usage" class="{{ $controlBase }} mt-2">
            @foreach(\App\Models\WarehouseLocation::$usageLabels as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            "Vista" solo agrupa — no almacena stock. "Interno" es el tipo estándar.
        </p>
        @error('form.usage') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Capacidad --}}
    <div class="md:col-span-4">
        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
            Capacidad
        </label>
        <input wire:model="form.capacity" type="number" step="0.01" min="0"
            placeholder="Opcional — ej: 1500.00"
            class="{{ $controlBase }} mt-2">
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            Límite de almacenamiento (unidades o m³).
        </p>
        @error('form.capacity') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Ubicación de Merma --}}
    <div class="md:col-span-3">
        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
            Ubicación de Merma
        </label>
        <div class="mt-2 h-12 flex items-center justify-around rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="radio" wire:model="form.scrap_location" value="1" class="text-red-500">
                <span class="text-sm text-gray-700 dark:text-gray-200">
                    <i class="fa-solid fa-trash-can text-red-400 text-xs mr-1"></i>Sí
                </span>
            </label>
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="radio" wire:model="form.scrap_location" value="0" class="text-gray-400">
                <span class="text-sm text-gray-700 dark:text-gray-200">No</span>
            </label>
        </div>
        @error('form.scrap_location') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Estado --}}
    <div class="md:col-span-3">
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
        @error('form.state') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Info: complete_name auto-generado --}}
    <div class="md:col-span-12">
        <div class="rounded-xl border border-blue-100 dark:border-blue-900/30 bg-blue-50 dark:bg-blue-900/10 p-4 flex gap-3">
            <i class="fa-solid fa-circle-info text-blue-500 mt-0.5 shrink-0"></i>
            <div class="text-sm text-blue-700 dark:text-blue-300">
                <strong>Nombre completo</strong> se genera automáticamente al guardar,
                siguiendo el árbol: <span class="font-mono">Almacén / Padre / Nombre</span>.
                Por ejemplo: <span class="font-mono">WH / Stock / Pasillo A</span>.
            </div>
        </div>
    </div>

</div>
