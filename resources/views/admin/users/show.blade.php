<x-layouts.app :title="__('Detalle del Usuario')">
    <div class="flex flex-col gap-6">
        <!-- Breadcrumb -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-4">
            <x-breadcrumb :links="[
                'Dashboard' => route('dashboard'),
                'Usuarios' => route('admin.users.index'),
                'Detalle del Usuario' => '#',
            ]" />
        </div>

        <!-- Encabezado -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                <i class="fa-solid fa-user text-primary"></i> Detalle del Usuario: {{ $user->name }}
            </h1>
        </div>

        <!-- Información principal del usuario -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-6 space-y-6">
            <!-- Foto, nombre y email -->
            <div class="flex items-center gap-4">
                <img src="{{ $user->employee->photo ? asset('storage/' . $user->employee->photo) : asset('img/default-user.png') }}"
                    alt="Foto del usuario" class="w-24 h-24 rounded-full border border-gray-300 object-cover shadow" />
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                </div>
            </div>

            <!-- Datos personales -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                <div>
                    <x-label for="address">Dirección</x-label>
                    <x-input type="text" value="{{ $user->employee->address }}" disabled
                        class="w-full bg-gray-100 dark:bg-gray-800" />
                </div>

                <div>
                    <x-label for="movil">Celular</x-label>
                    <x-input type="text" value="{{ $user->employee->movil }}" disabled
                        class="w-full bg-gray-100 dark:bg-gray-800" />
                </div>
                <div>
                    <x-label for="dni">DNI</x-label>
                    <x-input type="text" value="{{ $user->employee->dni }}" disabled
                        class="w-full bg-gray-100 dark:bg-gray-800" />
                </div>
                <div>
                    <x-label for="name">Cargo</x-label>
                    <x-input type="text" value="{{ $user->employee->position->name ?? '—' }}" disabled
                        class="w-full bg-gray-100 dark:bg-gray-800" />
                </div>
                <div>
                    <x-label for="gender">Genero</x-label>
                    <x-input type="text" value="{{ $user->employee->gender == 1 ? 'Masculino' : 'Femenino' }}"
                        disabled class="w-full bg-gray-100 dark:bg-gray-800" />
                </div>
                <div>
                    <x-label for="local">Local</x-label>
                    <x-input type="text" value="{{ $user->employee->local->name ?? '—' }}"
                        disabled class="w-full bg-gray-100 dark:bg-gray-800" />
                </div>

                <div>
                    <x-label for="state">Estado</x-label>
                    <x-input type="text" value="{{ $user->employee->state ? 'Activo' : 'Inactivo' }}"
                        disabled class="w-full bg-gray-100 dark:bg-gray-800" />
                </div>

            </div>
        </div>

        <!-- Roles asignados -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Roles Asignados</h3>

            @forelse ($user->roles as $role)
                <span
                    class="inline-flex items-center px-3 py-1 mr-2 mb-2 text-sm font-medium bg-indigo-100 text-indigo-800 rounded-full dark:bg-indigo-800 dark:text-white">
                    <i class="fa-solid fa-shield-halved mr-1"></i> {{ $role->display_name }}
                </span>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">Este usuario no tiene roles asignados.</p>
            @endforelse
        </div>

        <!-- Permisos asignados -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Permisos Directos</h3>

            @php
                $permissions = $user->getDirectPermissions()->groupBy('model_name');
            @endphp

            @forelse ($permissions as $module => $modulePermissions)
                <div
                    class="mb-6 border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-gray-50 dark:bg-gray-800">
                    <h4 class="text-base font-semibold text-gray-800 dark:text-white mb-4">{{ ucfirst($module) }}</h4>

                    <ul class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2">
                        @foreach ($modulePermissions as $permission)
                            <li class="text-sm text-gray-700 dark:text-gray-300">
                                <i class="fa-solid fa-check-circle text-green-500 mr-1"></i>
                                {{ $permission->display_name ?? $permission->name }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">No hay permisos directos asignados.</p>
            @endforelse
        </div>

        <!-- Botón de regreso -->
        <div class="flex justify-end">
            <a href="{{ route('admin.users.index') }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                <i class="fa-solid fa-arrow-left mr-2"></i> Volver a la lista
            </a>
        </div>
    </div>
</x-layouts.app>
