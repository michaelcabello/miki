<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                Imágenes de: <span class="text-blue-600">{{ $productTemplate->name }}</span>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Administra las imágenes por variante manteniendo una estructura uniforme en el catálogo.
            </p>
        </div>

        <div>
            <a href="{{ route('admin.products.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-600">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Volver
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-wrap gap-4 justify-between items-center">
        <div class="relative w-full md:w-1/3">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por SKU o nombre..."
                class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring focus:ring-blue-500">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fa-solid fa-search"></i>
            </span>
        </div>

        <div>
            <select wire:model.live="perPage"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                <option value="10">10 registros</option>
                <option value="25">25 registros</option>
                <option value="50">50 registros</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-7">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="px-4 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                        Variantes del producto
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Selecciona una variante para administrar sus imágenes.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th wire:click="sortBy('sku')"
                                    class="px-4 py-3 text-left cursor-pointer text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">
                                    SKU
                                    @if ($sortField === 'sku')
                                        <i class="fa-solid {{ $sortDirection === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                    @endif
                                </th>

                                <th wire:click="sortBy('name')"
                                    class="px-4 py-3 text-left cursor-pointer text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">
                                    Nombre
                                    @if ($sortField === 'name')
                                        <i class="fa-solid {{ $sortDirection === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                    @endif
                                </th>

                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">
                                    Tipo
                                </th>

                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">
                                    Imágenes
                                </th>

                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">
                                    Acción
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($variants as $variant)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ $selectedVariant?->id === $variant->id ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                                        {{ $variant->sku ?? '—' }}
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                                        {{ $variant->name ?? 'Sin nombre' }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        @if ((bool) data_get($variant, 'is_default'))
                                            <span class="px-2 py-1 bg-indigo-100 text-indigo-700 text-xs rounded-full">
                                                Default
                                            </span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full dark:bg-gray-700 dark:text-gray-300">
                                                Variante
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">
                                            {{ $variant->images_count }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <button wire:click="selectVariant({{ $variant->id }})"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold transition
                                                {{ $selectedVariant?->id === $variant->id
                                                    ? 'bg-blue-600 text-white hover:bg-blue-700'
                                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600' }}">
                                            <i class="fa-solid fa-images mr-2"></i>
                                            Gestionar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Este producto no tiene variantes registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $variants->links() }}
                </div>
            </div>
        </div>

        <div class="xl:col-span-5">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden h-full">
                <div class="px-4 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                        Panel de imágenes
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Carga varias imágenes, arrástralas y define cuál será la portada.
                    </p>
                </div>

                @if ($selectedVariant)
                    <div class="p-4 space-y-4">
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900/30">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $selectedVariant->name ?? 'Sin nombre' }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        SKU: {{ $selectedVariant->sku ?? '—' }}
                                    </p>
                                </div>

                                @if ((bool) data_get($selectedVariant, 'is_default'))
                                    <span class="px-2 py-1 bg-indigo-100 text-indigo-700 text-xs rounded-full">
                                        Variante base
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-800">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                        Subida de imágenes
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Arrastra, suelta o selecciona varias imágenes para esta variante.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    onclick="window.dispatchEvent(new CustomEvent('upload-images-to-livewire'))"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                                    <i class="fa-solid fa-cloud-arrow-up mr-2"></i>
                                    Guardar imágenes
                                </button>
                            </div>

                            <div wire:ignore x-data="imageUploader(@this)" x-init="init()" class="space-y-3">
                                <input type="file" x-ref="input" multiple>

                                @error('newImages')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                @error('newImages.*')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div wire:loading.flex wire:target="newImages,saveImages" class="items-center gap-2 text-sm text-blue-600 mt-3">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                Procesando imágenes...
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                        Galería actual
                                    </h5>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Arrastra las tarjetas para definir el orden visual.
                                    </p>
                                </div>
                            </div>

                            @if ($selectedVariant->images->count())
                                <div x-data="sortableGallery(@this)" x-init="init()" x-ref="sortableContainer" class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    @foreach ($selectedVariant->images as $image)
                                        <div class="group relative rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/20"
                                            data-id="{{ $image->id }}">
                                            <a href="{{ $image->original_url }}" target="_blank" class="block">
                                                <img
                                                    src="{{ $image->thumb_url }}"
                                                    alt="{{ $image->original_name }}"
                                                    class="w-full h-36 object-cover"
                                                    loading="lazy"
                                                    decoding="async"
                                                >
                                            </a>

                                            <div class="p-3 space-y-2">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="text-[11px] text-gray-500 dark:text-gray-400 truncate">
                                                        {{ $image->original_name }}
                                                    </span>

                                                    @if ($image->is_primary)
                                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-green-100 text-green-700 font-semibold">
                                                            Portada
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="text-[11px] text-gray-400">
                                                    {{ $image->width }}×{{ $image->height }} · {{ $image->human_size }}
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    @if (!$image->is_primary)
                                                        <button type="button"
                                                            wire:click="setPrimaryImage({{ $image->id }})"
                                                            class="flex-1 inline-flex justify-center items-center px-2 py-1.5 rounded-lg bg-indigo-100 text-indigo-700 text-xs font-semibold hover:bg-indigo-200 transition">
                                                            <i class="fa-solid fa-star mr-1"></i>
                                                            Portada
                                                        </button>
                                                    @endif

                                                    <button type="button"
                                                        wire:click="deleteImage({{ $image->id }})"
                                                        class="inline-flex justify-center items-center px-2 py-1.5 rounded-lg bg-red-100 text-red-700 text-xs font-semibold hover:bg-red-200 transition">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="absolute top-2 left-2">
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white/90 text-gray-700 shadow cursor-move">
                                                    <i class="fa-solid fa-grip-vertical"></i>
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 p-8 text-center bg-gray-50 dark:bg-gray-900/20">
                                    <div class="mx-auto w-14 h-14 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mb-4">
                                        <i class="fa-solid fa-image text-xl"></i>
                                    </div>
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                        Aún no hay imágenes
                                    </h4>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        Sube una o varias imágenes para comenzar a construir la galería de esta variante.
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                            <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">
                                Resumen de la selección
                            </h5>

                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                <div class="rounded-lg bg-gray-50 dark:bg-gray-700/50 p-3">
                                    <dt class="text-gray-500 dark:text-gray-400">Producto</dt>
                                    <dd class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $productTemplate->name }}
                                    </dd>
                                </div>

                                <div class="rounded-lg bg-gray-50 dark:bg-gray-700/50 p-3">
                                    <dt class="text-gray-500 dark:text-gray-400">Variante</dt>
                                    <dd class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $selectedVariant->name ?? '—' }}
                                    </dd>
                                </div>

                                <div class="rounded-lg bg-gray-50 dark:bg-gray-700/50 p-3">
                                    <dt class="text-gray-500 dark:text-gray-400">SKU</dt>
                                    <dd class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $selectedVariant->sku ?? '—' }}
                                    </dd>
                                </div>

                                <div class="rounded-lg bg-gray-50 dark:bg-gray-700/50 p-3">
                                    <dt class="text-gray-500 dark:text-gray-400">Imágenes</dt>
                                    <dd class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $selectedVariant->images->count() }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                @else
                    <div class="p-8 text-center">
                        <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center mb-4 dark:bg-gray-700 dark:text-gray-300">
                            <i class="fa-solid fa-image text-2xl"></i>
                        </div>

                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">
                            No hay variante seleccionada
                        </h3>

                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Selecciona una variante desde la tabla para comenzar a administrar sus imágenes.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>

    <script>
        function imageUploader($wire) {
            return {
                pond: null,
                pendingFiles: [],

                init() {
                    FilePond.registerPlugin(
                        FilePondPluginImagePreview,
                        FilePondPluginFileValidateType,
                        FilePondPluginFileValidateSize
                    );

                    this.pond = FilePond.create(this.$refs.input, {
                        allowMultiple: true,
                        allowReorder: true,
                        acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp'],
                        maxFileSize: '5MB',
                        storeAsFile: true,
                        instantUpload: false,
                        labelIdle: `
                            <div class="py-6">
                                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                    <i class="fa-solid fa-cloud-arrow-up text-xl"></i>
                                </div>
                                <p class="text-sm font-semibold text-gray-700">Arrastra imágenes aquí</p>
                                <p class="text-xs text-gray-500">o <span class="text-blue-600 font-semibold">haz clic para seleccionarlas</span></p>
                                <p class="mt-2 text-xs text-gray-400">JPG, PNG, WEBP · máximo 5 MB por imagen</p>
                            </div>
                        `,
                    });

                    this.pond.on('updatefiles', (items) => {
                        this.pendingFiles = items.map(item => item.file);
                    });

                    window.addEventListener('upload-images-to-livewire', () => {
                        if (!this.pendingFiles.length) {
                            return;
                        }

                        $wire.$uploadMultiple(
                            'newImages',
                            this.pendingFiles,
                            () => {
                                $wire.call('saveImages');
                            },
                            (error) => {
                                console.error('Error al subir archivos temporales a Livewire:', error);
                            },
                            () => {}
                        );
                    });

                    Livewire.on('notify', () => {
                        this.pendingFiles = [];
                        this.pond.removeFiles();
                    });
                }
            }
        }

        function sortableGallery($wire) {
            return {
                init() {
                    Sortable.create(this.$refs.sortableContainer, {
                        animation: 200,
                        handle: '.cursor-move',
                        onEnd: () => {
                            const ids = Array.from(this.$refs.sortableContainer.children)
                                .map(item => Number(item.dataset.id));

                            $wire.call('updateImageOrder', ids);
                        }
                    });
                }
            }
        }
    </script>
@endpush
