<div class="space-y-6">

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => route('dashboard'),
            'Empresa' => route('admin.company.edit'),
            'Editar' => '#',
        ]" />
    </div>

    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Configuración de empresa</h1>
                <p class="text-sm text-gray-500 dark:text-gray-300">
                    Administra la identidad fiscal, branding, ubicación, correo y credenciales operativas del ERP.
                </p>
            </div>

            <button wire:click="save"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                       bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition">
                <i class="fa-regular fa-floppy-disk"></i> Guardar
            </button>
        </div>
    </div>

    <!-- Tabs + Content -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">

        <!-- Tabs -->
        <div class="px-6 pt-5">
            <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700">
                <button type="button" wire:click="setTab('general')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'general'
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-regular fa-building mr-2"></i> Información general
                </button>

                <button type="button" wire:click="setTab('contacto')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'contacto'
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-regular fa-envelope mr-2"></i> Contacto
                </button>

                <button type="button" wire:click="setTab('ubicacion')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'ubicacion'
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-solid fa-location-dot mr-2"></i> Ubicación
                </button>

                <button type="button" wire:click="setTab('sunat')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'sunat'
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-solid fa-file-invoice mr-2"></i> SUNAT y certificados
                </button>

                <button type="button" wire:click="setTab('branding')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'branding'
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-solid fa-image mr-2"></i> Branding
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">

            @if ($tab === 'general')
                <div class="space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Información general</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-300">
                            Define los datos fiscales y comerciales principales de la empresa.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">RUC</label>
                            <input wire:model.defer="ruc" type="text" placeholder="Ej: 20123456789"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                       focus:ring-4 focus:ring-indigo-500/25 focus:border-indigo-400 shadow-sm">
                            @error('ruc')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Nombre
                                comercial</label>
                            <input wire:model.defer="nombrecomercial" type="text" placeholder="Ej: TICOM"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                       focus:ring-4 focus:ring-indigo-500/25 focus:border-indigo-400 shadow-sm">
                            @error('nombrecomercial')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Razón social</label>
                            <input wire:model.defer="razonsocial" type="text" placeholder="Ej: TICOM PERÚ S.A.C."
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                       focus:ring-4 focus:ring-indigo-500/25 focus:border-indigo-400 shadow-sm">
                            @error('razonsocial')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Moneda</label>
                            <select wire:model.defer="currency_id"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                <option value="">-- Seleccionar --</option>
                                @foreach ($currencies as $currency)
                                    <option value="{{ $currency->id }}">
                                        {{ $currency->name ?? ($currency->code ?? $currency->id) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('currency_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">UBL Version</label>
                            <input wire:model.defer="ublversion" type="text" placeholder="Ej: 2.1"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('ublversion')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Detracción</label>
                            <input wire:model.defer="detraccion" type="text" placeholder="Ej: 0.1000"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('detraccion')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2 flex flex-wrap items-center gap-4 pt-2">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" class="w-5 h-5" wire:model.defer="production">
                                <span class="text-sm text-gray-700 dark:text-gray-200">Ambiente de producción</span>
                            </label>

                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" class="w-5 h-5" wire:model.defer="state">
                                <span class="text-sm text-gray-700 dark:text-gray-200">Empresa activa</span>
                            </label>
                        </div>
                    </div>
                </div>
            @endif

            @if ($tab === 'contacto')
                <div class="space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Contacto</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-300">
                            Configura los canales de comunicación y los datos del servidor SMTP.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Celular</label>
                            <input wire:model.defer="celular" type="text" placeholder="Ej: 999999999"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('celular')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Teléfono</label>
                            <input wire:model.defer="telefono" type="text" placeholder="Ej: 014445555"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('telefono')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Correo</label>
                            <input wire:model.defer="correo" type="email" placeholder="empresa@dominio.com"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('correo')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">SMTP</label>
                            <input wire:model.defer="smtp" type="text" placeholder="smtp.gmail.com"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('smtp')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Puerto</label>
                            <input wire:model.defer="puerto" type="text" placeholder="587"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('puerto')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Password SMTP</label>
                            <input wire:model.defer="password" type="text" placeholder="********"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('password')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            @endif

            @if ($tab === 'ubicacion')
                <div class="space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Ubicación</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-300">
                            Define la dirección fiscal y la ubicación geográfica de la empresa.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Dirección</label>
                            <input wire:model.defer="direccion" type="text" placeholder="Ej: Av. Principal 123"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('direccion')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Departamento</label>
                            <select wire:model.live="department_id"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                <option value="">-- Seleccionar --</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Provincia</label>
                            <select wire:model.live="province_id"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                <option value="">-- Seleccionar --</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province['id'] }}">{{ $province['name'] }}</option>
                                @endforeach
                            </select>
                            @error('province_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Distrito</label>
                            <select wire:model.live="district_id"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                <option value="">-- Seleccionar --</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district['id'] }}">{{ $district['name'] }}</option>
                                @endforeach
                            </select>
                            @error('district_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Ubigeo</label>
                            <input wire:model.defer="ubigeo" type="text" readonly
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('ubigeo')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            @endif

            @if ($tab === 'sunat')
                <div class="space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">SUNAT y certificados</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-300">
                            Configura credenciales SOL, certificado digital y acceso de integración.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">SOL User</label>
                            <input wire:model.defer="soluser" type="text"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                           bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('soluser')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">SOL Password</label>
                            <input wire:model.defer="solpass" type="text"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                           bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('solpass')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Cliente ID</label>
                            <input wire:model.defer="cliente_id" type="text"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                           bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('cliente_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Cliente
                                Secret</label>
                            <input wire:model.defer="cliente_secret" type="text"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                           bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('cliente_secret')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- SUBIDA DEL CERTIFICADO PEM --}}
                        <div class="md:col-span-2">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Cargar certificado digital (.pem)
                            </label>

                            <input wire:model="new_certificate" type="file" accept=".pem"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                           bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">

                            <div wire:loading wire:target="new_certificate" class="mt-2 text-sm text-blue-600">
                                Subiendo certificado...
                            </div>

                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Solo se permite archivos .pem de máximo 2 MB. El archivo debe contener CERTIFICATE y
                                PRIVATE KEY.
                            </p>

                            @error('new_certificate')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- RUTA ACTUAL DEL CERTIFICADO --}}
                        <div class="md:col-span-2">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Ruta actual del certificado
                            </label>

                            <input type="text" value="{{ $certificate_path }}" readonly
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                           bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200">

                            @error('certificate_path')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Inicio
                                certificado</label>
                            <input wire:model.defer="fechainiciocertificado" type="date"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                           bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('fechainiciocertificado')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Fin
                                certificado</label>
                            <input wire:model.defer="fechafincertificado" type="date"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                           bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('fechafincertificado')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Pago</label>
                            <input wire:model.defer="pago" type="text"
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                           bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            @error('pago')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            @endif

            @if ($tab === 'branding')
                <div class="space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Branding</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-300">
                            Administra el logo institucional que se usará en reportes, pantallas y marca de agua.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div
                            class="rounded-xl border border-gray-200 dark:border-gray-700 p-5 bg-gray-50 dark:bg-gray-900/30">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Vista previa</label>

                            <div
                                class="mt-3 flex items-center justify-center rounded-xl border border-dashed border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-6 min-h-[260px]">
                                <img src="{{ $new_logo ? $new_logo->temporaryUrl() : $company->logo_url }}"
                                    alt="Logo de empresa" class="max-h-52 object-contain rounded-lg">
                            </div>

                            <div class="mt-4 flex gap-2">
                                <button wire:click="removeLogo" type="button"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl
                                           bg-red-100 hover:bg-red-200 text-red-700 font-semibold transition">
                                    <i class="fa-solid fa-trash"></i> Quitar logo
                                </button>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Subir nuevo
                                    logo</label>
                                <input wire:model="new_logo" type="file"
                                    class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                                           bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                @error('new_logo')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div
                                class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900/30">
                                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Resumen técnico</h3>

                                <div class="mt-3 space-y-3 text-sm">
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400">Ruta actual</p>
                                        <p class="font-medium text-gray-800 dark:text-gray-100 break-all">
                                            {{ $company->logo ?: 'No definido' }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400">Nombre visual</p>
                                        <p class="font-medium text-gray-800 dark:text-gray-100">
                                            {{ $company->display_name }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400">Logo URL</p>
                                        <p class="font-medium text-gray-800 dark:text-gray-100 break-all">
                                            {{ $company->logo_url }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
