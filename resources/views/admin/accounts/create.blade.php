<x-layouts.app :title="__('Crear Usuario')">
    <div class="flex flex-col gap-6">

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-4">
            <x-breadcrumb :links="[
                'Dashboard' => route('dashboard'),
                'Cuenta Contable' => route('admin.accounts.index'),
                'Crear Cuenta Contable' => '#',
            ]" />
        </div>


        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100"><i
                        class="fa-solid fa-user-shield text-primary"></i> Creando Nueva Cuenta Contable</h1>
                {{--   <p class="text-gray-500 text-sm">Administra cuentas y roles del sistema</p> --}}
            </div>

        </div>

        <!-- Formulario -->




        <form method="POST" action="{{ route('admin.accounts.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Código -->
                <div>
                    <x-label for="code">Código:</x-label>
                    <flux:input icon="user" name="code" value="{{ old('code') }}"
                        placeholder="Ej: 101, 112, 1011" maxlength="10" />
                    <flux:error name="code" />
                </div>

                <!-- Nombre -->
                <div>
                    <x-label for="name">Nombre:</x-label>
                    <flux:input icon="user" name="name" value="{{ old('name') }}"
                        placeholder="Caja, Bancos, Ventas..." />
                    <flux:error name="name" />
                </div>

                <!-- Código Equivalente -->
                <div>
                    <x-label for="equivalent_code">Código Equivalente (SUNAT):</x-label>
                    <flux:input icon="user" name="equivalent_code" value="{{ old('equivalent_code') }}"
                        placeholder="Ej: 1011" />
                    <flux:error name="equivalent_code" />
                </div>

                <!-- Tipo de cuenta -->
                <div>
                    <x-label for="account_type_id">Tipo de Cuenta:</x-label>
                    <select name="account_type_id"
                        class="block w-full pl-3 pr-3 py-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Selecciona un tipo</option>
                        @foreach ($accountTypes as $type)
                            <option value="{{ $type->id }}"
                                {{ old('account_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error for="account_type_id" />
                </div>
            </div>

            <!-- Opciones -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-6 space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Opciones</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="reconcile" value="1" {{ old('reconcile') ? 'checked' : '' }}
                            class="w-5 h-5 text-indigo-600 border-gray-300 rounded">
                        <span>Permite conciliar</span>
                    </label>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="cost_center" value="1"
                            {{ old('cost_center') ? 'checked' : '' }}
                            class="w-5 h-5 text-indigo-600 border-gray-300 rounded">
                        <span>Requiere centro de costos</span>
                    </label>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="current_account" value="1"
                            {{ old('current_account') ? 'checked' : '' }}
                            class="w-5 h-5 text-indigo-600 border-gray-300 rounded">
                        <span>Es cuenta corriente</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end">
                <x-button type="submit" class="w-full md:w-auto">
                    <i class="fa-regular fa-floppy-disk mr-2"></i> Crear Cuenta
                </x-button>
            </div>
        </form>


    </div>


</x-layouts.app>
