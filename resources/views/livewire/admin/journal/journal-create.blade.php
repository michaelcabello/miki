<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => route('dashboard'),
            'Diarios'   => route('admin.journals.index'),
            'Crear'     => '#',
        ]" />
    </div>

    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Crear diario</h1>
                <p class="text-sm text-gray-500 dark:text-gray-300">
                    Define un diario contable: ventas, compras, banco, caja, etc.
                </p>
            </div>
            <div class="flex items-center gap-2">
                @can('Journal Create')
                    <button wire:click="save"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                               bg-gradient-to-r from-indigo-600 to-sky-600 hover:from-indigo-700 hover:to-sky-700
                               text-white font-semibold shadow-sm transition">
                        <i class="fa-regular fa-floppy-disk"></i> Guardar
                    </button>
                @endcan
                <a href="{{ route('admin.journals.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                           bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold transition
                           hover:bg-gray-200 dark:hover:bg-gray-600">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    {{-- Tabs + Contenido --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">

        {{-- Tabs --}}
        <div class="px-6 pt-5">
            <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700">
                <button type="button" wire:click="setTab('general')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'general' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-regular fa-file-lines mr-2"></i> General
                </button>
                <button type="button" wire:click="setTab('accounts')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'accounts' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-solid fa-scale-balanced mr-2"></i> Cuentas
                </button>
                <button type="button" wire:click="setTab('bank')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'bank' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-solid fa-building-columns mr-2"></i> Banco / Caja
                </button>
                <button type="button" wire:click="setTab('documents')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'documents' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-solid fa-file-invoice mr-2"></i> Documentos
                </button>
            </div>
        </div>

        @php
            $controlBase = "w-full h-12 px-4 rounded-xl border border-gray-300 dark:border-gray-600
                            bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                            focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition";
            $selectBase  = $controlBase . " cursor-pointer";
            $labelBase   = "text-sm font-semibold text-gray-700 dark:text-gray-200";
        @endphp

        <div class="p-6 space-y-6">

            {{-- ══════════════════════════ TAB: GENERAL ══════════════════════════ --}}
            @if ($tab === 'general')
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">

                {{-- Código --}}
                <div class="md:col-span-3">
                    <label class="{{ $labelBase }}">Código <span class="text-red-500">*</span></label>
                    <input wire:model.live="code" type="text" placeholder="Ej: BNK1, CSH, SAJ..."
                        class="{{ $controlBase }} mt-2 uppercase">
                    <p class="text-xs text-gray-500 dark:text-gray-300 mt-1">
                        Mayúsculas, números, _ y -. Se normaliza automáticamente.
                    </p>
                    @error('code') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Nombre --}}
                <div class="md:col-span-6">
                    <label class="{{ $labelBase }}">Nombre <span class="text-red-500">*</span></label>
                    <input wire:model.defer="name" type="text" placeholder="Ej: Banco BCP, Caja Chica, Ventas..."
                        class="{{ $controlBase }} mt-2">
                    @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tipo de Diario --}}
                <div class="md:col-span-3">
                    <label class="{{ $labelBase }}">Tipo de diario <span class="text-red-500">*</span></label>
                    <select wire:model.defer="journal_type_id" class="{{ $selectBase }} mt-2">
                        <option value="">— Seleccionar —</option>
                        @foreach ($journalTypes as $jt)
                            <option value="{{ $jt->id }}">{{ $jt->name }}</option>
                        @endforeach
                    </select>
                    @error('journal_type_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Moneda --}}
                <div class="md:col-span-3">
                    <label class="{{ $labelBase }}">Moneda</label>
                    <select wire:model.defer="currency_id" class="{{ $selectBase }} mt-2">
                        <option value="">— Moneda por defecto —</option>
                        @foreach ($currencies as $cur)
                            <option value="{{ $cur->id }}">{{ $cur->name }}</option>
                        @endforeach
                    </select>
                    @error('currency_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Estado --}}
                <div class="md:col-span-3">
                    <label class="{{ $labelBase }}">Estado</label>
                    <div class="mt-2 h-12 flex items-center justify-between gap-4 px-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <input type="radio" wire:model.defer="state" value="1"> <span>Activo</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <input type="radio" wire:model.defer="state" value="0"> <span>Inactivo</span>
                        </label>
                    </div>
                    @error('state') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Activo (Archivado) --}}
                <div class="md:col-span-3">
                    <label class="{{ $labelBase }}">Archivado</label>
                    <div class="mt-2 h-12 flex items-center px-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <input type="checkbox" wire:model.defer="active"
                                class="rounded border-gray-300 dark:border-gray-600 text-indigo-600">
                            <span>Diario activo (no archivado)</span>
                        </label>
                    </div>
                </div>

                {{-- Usa documentos --}}
                <div class="md:col-span-3">
                    <label class="{{ $labelBase }}">Documentos</label>
                    <div class="mt-2 h-12 flex items-center px-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <input type="checkbox" wire:model.defer="use_documents"
                                class="rounded border-gray-300 dark:border-gray-600 text-indigo-600">
                            <span>Usar documentos</span>
                        </label>
                    </div>
                </div>

            </div>
            @endif

            {{-- ══════════════════════════ TAB: CUENTAS ══════════════════════════ --}}
            @if ($tab === 'accounts')
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">

                {{-- Helper info --}}
                <div class="md:col-span-12">
                    <div class="rounded-xl border border-sky-200 dark:border-sky-800 bg-sky-50 dark:bg-sky-900/20 p-4">
                        <p class="text-sm text-sky-700 dark:text-sky-300">
                            <i class="fa-solid fa-circle-info mr-2"></i>
                            Las cuentas son opcionales y dependen del tipo de diario. Para diarios de banco/caja,
                            configura las cuentas de débito y crédito por defecto.
                        </p>
                    </div>
                </div>

                {{-- Cuenta principal --}}
                <div class="md:col-span-6">
                    <label class="{{ $labelBase }}">Cuenta contrapartida principal</label>
                    <select wire:model.defer="account_id" class="{{ $selectBase }} mt-2">
                        <option value="">— Sin cuenta —</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->code }} — {{ $acc->name }}</option>
                        @endforeach
                    </select>
                    @error('account_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Cuenta débito por defecto --}}
                <div class="md:col-span-6">
                    <label class="{{ $labelBase }}">Cuenta débito por defecto</label>
                    <select wire:model.defer="default_debit_account_id" class="{{ $selectBase }} mt-2">
                        <option value="">— Sin cuenta —</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->code }} — {{ $acc->name }}</option>
                        @endforeach
                    </select>
                    @error('default_debit_account_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Cuenta crédito por defecto --}}
                <div class="md:col-span-6">
                    <label class="{{ $labelBase }}">Cuenta crédito por defecto</label>
                    <select wire:model.defer="default_credit_account_id" class="{{ $selectBase }} mt-2">
                        <option value="">— Sin cuenta —</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->code }} — {{ $acc->name }}</option>
                        @endforeach
                    </select>
                    @error('default_credit_account_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Cuenta suspense / puente --}}
                <div class="md:col-span-6">
                    <label class="{{ $labelBase }}">Cuenta suspense / puente</label>
                    <select wire:model.defer="suspense_account_id" class="{{ $selectBase }} mt-2">
                        <option value="">— Sin cuenta —</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->code }} — {{ $acc->name }}</option>
                        @endforeach
                    </select>
                    @error('suspense_account_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Cuenta ganancia tipo de cambio --}}
                <div class="md:col-span-6">
                    <label class="{{ $labelBase }}">Ganancia diferencia de cambio</label>
                    <select wire:model.defer="exchange_gain_account_id" class="{{ $selectBase }} mt-2">
                        <option value="">— Sin cuenta —</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->code }} — {{ $acc->name }}</option>
                        @endforeach
                    </select>
                    @error('exchange_gain_account_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Cuenta pérdida tipo de cambio --}}
                <div class="md:col-span-6">
                    <label class="{{ $labelBase }}">Pérdida diferencia de cambio</label>
                    <select wire:model.defer="exchange_loss_account_id" class="{{ $selectBase }} mt-2">
                        <option value="">— Sin cuenta —</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->code }} — {{ $acc->name }}</option>
                        @endforeach
                    </select>
                    @error('exchange_loss_account_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

            </div>
            @endif

            {{-- ══════════════════════════ TAB: BANCO ══════════════════════════ --}}
            @if ($tab === 'bank')
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">

                <div class="md:col-span-12">
                    <div class="rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-4">
                        <p class="text-sm text-amber-700 dark:text-amber-300">
                            <i class="fa-solid fa-building-columns mr-2"></i>
                            Completa estos datos solo si el diario es de tipo <strong>Banco</strong> o <strong>Caja</strong>.
                        </p>
                    </div>
                </div>

                {{-- Nombre del banco --}}
                <div class="md:col-span-6">
                    <label class="{{ $labelBase }}">Nombre del banco</label>
                    <input wire:model.defer="bank_name" type="text" placeholder="Ej: BCP, BBVA, Scotiabank..."
                        class="{{ $controlBase }} mt-2">
                    @error('bank_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Número de cuenta --}}
                <div class="md:col-span-6">
                    <label class="{{ $labelBase }}">Número de cuenta bancaria</label>
                    <input wire:model.defer="bank_account_number" type="text" placeholder="Ej: 194-12345678-0-01"
                        class="{{ $controlBase }} mt-2">
                    @error('bank_account_number') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- CCI --}}
                <div class="md:col-span-4">
                    <label class="{{ $labelBase }}">CCI (Código de Cuenta Interbancario)</label>
                    <input wire:model.defer="cci" type="text" placeholder="20 dígitos..."
                        class="{{ $controlBase }} mt-2">
                    @error('cci') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- SWIFT --}}
                <div class="md:col-span-4">
                    <label class="{{ $labelBase }}">SWIFT / BIC</label>
                    <input wire:model.defer="swift" type="text" placeholder="Ej: BCPLPEPL"
                        class="{{ $controlBase }} mt-2">
                    @error('swift') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- IBAN --}}
                <div class="md:col-span-4">
                    <label class="{{ $labelBase }}">IBAN</label>
                    <input wire:model.defer="iban" type="text" placeholder="Código IBAN internacional..."
                        class="{{ $controlBase }} mt-2">
                    @error('iban') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

            </div>
            @endif

            {{-- ══════════════════════════ TAB: DOCUMENTOS ══════════════════════════ --}}
            @if ($tab === 'documents')
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">

                {{-- Prefijo de documento --}}
                <div class="md:col-span-3">
                    <label class="{{ $labelBase }}">Prefijo del documento</label>
                    <input wire:model.defer="document_prefix" type="text" placeholder="Ej: F001, B001..."
                        class="{{ $controlBase }} mt-2 uppercase">
                    <p class="text-xs text-gray-500 dark:text-gray-300 mt-1">
                        Serie de la factura o boleta (para facturación electrónica).
                    </p>
                    @error('document_prefix') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Número siguiente --}}
                <div class="md:col-span-3">
                    <label class="{{ $labelBase }}">Número inicial <span class="text-red-500">*</span></label>
                    <input wire:model.defer="document_next_number" type="number" min="1" step="1"
                        class="{{ $controlBase }} mt-2" placeholder="1">
                    @error('document_next_number') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Usar numeración automática --}}
                <div class="md:col-span-3">
                    <label class="{{ $labelBase }}">Numeración automática</label>
                    <div class="mt-2 h-12 flex items-center px-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <input type="checkbox" wire:model.defer="use_document_sequence"
                                class="rounded border-gray-300 dark:border-gray-600 text-indigo-600">
                            <span>Secuencia automática</span>
                        </label>
                    </div>
                </div>

                {{-- Permitir asientos manuales --}}
                <div class="md:col-span-3">
                    <label class="{{ $labelBase }}">Asientos manuales</label>
                    <div class="mt-2 h-12 flex items-center px-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <input type="checkbox" wire:model.defer="allow_manual_entries"
                                class="rounded border-gray-300 dark:border-gray-600 text-indigo-600">
                            <span>Permitir asientos manuales</span>
                        </label>
                    </div>
                </div>

                {{-- Configuración adicional (JSON) --}}
                <div class="md:col-span-12">
                    <label class="{{ $labelBase }}">Configuración adicional (JSON)</label>
                    <textarea wire:model.defer="settings_raw" rows="5"
                        placeholder='{"clave": "valor"}'
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600
                               bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-mono text-sm
                               focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition mt-2">
                    </textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-300 mt-1">
                        Opcional. Ingresa un objeto JSON válido para configuración flexible.
                    </p>
                    @error('settings_raw') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

            </div>
            @endif

            {{-- Botón guardar global (siempre visible) --}}
            @can('Journal Create')
            <div class="pt-2 flex justify-end border-t border-gray-200 dark:border-gray-700">
                <button wire:click="save"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl
                           bg-gradient-to-r from-indigo-600 to-sky-600 hover:from-indigo-700 hover:to-sky-700
                           text-white font-semibold shadow-sm transition">
                    <i class="fa-regular fa-floppy-disk"></i> Guardar diario
                </button>
            </div>
            @endcan

        </div>
    </div>
</div>
