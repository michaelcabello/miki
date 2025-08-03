<div>
    @if ($open)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 max-w-2xl w-full">
                <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">
                    Permisos del rol: {{ $role->display_name }}
                </h2>

                <ul
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 text-sm text-gray-700 dark:text-gray-200">
                    @foreach ($permissions as $perm)
                        <li><i class="fa-solid fa-check-circle text-green-500 mr-1"></i> {{ $perm->display_name }}</li>
                    @endforeach
                </ul>

                <div class="mt-4 text-right">
                    <button wire:click="close"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Cerrar</button>
                </div>
            </div>
        </div>
    @endif
</div>
