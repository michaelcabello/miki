<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => route('dashboard'),
            'Journal Types' => route('admin.journaltypes.index'),
            'Crear' => '#',
        ]" />
    </div>

    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Crear tipo de diario</h1>
                <p class="text-sm text-gray-500 dark:text-gray-300">
                    Crea un tipo como en Odoo (Ventas, Compras, Banco, Caja, Misceláneo, etc.).
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.journaltypes.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                           bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold transition
                           hover:bg-gray-200 dark:hover:bg-gray-600">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>

                {{-- <button wire:click="save"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                           bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition">
                    <i class="fa-regular fa-floppy-disk"></i> Guardar
                </button> --}}
            </div>
        </div>

        @if (session('success'))
            <div
                class="mt-4 px-4 py-3 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100
                        dark:bg-emerald-950/30 dark:text-emerald-200 dark:border-emerald-900/40">
                {{ session('success') }}
            </div>
        @endif
    </div>

    {{-- Tabs + Content --}}
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

        {{-- Content --}}
        <div class="p-6 space-y-6">

            @if ($tab === 'general')
                <div class="grid grid-cols-1 md:grid-cols-12 gap-5">

                    {{-- Code --}}
                    <div class="md:col-span-4">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Código <span class="text-red-500">*</span>
                        </label>
                        <input wire:model.live="code" type="text" placeholder="Ej: SALE, PURCH, BANK, CASH"
                            class="{{ $controlBase }} mt-2">
                        <p class="text-xs text-gray-500 dark:text-gray-300 mt-1">
                            Solo mayúsculas, números y “_”. Se normaliza automáticamente.
                        </p>
                        @error('code')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Name --}}
                    <div class="md:col-span-6">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input wire:model.defer="name" type="text" placeholder="Ej: Ventas, Compras, Banco..."
                            class="{{ $controlBase }} mt-2">
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Order --}}
                    <div class="md:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Orden <span class="text-red-500">*</span>
                        </label>
                        <input wire:model.defer="order" type="number" min="0" step="1"
                            class="{{ $controlBase }} mt-2" placeholder="0">
                        @error('order')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- State --}}
                    <div class="md:col-span-4">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Estado</label>
                        <div
                            class="mt-2 h-12 flex items-center justify-between gap-4 px-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                <input type="radio" wire:model.defer="state" value="1">
                                <span>Activo</span>
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                <input type="radio" wire:model.defer="state" value="0">
                                <span>Inactivo</span>
                            </label>
                        </div>
                        @error('state')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Helper cards --}}
                    <div class="md:col-span-8">
                        <div class="mt-6 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                Sugerencias tipo Odoo (códigos comunes)
                            </p>
                            <div class="flex flex-wrap gap-2 text-xs">
                                <span class="px-2 py-1 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">SALE = Ventas</span>
                                <span class="px-2 py-1 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">PURCH = Compras</span>
                                <span class="px-2 py-1 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">BANK = Banco</span>
                                <span class="px-2 py-1 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">CASH = Caja</span>
                                <span class="px-2 py-1 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">MISC = Misceláneo</span>
                            </div>
                        </div>
                    </div>

                </div>
                @can('JournalType Create')
                <div class="pt-2 flex justify-end">
                    <button wire:click="save"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl
                               bg-gradient-to-r from-indigo-600 to-sky-600 hover:from-indigo-700 hover:to-sky-700
                               text-white font-semibold shadow-sm transition">
                        <i class="fa-regular fa-floppy-disk"></i> Guardar tipo de diario
                    </button>
                </div>
                @endcan
            @endif

        </div>
    </div>
</div>
