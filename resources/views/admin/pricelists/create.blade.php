<x-layouts.app :title="__('Crear Lista de Precios')">
    <div class="flex flex-col gap-6">

        <!-- Breadcrumb -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 px-6 py-4">
            <x-breadcrumb :links="[
                'Dashboard' => route('dashboard'),
                'Listas de Precios' => route('admin.pricelists.index'),
                'Crear Lista' => '#',
            ]" />
        </div>

        <!-- Header -->
        <div class="relative overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
            <div class="absolute inset-0 opacity-60 pointer-events-none
                        bg-gradient-to-r from-indigo-50 via-white to-sky-50
                        dark:from-indigo-950/30 dark:via-gray-900 dark:to-sky-950/30">
            </div>

            <div class="relative p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center
                                bg-indigo-600 text-white shadow-md shadow-indigo-600/20">
                        <i class="fa-solid fa-tags text-lg"></i>
                    </div>

                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            Crear nueva lista de precios
                        </h1>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                            Define una lista como <span class="font-medium">Default</span>, <span class="font-medium">Mayorista</span> o <span class="font-medium">Promo</span>.
                        </p>
                    </div>
                </div>

                <a href="{{ route('admin.pricelists.index') }}"
                   class="inline-flex items-center justify-center rounded-xl px-4 py-2
                          border border-gray-200 dark:border-gray-600
                          bg-white/80 dark:bg-gray-800/60
                          text-gray-700 dark:text-gray-200
                          hover:bg-white dark:hover:bg-gray-800 transition">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Volver
                </a>
            </div>
        </div>

        <!-- Formulario -->
        <form method="POST" action="{{ route('admin.pricelists.store') }}" class="space-y-5">
            @csrf

            <!-- Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Datos de la lista de precios
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Completa los campos. Los que tienen <span class="text-red-500 font-semibold">*</span> son obligatorios.
                            </p>
                        </div>

                        <span class="hidden sm:inline-flex items-center gap-2 text-xs px-3 py-1.5 rounded-full
                                     bg-indigo-50 text-indigo-700 border border-indigo-100
                                     dark:bg-indigo-950/40 dark:text-indigo-200 dark:border-indigo-900/40">
                            <i class="fa-regular fa-lightbulb"></i>
                            Tip: usa nombres claros
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <!-- Nombre -->
                        <div class="space-y-2">
                            <x-label for="name">
                                Nombre <span class="text-red-500">*</span>
                            </x-label>

                            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40
                                        focus-within:ring-2 focus-within:ring-indigo-500/40 focus-within:border-indigo-400 transition">
                                <div class="p-1">
                                    <input id="name" name="name" value="{{ old('name') }}"
                                           placeholder="Ej: Default, Mayorista, Promo POS"
                                           class="w-full px-4 py-3 rounded-2xl bg-transparent border-0 outline-none
                                                  text-gray-900 dark:text-gray-100 placeholder:text-gray-400
                                                  focus:ring-0" />
                                </div>
                            </div>
                            @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Moneda -->
                        <div class="space-y-2">
                            <x-label for="currency_id">
                                Moneda <span class="text-red-500">*</span>
                            </x-label>

                            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40
                                        focus-within:ring-2 focus-within:ring-indigo-500/40 focus-within:border-indigo-400 transition">
                                <div class="p-1">
                                    <select id="currency_id" name="currency_id"
                                            class="w-full px-4 py-3 rounded-2xl bg-transparent border-0 outline-none
                                                   text-gray-900 dark:text-gray-100 focus:ring-0">
                                        <option value="">-- Seleccionar --</option>
                                        @foreach($currencies as $c)
                                            <option value="{{ $c->id }}" @selected(old('currency_id') == $c->id)>
                                                {{ $c->abbreviation ?? $c->name ?? ('Moneda #' . $c->id) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @error('currency_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror

                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                La moneda define el precio final mostrado en ventas/POS.
                            </p>
                        </div>

                        <!-- Notas -->
                        <div class="space-y-2 md:col-span-2">
                            <x-label for="notes">Notas (opcional)</x-label>

                            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40
                                        focus-within:ring-2 focus-within:ring-indigo-500/40 focus-within:border-indigo-400 transition">
                                <div class="p-1">
                                    <textarea id="notes" name="notes" rows="3"
                                              placeholder="Ej: Lista para clientes mayoristas a partir de 12 unidades..."
                                              class="w-full px-4 py-3 rounded-2xl bg-transparent border-0 outline-none
                                                     text-gray-900 dark:text-gray-100 placeholder:text-gray-400
                                                     focus:ring-0 resize-none">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                            @error('notes') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Active -->
                        <div class="md:col-span-2">
                            <div class="flex items-center justify-between p-4 rounded-2xl
                                        border border-gray-200 dark:border-gray-700
                                        bg-gradient-to-r from-gray-50 via-white to-gray-50
                                        dark:from-gray-900/50 dark:via-gray-800 dark:to-gray-900/50">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center
                                                bg-emerald-600 text-white shadow-sm shadow-emerald-600/20">
                                        <i class="fa-solid fa-toggle-on"></i>
                                    </div>

                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">Activa</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">
                                            Si está inactiva, no se podrá usar para calcular precios.
                                        </p>
                                    </div>
                                </div>

                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="state" value="1" class="sr-only peer"
                                           {{ old('state', 1) ? 'checked' : '' }}>
                                    <div class="relative w-12 h-7 bg-gray-300 dark:bg-gray-600 rounded-full
                                                peer peer-checked:bg-emerald-500 transition">
                                        <div class="absolute top-1 left-1 w-5 h-5 bg-white rounded-full
                                                    peer-checked:translate-x-5 transition"></div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Default -->
                        <div class="md:col-span-2">
                            <div class="flex items-center justify-between p-4 rounded-2xl
                                        border border-gray-200 dark:border-gray-700
                                        bg-gradient-to-r from-gray-50 via-white to-gray-50
                                        dark:from-gray-900/50 dark:via-gray-800 dark:to-gray-900/50">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center
                                                bg-indigo-600 text-white shadow-sm shadow-indigo-600/20">
                                        <i class="fa-solid fa-star"></i>
                                    </div>

                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">Lista por defecto</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">
                                            Si la marcas, esta lista será la “Default” del sistema.
                                        </p>
                                    </div>
                                </div>

                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_default" value="1" class="sr-only peer"
                                           {{ old('is_default') ? 'checked' : '' }}>
                                    <div class="relative w-12 h-7 bg-gray-300 dark:bg-gray-600 rounded-full
                                                peer peer-checked:bg-indigo-600 transition">
                                        <div class="absolute top-1 left-1 w-5 h-5 bg-white rounded-full
                                                    peer-checked:translate-x-5 transition"></div>
                                    </div>
                                </label>
                            </div>

                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                Recomendación: mantén solo una lista marcada como default.
                            </p>
                        </div>

                    </div>
                </div>

                <!-- Footer actions -->
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-900/30 rounded-b-2xl">
                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Luego podrás crear reglas en <b>Pricelist Items</b> (por producto, variante, cantidad y fechas).
                        </p>

                        <div class="flex gap-2">
                            <a href="{{ route('admin.pricelists.index') }}"
                               class="inline-flex items-center justify-center rounded-xl px-4 py-2
                                      border border-gray-200 dark:border-gray-600
                                      bg-white dark:bg-gray-800
                                      text-gray-700 dark:text-gray-200
                                      hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                Cancelar
                            </a>

                            <button type="submit"
                                    class="inline-flex items-center justify-center rounded-xl px-5 py-2.5
                                           bg-gradient-to-r from-indigo-600 to-sky-600
                                           hover:from-indigo-700 hover:to-sky-700
                                           text-white font-semibold
                                           shadow-md shadow-indigo-600/20
                                           focus:outline-none focus:ring-4 focus:ring-indigo-500/30 transition">
                                <i class="fa-regular fa-floppy-disk mr-2"></i>
                                Crear lista
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
</x-layouts.app>
