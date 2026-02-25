<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => route('dashboard'),
            'Account Settings' => '#',
        ]" />
    </div>

    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Configuración Contable</h1>
                <p class="text-sm text-gray-500 dark:text-gray-300">
                    Define las cuentas por defecto para ventas, compras, impuestos y redondeos.
                </p>
            </div>

            <button wire:click="save"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                       bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition">
                <i class="fa-regular fa-floppy-disk"></i> Guardar
            </button>
        </div>
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
                    <i class="fa-solid fa-gear mr-2"></i> General
                </button>

                <button type="button" wire:click="setTab('income')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'income'
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-solid fa-arrow-trend-up mr-2"></i> Ingresos
                </button>

                <button type="button" wire:click="setTab('expense')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'expense'
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-solid fa-arrow-trend-down mr-2"></i> Gastos
                </button>

                <button type="button" wire:click="setTab('tax')"
                    class="px-4 py-2 rounded-t-xl text-sm font-semibold transition
                        {{ $tab === 'tax'
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    <i class="fa-solid fa-percent mr-2"></i> Impuestos
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
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Cuenta por cobrar (CxC)</label>
                            <select wire:model.defer="default_receivable_account_id" class="{{ $controlBase }} mt-2">
                                <option value="">— Seleccionar —</option>
                                @foreach ($accountOptions as $a)
                                    <option value="{{ $a['id'] }}">{{ $a['label'] }}</option>
                                @endforeach
                            </select>
                            @error('default_receivable_account_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Cuenta por pagar (CxP)</label>
                            <select wire:model.defer="default_payable_account_id" class="{{ $controlBase }} mt-2">
                                <option value="">— Seleccionar —</option>
                                @foreach ($accountOptions as $a)
                                    <option value="{{ $a['id'] }}">{{ $a['label'] }}</option>
                                @endforeach
                            </select>
                            @error('default_payable_account_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Redondeo / Diferencias</label>
                            <select wire:model.defer="rounding_account_id" class="{{ $controlBase }} mt-2">
                                <option value="">— (Opcional) —</option>
                                @foreach ($accountOptions as $a)
                                    <option value="{{ $a['id'] }}">{{ $a['label'] }}</option>
                                @endforeach
                            </select>
                            @error('rounding_account_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Activo</label>
                            <div class="mt-2 h-12 flex items-center justify-between gap-4 px-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700">
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                    <input type="checkbox" class="w-5 h-5" wire:model.defer="active">
                                    <span>Habilitado</span>
                                </label>
                            </div>
                            @error('active') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                    </div>
                </div>
            @endif

            {{-- TAB: INGRESOS --}}
            @if ($tab === 'income')
                <div class="space-y-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Ingresos (Ventas)</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-300">
                            Define cuentas por defecto para ventas de mercaderías y servicios.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Ingreso Mercaderías (70...)</label>
                            <select wire:model.defer="default_income_goods_account_id" class="{{ $controlBase }} mt-2">
                                <option value="">— Seleccionar —</option>
                                @foreach ($accountOptions as $a)
                                    <option value="{{ $a['id'] }}">{{ $a['label'] }}</option>
                                @endforeach
                            </select>
                            @error('default_income_goods_account_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Ingreso Servicios (70...)</label>
                            <select wire:model.defer="default_income_service_account_id" class="{{ $controlBase }} mt-2">
                                <option value="">— Seleccionar —</option>
                                @foreach ($accountOptions as $a)
                                    <option value="{{ $a['id'] }}">{{ $a['label'] }}</option>
                                @endforeach
                            </select>
                            @error('default_income_service_account_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Fallback Ingresos (si no hay nada definido)</label>
                            <select wire:model.defer="default_income_account_id" class="{{ $controlBase }} mt-2">
                                <option value="">— (Opcional) —</option>
                                @foreach ($accountOptions as $a)
                                    <option value="{{ $a['id'] }}">{{ $a['label'] }}</option>
                                @endforeach
                            </select>
                            @error('default_income_account_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            @endif

            {{-- TAB: GASTOS --}}
            @if ($tab === 'expense')
                <div class="space-y-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Gastos (Compras)</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-300">
                            Define cuentas por defecto para compras de mercaderías y servicios.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Gasto Mercaderías (60...)</label>
                            <select wire:model.defer="default_expense_goods_account_id" class="{{ $controlBase }} mt-2">
                                <option value="">— Seleccionar —</option>
                                @foreach ($accountOptions as $a)
                                    <option value="{{ $a['id'] }}">{{ $a['label'] }}</option>
                                @endforeach
                            </select>
                            @error('default_expense_goods_account_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Gasto Servicios (63...)</label>
                            <select wire:model.defer="default_expense_service_account_id" class="{{ $controlBase }} mt-2">
                                <option value="">— Seleccionar —</option>
                                @foreach ($accountOptions as $a)
                                    <option value="{{ $a['id'] }}">{{ $a['label'] }}</option>
                                @endforeach
                            </select>
                            @error('default_expense_service_account_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Fallback Gastos (si no hay nada definido)</label>
                            <select wire:model.defer="default_expense_account_id" class="{{ $controlBase }} mt-2">
                                <option value="">— (Opcional) —</option>
                                @foreach ($accountOptions as $a)
                                    <option value="{{ $a['id'] }}">{{ $a['label'] }}</option>
                                @endforeach
                            </select>
                            @error('default_expense_account_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            @endif

            {{-- TAB: IMPUESTOS --}}
            @if ($tab === 'tax')
                <div class="space-y-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Impuestos</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-300">
                            Fallback de cuentas para IGV (si un impuesto no define repartición).
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">IGV Ventas (40...)</label>
                            <select wire:model.defer="default_sales_tax_account_id" class="{{ $controlBase }} mt-2">
                                <option value="">— Seleccionar —</option>
                                @foreach ($accountOptions as $a)
                                    <option value="{{ $a['id'] }}">{{ $a['label'] }}</option>
                                @endforeach
                            </select>
                            @error('default_sales_tax_account_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">IGV Compras (40...)</label>
                            <select wire:model.defer="default_purchase_tax_account_id" class="{{ $controlBase }} mt-2">
                                <option value="">— Seleccionar —</option>
                                @foreach ($accountOptions as $a)
                                    <option value="{{ $a['id'] }}">{{ $a['label'] }}</option>
                                @endforeach
                            </select>
                            @error('default_purchase_tax_account_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('show-swal', (event) => {
                    Swal.fire(event.title || 'OK', event.text || '', event.icon || 'success');
                });
            });
        </script>
    @endpush
</div>
