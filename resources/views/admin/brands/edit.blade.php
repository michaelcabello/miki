<x-layouts.app :title="__('Editar Marca')">
    <div class="flex flex-col gap-6">

        <!-- Breadcrumb -->
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 px-6 py-4">
            <x-breadcrumb :links="[
                'Dashboard' => route('dashboard'),
                'Marcas' => route('admin.brands.index'),
                'Crear Marca' => '#',
            ]" />
        </div>

        <!-- Header (estilo attribute) -->
        <div
            class="relative overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
            <div
                class="absolute inset-0 opacity-60 pointer-events-none
                       bg-gradient-to-r from-indigo-50 via-white to-sky-50
                       dark:from-indigo-950/30 dark:via-gray-900 dark:to-sky-950/30">
            </div>

            <div class="relative p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div
                        class="shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center
                                bg-indigo-600 text-white shadow-md shadow-indigo-600/20">
                        <i class="fa-solid fa-copyright text-lg"></i>
                    </div>

                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            Editar marca
                        </h1>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                            Modificando una marca como <span class="font-medium">Nike</span>, <span
                                class="font-medium">Adidas</span>, etc.
                        </p>
                    </div>
                </div>

                <a href="{{ route('admin.brands.index') }}"
                    class="inline-flex items-center justify-center rounded-xl px-4 py-2
                          border border-gray-200 dark:border-gray-600
                          bg-white/80 dark:bg-gray-800/60
                          text-gray-700 dark:text-gray-200
                          hover:bg-white dark:hover:bg-gray-800
                          transition">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Volver
                </a>
            </div>
        </div>

        <!-- Formulario -->
        <form method="POST" action="{{ route('admin.brands.update', $brand) }}" enctype="multipart/form-data"
            class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Datos de la marca</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Completa los campos. Los que tienen <span class="text-red-500 font-semibold">*</span>
                                son obligatorios.
                            </p>
                        </div>


                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Columna izquierda -->
                        <div class="space-y-5">
                            <!-- Nombre -->
                            <div class="space-y-2">
                                <x-label for="name">
                                    Nombre de la marca <span class="text-red-500">*</span>
                                </x-label>

                                <div
                                    class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40
                                            focus-within:ring-2 focus-within:ring-indigo-500/40 focus-within:border-indigo-400 transition">
                                    <div class="p-1">
                                        <flux:input icon="tag" id="name" name="name"
                                            value="{{ old('name', $brand->name) }}" placeholder="Ej: Nike, Adidas, Puma"
                                            class="!bg-transparent !border-0 !shadow-none !ring-0" />
                                    </div>
                                </div>
                                <flux:error name="name" />
                            </div>



                            <!-- Orden -->
                            <div class="space-y-2">
                                <x-label for="order">Orden</x-label>

                                <div
                                    class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40
                                            focus-within:ring-2 focus-within:ring-indigo-500/40 focus-within:border-indigo-400 transition">
                                    <div class="p-1">
                                        <flux:input type="number" id="order" name="order" min="0"
                                            step="1" inputmode="numeric" pattern="[0-9]*" onwheel="this.blur()"
                                            value="{{ old('order', $brand->order ?? 0) }}" placeholder="Ej: 0" />
                                    </div>
                                </div>
                                <flux:error name="order" />
                            </div>

                            <!-- Estado -->
                            <div
                                class="flex items-center justify-between p-4 rounded-2xl
                                        border border-gray-200 dark:border-gray-700
                                        bg-gradient-to-r from-gray-50 via-white to-gray-50
                                        dark:from-gray-900/50 dark:via-gray-800 dark:to-gray-900/50">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-10 h-10 rounded-2xl flex items-center justify-center
                                                bg-emerald-600 text-white shadow-sm shadow-emerald-600/20">
                                        <i class="fa-solid fa-toggle-on"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">Estado</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">
                                            Activa la marca para que esté disponible en productos.
                                        </p>
                                    </div>
                                </div>

                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="state" value="1" class="sr-only peer"
                                        {{ old('state', $brand->state) ? 'checked' : '' }}>
                                    <div
                                        class="relative w-12 h-7 bg-gray-300 dark:bg-gray-600 rounded-full
                                                peer peer-checked:bg-emerald-500 transition">
                                        <div
                                            class="absolute top-1 left-1 w-5 h-5 bg-white rounded-full
                                                    peer-checked:translate-x-5 transition">
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Imagen -->

                            @if ($brand->image)
                                <div>
                                    <x-label>Imagen actual:</x-label>
                                    <div class="mt-2 relative inline-block">
                                        <img src="{{ Storage::disk('s3')->url($brand->image) }}" alt="{{ $brand->name }}"
                                            class="max-h-48 rounded-lg shadow-md" id="currentImage">
                                        {{-- <button type="button" onclick="removeCurrentImage()"
                                            class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white rounded-full p-2 shadow-lg"
                                            title="Eliminar imagen actual">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button> --}}
                                    </div>
                                    <input type="hidden" name="remove_image" id="remove_image" value="0">
                                </div>
                            @endif



                            <div class="space-y-2">
                                <x-label for="image">Imagen (opcional)</x-label>

                                <input type="file" name="image" id="image"
                                    accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                    class="w-full text-sm text-gray-500
                                           file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold
                                           file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100
                                           dark:file:bg-gray-700 dark:file:text-gray-300"
                                    onchange="previewImage(event)">

                                @error('image')
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror

                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Formatos: JPEG, PNG, JPG, GIF, WEBP. Máximo 2MB.
                                </p>

                                <div id="imagePreview" class="mt-3 hidden">
                                    <img id="preview" src="" alt="Preview"
                                        class="max-h-48 rounded-xl shadow-md">
                                </div>
                            </div>
                        </div>

                        <!-- Columna derecha (SEO) -->
                        <div class="space-y-5">
                            {{-- <div class="flex items-center justify-between">
                                <h4 class="text-base font-semibold text-gray-900 dark:text-white">
                                    <i class="fa-solid fa-search mr-2 text-emerald-600"></i> SEO (opcional)
                                </h4>
                            </div> --}}

                            <!-- Título SEO -->
                            <div class="space-y-2">
                                <x-label for="title">Título SEO</x-label>

                                <div
                                    class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40
                                            focus-within:ring-2 focus-within:ring-indigo-500/40 focus-within:border-indigo-400 transition">
                                    <div class="p-1">
                                        <flux:input id="title" name="title" value="{{ old('title', $brand->title) }}"
                                            maxlength="255" placeholder="Título para motores de búsqueda" />
                                    </div>
                                </div>
                                <flux:error name="title" />
                                <p class="text-xs text-gray-500 dark:text-gray-400">Máximo 255 caracteres</p>
                            </div>

                            <!-- Descripción SEO -->
                            <div class="space-y-2">
                                <x-label for="description">Descripción SEO</x-label>
                                <textarea name="description" id="description" rows="5"
                                    class="w-full rounded-2xl border border-gray-200 dark:border-gray-700
                                           bg-gray-50 dark:bg-gray-900/40 text-gray-800 dark:text-gray-200
                                           focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 transition p-3"
                                    placeholder="Descripción breve de la marca para SEO">{{ old('description', $brand->description) }}</textarea>
                                @error('description')
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Keywords -->
                            <div class="space-y-2">
                                <x-label for="keywords">Keywords</x-label>
                                <textarea name="keywords" id="keywords" rows="4"
                                    class="w-full rounded-2xl border border-gray-200 dark:border-gray-700
                                           bg-gray-50 dark:bg-gray-900/40 text-gray-800 dark:text-gray-200
                                           focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 transition p-3"
                                    placeholder="palabra1, palabra2, palabra3...">{{ old('keywords', $brand->keywords) }}</textarea>
                                @error('keywords')
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 dark:text-gray-400">Separa con comas</p>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Footer actions -->
                <div
                    class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-900/30 rounded-b-2xl">
                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Puedes cargar la imagen ahora o luego editar la marca.
                        </p>

                        <div class="flex gap-2">
                            <a href="{{ route('admin.brands.index') }}"
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
                                       focus:outline-none focus:ring-4 focus:ring-indigo-500/30
                                       transition">
                                <i class="fa-regular fa-floppy-disk mr-2"></i>
                                Actualizar marca
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

     @push('scripts')
        <script>
            // Preview de nueva imagen
            function previewImage(event) {
                const file = event.target.files[0];
                const preview = document.getElementById('preview');
                const previewContainer = document.getElementById('imagePreview');

                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                    }

                    reader.readAsDataURL(file);
                } else {
                    previewContainer.classList.add('hidden');
                }
            }

            // Eliminar imagen actual
           /*  function removeCurrentImage() {
                Swal.fire({
                    title: '¿Eliminar imagen?',
                    text: "La imagen actual será eliminada",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('currentImage').style.display = 'none';
                        document.getElementById('remove_image').value = '1';

                        Swal.fire(
                            'Marcada para eliminación',
                            'La imagen se eliminará al guardar los cambios',
                            'success'
                        );
                    }
                });
            } */

            // Auto-generar slug desde el nombre (solo si el slug está vacío)
            document.getElementById('name').addEventListener('input', function(e) {
                const slugInput = document.getElementById('slug');
                if (!slugInput.value || slugInput.dataset.autogenerated) {
                    const slug = e.target.value
                        .toLowerCase()
                        .normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '')
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-|-$/g, '');

                    slugInput.value = slug;
                    slugInput.dataset.autogenerated = 'true';
                }
            });

            // Marcar cuando el usuario edita manualmente el slug
            document.getElementById('slug').addEventListener('input', function() {
                if (this.value) {
                    delete this.dataset.autogenerated;
                }
            });
        </script>
    @endpush

</x-layouts.app>
