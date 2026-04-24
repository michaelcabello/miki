@props(['routeName', 'templateRoute', 'columns'])

<div x-data="{ show: false, fileName: '' }"
     x-on:open-import.window="show = true"
     x-on:close.window="show = false"
     x-on:keydown.escape.window="show = false"
     x-show="show"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;"
     x-cloak>

    {{-- Fondo con Blur --}}
    <div x-show="show" x-transition.opacity class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 backdrop-blur-sm"></div>

    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div x-show="show" x-transition.scale.95 class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-2xl max-w-lg w-full">

            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                        <i class="fa-solid fa-file-csv text-indigo-600"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">Importador de Datos</h2>
                </div>
                <button @click="show = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form action="{{ $routeName }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6">
                    <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-100 dark:border-amber-800 flex gap-3">
                        <i class="fa-solid fa-circle-exclamation text-amber-600 mt-1"></i>
                        <div>
                            <p class="text-xs text-amber-800 dark:text-amber-300 font-semibold mb-1 uppercase tracking-wider">Columnas Requeridas:</p>
                            <p class="text-xs text-amber-700 dark:text-amber-400 font-mono">{{ $columns }}</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <a href="{{ $templateRoute }}" class="inline-flex items-center text-xs font-bold text-indigo-600 hover:underline">
                            <i class="fa-solid fa-download mr-2"></i> Descargar plantilla de ejemplo
                        </a>
                    </div>

                    <label class="relative group block w-full h-44 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-2xl hover:border-indigo-500 transition-all cursor-pointer bg-gray-50/50 dark:bg-gray-900/20">
                        <div class="flex flex-col items-center justify-center h-full text-center p-4">
                            <template x-if="!fileName">
                                <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 group-hover:text-indigo-500 mb-2 transition-colors"></i>
                            </template>
                            <template x-if="fileName">
                                <i class="fa-solid fa-file-circle-check text-3xl text-green-500 mb-2 animate-bounce"></i>
                            </template>
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300" x-text="fileName ? 'Archivo listo' : 'Selecciona tu Excel'"></p>
                            <p class="text-xs text-gray-500 mt-1" x-text="fileName ? fileName : 'o arrastra y suelta aquí'"></p>
                        </div>
                        <input type="file" name="file" class="hidden" required @change="fileName = $event.target.files[0].name">
                    </label>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                    <button type="button" @click="show = false" class="px-5 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 rounded-xl transition">Cancelar</button>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg transition flex items-center gap-2">
                        <i class="fa-solid fa-upload"></i> Procesar Importación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
