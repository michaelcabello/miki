<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => route('dashboard'),
            'Impuestos'  => route('admin.taxes.index'),
            'Crear'      => '#',
        ]" />
    </div>

    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Crear impuesto</h1>
                <p class="text-sm text-gray-500 dark:text-gray-300">Define el impuesto, su cálculo y ámbito de aplicación.</p>
            </div>
            <a href="{{ route('admin.taxes.index') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                      bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold transition
                      hover:bg-gray-200 dark:hover:bg-gray-600">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    {{-- Tabs + Contenido --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">

        {{-- Tabs --}}
        <div class="px-6 pt-5">
            <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700">
                <button type="button" wire:click="setTab('general')"
                        class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                               {{ $tab === 'general'
                                  ? 'bg-indigo-600 text-white'
                                  : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-regular fa-file-lines mr-2"></i> Datos
                </button>
            </div>
        </div>

        @php
            $controlBase = "w-full h-12 px-4 rounded-xl border border-gray-300 dark:border-gray-600
                            bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                            focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition";
        @endphp

        <div class="p-6 space-y-6">
            @if ($tab === 'general')

            {{-- ── Fila 1: Nombre + Secuencia ── --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">

                {{-- Nombre --}}
                <div class="md:col-span-9">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input wire:model.defer="name" type="text"
                           placeholder="Ej: IGV 18% (Venta), Exonerado, ISC 10%..."
                           class="{{ $controlBase }} mt-2">
                    @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Secuencia --}}
                <div class="md:col-span-3">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                        Secuencia <span class="text-red-500">*</span>
                    </label>
                    <input wire:model.defer="sequence" type="number" min="0" step="1"
                           class="{{ $controlBase }} mt-2" placeholder="1">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Orden de aplicación.</p>
                    @error('sequence') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

            </div>

            {{-- ── Fila 2: Tipo cálculo + Monto + Ámbito uso ── --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">

                {{-- Tipo de cálculo --}}
                <div class="md:col-span-4">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                        Tipo de cálculo <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.live="amount_type" class="{{ $controlBase }} mt-2">
                        <option value="percent">Porcentaje (%)</option>
                        <option value="fixed">Fijo (monto fijo)</option>
                        <option value="division">División</option>
                        <option value="group">Grupo</option>
                    </select>
                    @error('amount_type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Monto / Porcentaje --}}
                <div class="md:col-span-3">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                        @if ($amount_type === 'percent') Porcentaje (%)
                        @elseif ($amount_type === 'fixed') Monto fijo
                        @else Valor
                        @endif
                        <span class="text-red-500">*</span>
                    </label>
                    <input wire:model.defer="amount" type="number" min="0" step="0.01"
                           class="{{ $controlBase }} mt-2"
                           placeholder="{{ $amount_type === 'percent' ? 'Ej: 18' : 'Ej: 5.00' }}">
                    @error('amount') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Ámbito de uso --}}
                <div class="md:col-span-3">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                        Ámbito de uso <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.defer="type_tax_use" class="{{ $controlBase }} mt-2">
                        <option value="sale">Ventas</option>
                        <option value="purchase">Compras</option>
                        <option value="none">Ninguno</option>
                    </select>
                    @error('type_tax_use') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Ámbito fiscal (tax_scope) --}}
                <div class="md:col-span-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                        Ámbito fiscal
                    </label>
                    <input wire:model.defer="tax_scope" type="text"
                           placeholder="Ej: peru"
                           class="{{ $controlBase }} mt-2">
                    @error('tax_scope') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

            </div>

            {{-- ── Fila 3: Cuenta contable ── --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">

                <div class="md:col-span-6">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                        Cuenta contable
                    </label>
                    <select wire:model.defer="account_id" class="{{ $controlBase }} mt-2">
                        <option value="">— Sin cuenta asignada —</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Cuenta donde se registrará el impuesto.
                    </p>
                    @error('account_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Descripción --}}
                <div class="md:col-span-6">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Descripción</label>
                    <input wire:model.defer="description" type="text"
                           placeholder="Descripción breve del impuesto..."
                           class="{{ $controlBase }} mt-2">
                    @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

            </div>

            {{-- ── Fila 4: Flags booleanos + Estado ── --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">

                {{-- Precio incluye impuesto --}}
                <div class="md:col-span-3">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Precio incluye impuesto</label>
                    <div class="mt-2 h-12 flex items-center justify-between gap-4 px-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <input type="radio" wire:model.defer="price_include" value="1"> <span>Sí</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <input type="radio" wire:model.defer="price_include" value="0"> <span>No</span>
                        </label>
                    </div>
                    @error('price_include') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Afecta base --}}
                <div class="md:col-span-3">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Incluye en base</label>
                    <div class="mt-2 h-12 flex items-center justify-between gap-4 px-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <input type="radio" wire:model.defer="include_base_amount" value="1"> <span>Sí</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <input type="radio" wire:model.defer="include_base_amount" value="0"> <span>No</span>
                        </label>
                    </div>
                    @error('include_base_amount') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Base afectada --}}
                <div class="md:col-span-3">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Base afectada</label>
                    <div class="mt-2 h-12 flex items-center justify-between gap-4 px-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <input type="radio" wire:model.defer="is_base_affected" value="1"> <span>Sí</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <input type="radio" wire:model.defer="is_base_affected" value="0"> <span>No</span>
                        </label>
                    </div>
                    @error('is_base_affected') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Estado activo --}}
                <div class="md:col-span-3">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Estado</label>
                    <div class="mt-2 h-12 flex items-center justify-between gap-4 px-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <input type="radio" wire:model.defer="active" value="1"> <span>Activo</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <input type="radio" wire:model.defer="active" value="0"> <span>Inactivo</span>
                        </label>
                    </div>
                    @error('active') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

            </div>

            {{-- Botón Guardar --}}
            @can('Tax Create')
            <div class="pt-2 flex justify-end">
                <button wire:click="save"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl
                               bg-gradient-to-r from-indigo-600 to-sky-600 hover:from-indigo-700 hover:to-sky-700
                               text-white font-semibold shadow-sm transition">
                    <i class="fa-regular fa-floppy-disk"></i> Guardar
                </button>
            </div>
            @endcan

            @endif
        </div>
    </div>

</div>
