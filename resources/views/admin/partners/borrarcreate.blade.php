{{-- VISTA: Crear Partner --}}
<x-layouts.app :title="__('Crear Partner')">
    <div class="flex flex-col gap-6">

        <!-- Breadcrumb -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 px-6 py-4">
            <x-breadcrumb :links="[
                'Dashboard' => route('dashboard'),
                'Partners' => route('admin.partners.index'),
                'Crear Partner' => '#',
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
                        <i class="fa-solid fa-address-book text-lg"></i>
                    </div>

                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Crear nuevo partner</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                            Crea la empresa/persona principal. Luego podrás agregar contactos (hijos) como en Odoo.
                        </p>
                    </div>
                </div>

                <a href="{{ route('admin.partners.index') }}"
                   class="inline-flex items-center justify-center rounded-xl px-4 py-2
                    border border-gray-200 dark:border-gray-600 bg-white/80 dark:bg-gray-800/60
                    text-gray-700 dark:text-gray-200 hover:bg-white dark:hover:bg-gray-800 transition">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Volver
                </a>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.partners.store') }}" class="space-y-5">
            @csrf

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">

                <div class="p-6">
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Datos del partner</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Guarda primero el partner raíz. Los contactos se agregan en la edición (Odoo-like).
                            </p>
                        </div>
                    </div>

                    <!-- Tabs simples (solo visual en create) -->
                    <div class="flex flex-wrap gap-2 mb-6">
                        <span class="px-3 py-1.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold">
                            Datos
                        </span>
                        <span class="px-3 py-1.5 rounded-xl bg-gray-100 text-gray-600 text-sm font-semibold dark:bg-gray-700 dark:text-gray-200">
                            Contactos (guardar primero)
                        </span>
                    </div>

                    @php
                        // ✅ Base uniforme
                        $controlBase = "w-full h-12 px-4 rounded-xl border border-gray-300 dark:border-gray-600
                            bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                            focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition";

                        $fieldWrap = "rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                            focus-within:ring-4 focus-within:ring-indigo-500/20 focus-within:border-indigo-400 transition";

                        $fieldInput = "w-full h-12 bg-transparent border-0 outline-none ring-0 focus:ring-0 focus:outline-none
                            px-4 text-gray-900 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-500";
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-5">

                        {{-- ✅ FILA 1: Nº Documento + Tipo + Tipo Documento + Clasificación --}}
                        <div class="space-y-2 md:col-span-3">
                            <x-label for="document_number">Nº documento</x-label>
                            <div class="{{ $fieldWrap }}">
                                <input id="document_number" name="document_number" type="text"
                                       value="{{ old('document_number') }}"
                                       placeholder="Ej: 20601234567"
                                       class="{{ $fieldInput }}" autocomplete="off" />
                            </div>
                            <x-input-error for="document_number" class="mt-1" />
                        </div>

                        <div class="space-y-2 md:col-span-3">
                            <x-label for="company_type_id">Tipo</x-label>
                            <select name="company_type_id" class="{{ $controlBase }}">
                                <option value="">— Seleccionar —</option>
                                @foreach ($companyTypes as $t)
                                    <option value="{{ $t->id }}" @selected(old('company_type_id') == $t->id)>{{ $t->name }}</option>
                                @endforeach
                            </select>
                            <flux:error name="company_type_id" />
                        </div>

                        <div class="space-y-2 md:col-span-3">
                            <x-label for="document_type_id">Tipo documento</x-label>
                            <select name="document_type_id" class="{{ $controlBase }}">
                                <option value="">— Seleccionar —</option>
                                @foreach ($documentTypes as $t)
                                    <option value="{{ $t->id }}" @selected(old('document_type_id') == $t->id)>{{ $t->name }}</option>
                                @endforeach
                            </select>
                            <flux:error name="document_type_id" />
                        </div>

                        <div class="space-y-2 md:col-span-3">
                            <x-label>Clasificación</x-label>
                            <div class="h-12 flex items-center gap-4 px-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                    <input type="checkbox" name="is_customer" value="1"
                                           class="rounded border-gray-300 dark:border-gray-600"
                                           @checked(old('is_customer', true))>
                                    <span>Cliente</span>
                                </label>

                                <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                    <input type="checkbox" name="is_supplier" value="1"
                                           class="rounded border-gray-300 dark:border-gray-600"
                                           @checked(old('is_supplier', false))>
                                    <span>Proveedor</span>
                                </label>
                            </div>
                        </div>

                        {{-- ✅ FILA 2: Nombre (full) --}}
                        <div class="space-y-2 md:col-span-12">
                            <x-label for="name">Nombre <span class="text-red-500">*</span></x-label>
                            <div class="{{ $fieldWrap }}">
                                <input id="name" name="name" type="text"
                                       value="{{ old('name') }}"
                                       placeholder="Ej: Cliente Empresa SAC / Juan Pérez"
                                       class="{{ $fieldInput }}" autocomplete="off" />
                            </div>
                            <x-input-error for="name" class="mt-1" />
                        </div>

                        {{-- ✅ FILA 3: Ubigeo (full) --}}
                        <div class="md:col-span-12">
                            @livewire('admin.shared.ubigeo-select', [
                                'departmentId' => old('department_id'),
                                'provinceId' => old('province_id'),
                                'districtId' => old('district_id'),
                            ])
                        </div>

                        {{-- ✅ FILA 4: Dirección (full) - formato "nombre" --}}
                        <div class="space-y-2 md:col-span-12">
                            <x-label for="street">Dirección</x-label>
                            <div class="{{ $fieldWrap }}">
                                <input id="street" name="street" type="text"
                                       value="{{ old('street') }}"
                                       placeholder="Ej: Av. Arequipa 123, Int. 402 - Ref: frente al banco"
                                       class="{{ $fieldInput }}" autocomplete="off" />
                            </div>
                            <x-input-error for="street" class="mt-1" />
                        </div>

                        {{-- ✅ FILA 5: Lista de precios + Moneda + Estado + Acceso Portal (MISMA FILA) --}}
                        <div class="space-y-2 md:col-span-3">
                            <x-label for="pricelist_id">Lista de precios</x-label>
                            <select name="pricelist_id" class="{{ $controlBase }}">
                                <option value="">— Sin lista —</option>
                                @foreach ($pricelists as $p)
                                    <option value="{{ $p->id }}" @selected(old('pricelist_id') == $p->id)>{{ $p->name }}</option>
                                @endforeach
                            </select>
                            <flux:error name="pricelist_id" />
                        </div>

                        <div class="space-y-2 md:col-span-3">
                            <x-label for="currency_id">Moneda</x-label>
                            <select name="currency_id" class="{{ $controlBase }}">
                                <option value="">— Seleccionar —</option>
                                @foreach ($currencies as $c)
                                    <option value="{{ $c->id }}" @selected(old('currency_id') == $c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                            <flux:error name="currency_id" />
                        </div>

                        <div class="space-y-2 md:col-span-3">
                            <x-label>Estado</x-label>
                            <div class="h-12 flex items-center justify-between gap-4 px-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                    <input type="radio" name="status" value="1"
                                           class="text-emerald-600 focus:ring-emerald-500"
                                           {{ (string)old('status', '1') === '1' ? 'checked' : '' }}>
                                    <span>Activo</span>
                                </label>

                                <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                    <input type="radio" name="status" value="0"
                                           class="text-red-600 focus:ring-red-500"
                                           {{ (string)old('status') === '0' ? 'checked' : '' }}>
                                    <span>Desactivo</span>
                                </label>
                            </div>
                        </div>

                        <div class="space-y-2 md:col-span-3">
                            <x-label>Acceso Portal</x-label>
                            <div class="h-12 flex items-center gap-3 px-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                                <input type="checkbox" name="portal_access" value="1"
                                       class="rounded border-gray-300 dark:border-gray-600"
                                       @checked(old('portal_access', false))>
                                <span class="text-sm text-gray-700 dark:text-gray-200">Habilitar acceso</span>
                            </div>
                        </div>

                        {{-- ✅ FILA 6: Mapa (full) --}}
                        <div class="space-y-2 md:col-span-12">
                            <x-label for="map">Mapa (URL)</x-label>
                            <div class="{{ $fieldWrap }}">
                                <input id="map" name="map" type="text"
                                       value="{{ old('map') }}"
                                       placeholder="https://maps.google.com/..."
                                       class="{{ $fieldInput }}" autocomplete="off" />
                            </div>
                            <x-input-error for="map" class="mt-1" />
                        </div>

                    </div>
                </div>

                <!-- Footer actions -->
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-900/30 rounded-b-2xl">
                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Luego de guardar podrás agregar contactos (hijos) en la edición.
                        </p>

                        <div class="flex gap-2">
                            <a href="{{ route('admin.partners.index') }}"
                               class="inline-flex items-center justify-center rounded-xl px-4 py-2
                                border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800
                                text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                Cancelar
                            </a>

                            <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl px-5 py-2.5
                                bg-gradient-to-r from-indigo-600 to-sky-600 hover:from-indigo-700 hover:to-sky-700
                                text-white font-semibold shadow-md shadow-indigo-600/20
                                focus:outline-none focus:ring-4 focus:ring-indigo-500/30 transition">
                                <i class="fa-regular fa-floppy-disk mr-2"></i>
                                Crear partner
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>

    </div>
</x-layouts.app>
