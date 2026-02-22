<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => route('dashboard'),
            'Partners' => route('admin.partners.index'),
            'Crear' => '#',
        ]" />
    </div>

    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Crear partner</h1>
                <p class="text-sm text-gray-500 dark:text-gray-300">
                    Crea la empresa/persona principal. Luego podrás agregar contactos (hijos) como en Odoo.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.partners.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                           bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold transition
                           hover:bg-gray-200 dark:hover:bg-gray-600">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>

                <button wire:click="save"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                           bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition">
                    <i class="fa-regular fa-floppy-disk"></i> Guardar
                </button>
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

                <button type="button" wire:click="setTab('accounting')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'accounting'
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-solid fa-calculator mr-2"></i> Contabilidad
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

            {{-- TAB: GENERAL --}}
            @if ($tab === 'general')
                <div class="grid grid-cols-1 md:grid-cols-12 gap-5">



                    <div class="md:col-span-3">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Nº documento</label>

                        <div class="mt-2 flex gap-2">
                            {{-- <input wire:model.defer="document_number" type="text" placeholder="DNI / RUC"
                                class="{{ $controlBase }} flex-1"> --}}
                            <input wire:model.defer="document_number" wire:keydown.enter="searchDocument" type="text"
                                placeholder="DNI / RUC" class="{{ $controlBase }} flex-1">

                            <button type="button" wire:click="searchDocument"
                                class="h-12 px-4 rounded-xl bg-gray-100 dark:bg-gray-700
                   text-gray-800 dark:text-gray-200 font-semibold
                   hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <span class="hidden sm:inline ml-2">Buscar</span>
                            </button>
                        </div>

                        @error('document_number')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-3">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Tipo</label>
                        <select wire:model.defer="company_type_id" class="{{ $controlBase }} mt-2">
                            <option value="">— Seleccionar —</option>
                            @foreach ($companyTypeOptions as $t)
                                <option value="{{ $t['id'] }}">{{ $t['name'] }}</option>
                            @endforeach
                        </select>
                        @error('company_type_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-3">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Tipo documento</label>
                        <select wire:model.defer="document_type_id" class="{{ $controlBase }} mt-2">
                            <option value="">— Seleccionar —</option>
                            @foreach ($documentTypeOptions as $t)
                                <option value="{{ $t['id'] }}">{{ $t['code'] }}</option>
                            @endforeach
                        </select>
                        @error('document_type_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-3">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Clasificación</label>
                        <div
                            class="mt-2 h-12 flex items-center gap-4 px-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                <input type="checkbox" class="w-5 h-5" wire:model.defer="is_customer">
                                <span>Cliente</span>
                            </label>

                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                <input type="checkbox" class="w-5 h-5" wire:model.defer="is_supplier">
                                <span>Proveedor</span>
                            </label>
                        </div>
                        @error('is_customer')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        @error('is_supplier')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- FILA 2: Nombre --}}
                    <div class="md:col-span-6">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input wire:model.defer="name" type="text" placeholder="Ej: Cliente Empresa SAC / Juan Pérez"
                            class="{{ $controlBase }} mt-2">
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- FILA 3: Dirección --}}
                    <div class="md:col-span-6">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Dirección</label>
                        <input wire:model.defer="street" type="text" placeholder="Ej: Av. Arequipa 123, Int. 402..."
                            class="{{ $controlBase }} mt-2">
                        @error('street')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- FILA: Contacto (Email / Teléfonos / Web) --}}
                    <div class="md:col-span-4">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Email {{ $portal_access ? '*' : '' }}
                        </label>
                        <input wire:model.defer="email" type="email" placeholder="correo@dominio.com"
                            class="{{ $controlBase }} mt-2">
                        @error('email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror

                        @if ($portal_access)
                            <p class="text-xs text-gray-500 dark:text-gray-300 mt-1">
                                * Requerido para habilitar acceso al portal.
                            </p>
                        @endif
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Teléfono</label>
                        <input wire:model.defer="phone" type="text" placeholder="01-555-1234"
                            class="{{ $controlBase }} mt-2">
                        @error('phone')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">WhatsApp</label>
                        <input wire:model.defer="whatsapp" type="text" placeholder="9xxxxxxxx"
                            class="{{ $controlBase }} mt-2">
                        @error('whatsapp')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Móvil</label>
                        <input wire:model.defer="mobile" type="text" placeholder="9xxxxxxxx"
                            class="{{ $controlBase }} mt-2">
                        @error('mobile')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Website</label>
                        <input wire:model.defer="website" type="text" placeholder="https://miweb.com"
                            class="{{ $controlBase }} mt-2">
                        @error('website')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>



                    {{-- ✅ FILA 5: Departamento, Provincia, Distrito, Moneda, Estado (TODO 1 FILA) --}}
                    <div class="md:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Departamento</label>

                        {{-- Departamento --}}
                        <select wire:model.live="department_id" class="{{ $controlBase }} mt-2">
                            <option value="">— Seleccionar —</option>
                            @foreach ($this->departments as $d)
                                <option value="{{ data_get($d, 'id') }}">{{ data_get($d, 'name') }}</option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Provincia</label>


                        <select wire:key="province-select-{{ $department_id ?? 'none' }}"
                            wire:model.live="province_id" @disabled(!$department_id)
                            class="{{ $controlBase }} mt-2">
                            <option value="">— Seleccionar —</option>
                            @foreach ($this->provinces as $p)
                                <option value="{{ data_get($p, 'id') }}">{{ data_get($p, 'name') }}</option>
                            @endforeach
                        </select>

                        @error('province_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Distrito</label>

                        <select wire:key="district-select-{{ $province_id ?? 'none' }}" wire:model.live="district_id"
                            @disabled(!$province_id) class="{{ $controlBase }} mt-2">
                            <option value="">— Seleccionar —</option>
                            @foreach ($this->districts as $di)
                                <option value="{{ data_get($di, 'id') }}">{{ data_get($di, 'name') }}</option>
                            @endforeach
                        </select>
                        @error('district_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- FILA 4: Lista precios --}}
                    <div class="md:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Lista de precios</label>
                        <select wire:model.defer="pricelist_id" class="{{ $controlBase }} mt-2">
                            <option value="">— Sin lista —</option>
                            @foreach ($pricelistOptions as $p)
                                <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                            @endforeach
                        </select>
                        @error('pricelist_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Moneda</label>
                        <select wire:model.defer="currency_id" class="{{ $controlBase }} mt-2">
                            <option value="">— Seleccionar —</option>
                            @foreach ($currencyOptions as $c)
                                <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                            @endforeach
                        </select>
                        @error('currency_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Estado</label>
                        <div
                            class="mt-2 h-12 flex items-center justify-between gap-4 px-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                <input type="radio" wire:model.defer="status" value="1">
                                <span>Activo</span>
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                <input type="radio" wire:model.defer="status" value="0">
                                <span>Desactivo</span>
                            </label>
                        </div>
                        @error('status')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ✅ FILA 6: Acceso Portal + Mapa --}}
                    {{-- <div class="md:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Acceso Portal</label>
                        <div
                            class="mt-2 h-12 flex items-center gap-3 px-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                            <input type="checkbox" class="w-5 h-5" wire:model.defer="portal_access">
                            <span class="text-sm text-gray-700 dark:text-gray-200">Habilitar acceso</span>
                        </div>
                        @error('portal_access')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div> --}}

                    <div class="md:col-span-12">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Mapa (URL)</label>
                        <input wire:model.defer="map" type="text" placeholder="https://maps.google.com/..."
                            class="{{ $controlBase }} mt-2">
                        @error('map')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            @endif

            {{-- TAB: CONTABILIDAD --}}
            @if ($tab === 'accounting')
                <div class="space-y-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Contabilidad</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-300">
                            Define las cuentas por defecto para el partner.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Cuenta por
                                cobrar</label>
                            <select wire:model.defer="account_receivable_id" class="{{ $controlBase }} mt-2">
                                <option value="">— Seleccionar —</option>
                                @foreach ($accountOptions as $a)
                                    <option value="{{ $a['id'] }}">{{ $a['label'] }}</option>
                                @endforeach
                            </select>
                            @error('account_receivable_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Cuenta por
                                pagar</label>
                            <select wire:model.defer="account_payable_id" class="{{ $controlBase }} mt-2">
                                <option value="">— Seleccionar —</option>
                                @foreach ($accountOptions as $a)
                                    <option value="{{ $a['id'] }}">{{ $a['label'] }}</option>
                                @endforeach
                            </select>
                            @error('account_payable_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button wire:click="save"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl
                                   bg-gradient-to-r from-indigo-600 to-sky-600 hover:from-indigo-700 hover:to-sky-700
                                   text-white font-semibold shadow-sm transition">
                            <i class="fa-regular fa-floppy-disk"></i> Guardar partner
                        </button>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
