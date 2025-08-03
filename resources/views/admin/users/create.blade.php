<x-layouts.app :title="__('Crear Usuario')">
    <div class="flex flex-col gap-6">
        <!-- Breadcrumb -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-4">
            <x-breadcrumb :links="[
                'Dashboard' => route('dashboard'),
                'Usuarios' => route('admin.users.index'),
                'Crear Usuario' => '#',
            ]" />
        </div>


        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100"><i
                        class="fa-solid fa-user-shield text-primary"></i> Creando Nuevo Usuario</h1>
                {{--   <p class="text-gray-500 text-sm">Administra cuentas y roles del sistema</p> --}}
            </div>
            {{-- <div class="flex flex-wrap gap-2">

                <a href="{{ route('admin.roles.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                    <i class="fa-solid fa-plus mr-2"></i> Guardar
                </a>

            </div> --}}
        </div>

        <!-- Formulario -->
        <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <!-- Datos Personales -->
            {{-- <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 space-y-4"> --}}
            <div
                class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm p-6 space-y-4">


                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Datos del Usuario</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">

                    <div>
                        <x-label for="name">Nombre completo:</x-label>
                        <flux:input icon="user" name="name" value="{{ old('name') }}"
                            placeholder="Nombres y Apellidos" />
                        <flux:error name="name" />
                    </div>

                    <div>
                        <x-label for="email">Email:</x-label>
                        <flux:input icon="envelope" name="email" value="{{ old('email') }}" placeholder="email" />
                        <flux:error name="email" />
                    </div>



                    <div>
                        <x-label for="password">Contraseña:</x-label>
                        <flux:input type="password" name="password" viewable />
                        <flux:error name="password" />

                    </div>

                    <div>
                        <x-label for="password">Repetir Contraseña:</x-label>
                        <flux:input type="password" name="password_confirmation" viewable />
                        {{-- <flux:error name="password" /> --}}
                    </div>

                    <div>
                        <x-label for="name">Dirección:</x-label>
                        <flux:input icon="user" name="address" value="{{ old('address') }}"
                            placeholder="Dirección" />
                    </div>

                    <div>
                        <x-label for="name">Celular:</x-label>
                        <flux:input icon="phone" name="movil" value="{{ old('movil') }}" placeholder="Celular" />
                    </div>

                    <div>
                        <x-label for="name">DNI:</x-label>
                        <flux:input icon="credit-card" name="dni" value="{{ old('dni') }}" placeholder="DNI" />
                    </div>




                    <div>
                        <x-label for="position_id">Cargo:</x-label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-briefcase absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <select name="position_id"
                                class="block w-full pl-10 pr-3 py-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Selecciona un cargo</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position->id }}"
                                        {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                        {{ $position->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <x-input-error for="position_id" />
                    </div>



                    <div>
                        <x-label for="gender">Género:</x-label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <select name="gender"
                                class="block w-full pl-10 pr-3 py-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Escoger</option>
                                <option value="1" {{ old('gender') == '1' ? 'selected' : '' }}>Masculino</option>
                                <option value="2" {{ old('gender') == '2' ? 'selected' : '' }}>Femenino</option>
                            </select>
                        </div>
                        <x-input-error for="gender" />
                    </div>

                    {{-- <div>
                        <x-label for="name">Genero:</x-label>
                        <flux:select wire:model="gender" placeholder="Seleccione...">
                            <flux:select.option>Varón</flux:select.option>
                            <flux:select.option>Mujer</flux:select.option>

                        </flux:select>
                    </div>
 --}}


                    <div>
                        <x-label for="local_id">Local:</x-label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-location-dot absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <select name="local_id"
                                class="block w-full pl-10 pr-3 py-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Selecciona un local</option>
                                @foreach ($locales as $local)
                                    <option value="{{ $local->id }}"
                                        {{ old('local_id') == $local->id ? 'selected' : '' }}>
                                        {{ $local->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <x-input-error for="local_id" />
                    </div>

                    <div class="flex items-center mt-2">
                        <input type="checkbox" name="state" id="state" value="1"
                            class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                            {{ old('state', 0) ? 'checked' : '' }}>
                        <label for="state" class="ml-2 text-sm text-gray-700 dark:text-gray-200">
                            Usuario Activo
                        </label>
                    </div>

                    <div>

                        <flux:input type="file" name="photo" label="Foto" />
                    </div>

                </div>
            </div>

            <!-- Roles -->
            {{--  <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Asignar Roles</h3>

                <div class="flex flex-wrap gap-3">
                    @foreach ($roles as $role)
                        <label
                            class="flex items-center gap-2 px-4 py-2 rounded-full border border-gray-300 dark:border-gray-600
                text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700
                cursor-pointer transition-all">
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                class="text-indigo-600 focus:ring-indigo-500 rounded"
                                {{ collect(old('roles', []))->contains($role->id) ? 'checked' : '' }}>
                            {{ $role->display_name ?? $role->name }}
                        </label>
                    @endforeach
                </div>
            </div> --}}


            {{-- <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Asignar Roles</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($roles as $role)
                        <label
                            class="group block border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-800 shadow-sm hover:shadow-md transition-all cursor-pointer">
                            <div class="flex items-start gap-3">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                    class="mt-1.5 w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    {{ collect(old('roles', []))->contains($role->id) ? 'checked' : '' }}>

                                <div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                        {{ $role->display_name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $role->name }}
                                    </p>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div> --}}

            <!-- Roles -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Asignar Roles</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                    @foreach ($roles as $role)
                        <div
                            class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-gray-50 dark:bg-gray-800">


                            <div class="flex justify-between items-center">
                                <label class="flex items-center gap-3 text-sm text-gray-700 dark:text-gray-200">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                        class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        {{ collect(old('roles', []))->contains($role->id) ? 'checked' : '' }}>
                                    <p class="font-semibold text-gray-800 dark:text-white">
                                        {{ $role->display_name }}
                                    </p>
                                </label>
                                <!-- Botón lupa -->
                                <button x-data
                                    @click="$dispatch('show-permissions-modal', { roleId: {{ $role->id }} })"
                                    type="button" class="text-blue-600 hover:text-blue-800 transition"
                                    title="Ver permisos de este rol">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                            </div>


                        </div>
                    @endforeach
                </div>
            </div>




            <!-- Permisos -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Permisos Específicos</h3>

                @php
                    $oldPermissions = collect(old('permissions', []));
                @endphp

                @forelse ($permissions as $module => $modulePermissions)
                    <div
                        class="mb-6 border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-gray-50 dark:bg-gray-800">
                        <h4 class="text-base font-semibold text-gray-800 dark:text-white mb-4">
                            {{ ucfirst($module) }}
                        </h4>

                        <div
                            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-3">
                            @foreach ($modulePermissions as $permission)
                                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                        class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        {{ $oldPermissions->contains($permission->name) ? 'checked' : '' }}>
                                    <span>{{ $permission->display_name ?? $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">No hay permisos definidos aún.</p>
                @endforelse
            </div>


            <!-- Botón Guardar -->
            <div class="flex justify-end">
                <x-button type="submit" class="w-full md:w-auto">
                    <i class="fa-regular fa-floppy-disk mr-2"></i> Crear Usuario
                </x-button>
            </div>
        </form>
    </div>

    <livewire:admin.show-role-permissions-modal />


</x-layouts.app>
