<div>


    <div class="px-6 py-2 bg-indigo-50 dark:bg-gray-700/30 border-b dark:border-gray-700">
        <label class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest">Cambiar Variante:</label>
        <div class="flex gap-2 overflow-x-auto pb-2 mt-1 scrollbar-hide">
            @foreach ($allVariants as $v)
                <button wire:click="switchVariant({{ $v->id }})"
                    class="flex-none px-3 py-1 rounded-lg text-xs font-medium transition
                {{ $variant->id === $v->id
                    ? 'bg-indigo-600 text-white shadow-md'
                    : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border dark:border-gray-600 hover:bg-gray-100' }}">
                    {{ $v->variant_name ?: 'Principal' }}
                </button>
            @endforeach
        </div>
    </div>



    {{--
       IMPORTANTE: Movemos el chequeo de @if ($isOpen) al principio de todo.
       Si el modal no está abierto, Livewire no intentará leer $variant,
       evitando el error de "property on null".
    --}}
    @if ($isOpen && $variant)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div
                class="bg-white dark:bg-gray-800 w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden animate-fade-in-up">

                <div class="px-6 py-5 border-b dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600">
                            <i class="fa-solid fa-box-open text-xl"></i>
                        </div>

                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-xl font-extrabold text-gray-800 dark:text-white tracking-tight">
                                    {{ $variant->template->name ?? 'Producto sin nombre' }}
                                </h3>
                                @if ($variant->sku)
                                    <span
                                        class="px-2 py-0.5 bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-[10px] font-mono rounded uppercase">
                                        {{ $variant->sku }}
                                    </span>
                                @endif
                            </div>

                            <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                @if ($variant->is_default)
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fa-solid fa-star text-amber-500 text-[10px]"></i>
                                        Producto Principal
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fa-solid fa-code-fork text-[10px]"></i>
                                        Variante: <b
                                            class="text-indigo-600 dark:text-indigo-400">{{ $variant->variant_name }}</b>
                                    </span>
                                @endif
                            </p>
                        </div>

                        <button wire:click="closeModal"
                            class="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-full transition-colors">
                            <i class="fa-solid fa-xmark text-gray-400"></i>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <div class="relative group">
                        <div
                            class="border-2 border-dashed border-indigo-200 dark:border-gray-600 rounded-xl p-8 text-center transition hover:border-indigo-400 hover:bg-indigo-50/50 dark:hover:bg-gray-700/50">
                            <input type="file" wire:model="newImages" multiple
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <div class="space-y-2">
                                <i class="fa-solid fa-cloud-arrow-up text-3xl text-indigo-500"></i>
                                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Arrastra imágenes aquí o
                                    <span class="text-indigo-600 font-bold underline">explora</span></p>
                                <p class="text-xs text-gray-400 uppercase tracking-widest">Máximo 2MB por imagen</p>
                            </div>
                        </div>
                    </div>

                    @if ($newImages)
                        <div class="mt-4 flex justify-end">
                            <button wire:click="saveImages" wire:loading.attr="disabled"
                                class="px-6 py-2 bg-indigo-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition flex items-center gap-2">
                                <span wire:loading.remove>Guardar {{ count($newImages) }} fotos</span>
                                <span wire:loading><i class="fa-solid fa-circle-notch animate-spin"></i>
                                    Procesando...</span>
                            </button>
                        </div>
                    @endif

                    <div class="mt-8">
                        <h4 class="text-xs font-bold text-gray-400 dark:text-gray-500 mb-4 uppercase tracking-widest">
                            Imágenes en Sistema</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            @forelse($variant->images as $img)
                                <div
                                    class="group relative rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-900 aspect-square border-2 {{ $img->is_main ? 'border-indigo-500' : 'border-transparent' }}">
                                    <img src="{{ asset('storage/' . $img->path) }}" class="w-full h-full object-cover">

                                    <div
                                        class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                        <button wire:click="setMain({{ $img->id }})"
                                            class="p-2 bg-white rounded-full text-indigo-600 hover:scale-110 transition shadow-lg">
                                            <i
                                                class="fa-solid {{ $img->is_main ? 'fa-star' : 'fa-star-half-stroke' }}"></i>
                                        </button>
                                        <button
                                            onclick="confirm('¿Eliminar esta imagen?') || event.stopImmediatePropagation()"
                                            wire:click="deleteImage({{ $img->id }})"
                                            class="p-2 bg-white rounded-full text-red-600 hover:scale-110 transition shadow-lg">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>

                                    @if ($img->is_main)
                                        <span
                                            class="absolute top-2 left-2 px-2 py-0.5 bg-indigo-500 text-[9px] text-white font-black rounded uppercase tracking-tighter shadow-sm">Main</span>
                                    @endif
                                </div>
                            @empty
                                <div
                                    class="col-span-full py-12 flex flex-col items-center justify-center border-2 border-dotted border-gray-200 dark:border-gray-700 rounded-2xl bg-gray-50/50">
                                    <i class="fa-solid fa-image text-4xl text-gray-300 mb-2"></i>
                                    <p class="text-gray-400 text-sm italic">Sin imágenes para esta variante</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
