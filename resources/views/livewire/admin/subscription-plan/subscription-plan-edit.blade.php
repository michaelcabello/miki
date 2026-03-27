<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard'             => route('dashboard'),
            'Planes de Suscripción' => route('admin.subscription-plans.index'),
            'Editar'                => '#',
        ]" />
    </div>

    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Editar plan de suscripción</h1>
                <p class="text-sm text-gray-500 dark:text-gray-300">
                    Editando: <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $plan->name }}</span>
                    &nbsp;·&nbsp;
                    {{ $plan->interval_label }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.subscription-plans.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                           bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold transition
                           hover:bg-gray-200 dark:hover:bg-gray-600">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>
                @can('SubscriptionPlan Update')
                    <button wire:click="update"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                               bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition">
                        <i class="fa-regular fa-floppy-disk"></i> Actualizar
                    </button>
                @endcan
            </div>
        </div>
    </div>

    {{-- Tabs + Content --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="px-6 pt-5">
            <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700">
                <button type="button" wire:click="setTab('general')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'general' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
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
                <div class="grid grid-cols-1 md:grid-cols-12 gap-5">

                    <div class="md:col-span-6">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input wire:model.defer="name" type="text"
                            class="{{ $controlBase }} mt-2"
                            placeholder="Ej: Mensual, Anual Pro...">
                        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Orden</label>
                        <input wire:model.defer="order" type="number" min="0" step="1"
                            class="{{ $controlBase }} mt-2">
                        @error('order') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-4">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Estado</label>
                        <div class="mt-2 h-12 flex items-center gap-6 px-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                <input type="radio" wire:model.defer="active" value="1"> Activo
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                <input type="radio" wire:model.defer="active" value="0"> Inactivo
                            </label>
                        </div>
                    </div>

                    <div class="md:col-span-12">
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-5">
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">
                                <i class="fa-regular fa-clock mr-2 text-indigo-500"></i>
                                Frecuencia de facturación
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                        Cada cuánto <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model.defer="interval_count" type="number" min="1"
                                        class="{{ $controlBase }} mt-2">
                                    @error('interval_count') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                        Unidad de tiempo <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.defer="interval_unit" class="{{ $controlBase }} mt-2">
                                        <option value="day">Día</option>
                                        <option value="week">Semana</option>
                                        <option value="month">Mes</option>
                                        <option value="year">Año</option>
                                    </select>
                                    @error('interval_unit') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                @can('SubscriptionPlan Update')
                    <div class="pt-2 flex justify-end">
                        <button wire:click="update"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl
                                   bg-gradient-to-r from-indigo-600 to-sky-600 hover:from-indigo-700 hover:to-sky-700
                                   text-white font-semibold shadow-sm transition">
                            <i class="fa-regular fa-floppy-disk"></i> Actualizar plan
                        </button>
                    </div>
                @endcan
            @endif
        </div>
    </div>
</div>
