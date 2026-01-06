<x-layouts.app :title="__('Crear Usuario')">
    <div class="flex flex-col gap-6">
        <!-- Breadcrumb -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-4">
            <x-breadcrumb :links="[
                'Dashboard' => route('dashboard'),
                'Categorias' => route('admin.categoryposts.index'),
                'Crear Categoria del Post' => '#',
            ]" />
        </div>


        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100"><i
                        class="fa-solid fa-user-shield text-primary"></i> Creando Nueva Categoría del Post</h1>
                {{--   <p class="text-gray-500 text-sm">Administra cuentas y roles del sistema</p> --}}
            </div>

        </div>

        <!-- Formulario -->
        <form method="POST" action="{{ route('admin.categoryposts.store') }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <!-- Datos Personales -->
            {{-- <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 space-y-4"> --}}
            <div
                class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm p-6 space-y-4">

                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Datos del Usuario</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
                    <div>
                        <flux:input icon="user" label="Nombre categoría:" name="name" value="{{ old('name') }}"
                            placeholder="Nombre Categoría" />

                    </div>

                    <div>
                        <flux:textarea name="description" label="Descripción" placeholder="Escribe la descripción..." rows="3">
                            {{ old('description') }}
                        </flux:textarea>
                        <flux:error name="description" />

                    </div>

                    <div>
                        <flux:input icon="user" label="Titulo de Google:" name="titlegoogle" value="{{ old('titlegoogle') }}"
                            placeholder="Title para Google" />

                    </div>

                    <div>
                        <flux:textarea name="descriptiongoogle" label="Description Google" placeholder="descripción de google..." rows="3">
                            {{ old('descriptiongoogle') }}
                        </flux:textarea>
                        <flux:error name="descriptiongoogle" />

                    </div>

                    <div>
                        <flux:input icon="user" name="keywordsgoogle" label="Keywords Google:" value="{{ old('keywordsgoogle') }}"
                            placeholder="keywords google" />

                    </div>




                    <div class="flex items-center mt-2">
                        <input type="checkbox" name="state" id="state" value="1"
                            class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                            {{ old('state', 0) ? 'checked' : '' }}>
                        <label for="state" class="ml-2 text-sm text-gray-700 dark:text-gray-200">
                            Categoría Activa
                        </label>
                    </div>

                    <div>
                        <x-label for="name">Orden:</x-label>
                        <flux:input type="number" name="order" id="order" value="{{ old('order') }}"
                            placeholder="Orden" classInput="w-1/4 h-10" />
                    </div>


                    <div>
                        <flux:input type="file" name="image" label="Foto" />
                    </div>

                </div>
            </div>

            <!-- Botón Guardar -->
            <div class="flex justify-end">
                <x-button type="submit" class="w-full md:w-auto">
                    <i class="fa-regular fa-floppy-disk mr-2"></i> Crear Categoría del Post
                </x-button>
            </div>
        </form>
    </div>




</x-layouts.app>
