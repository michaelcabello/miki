<div x-data="{ open: @entangle($attributes->wire('model')) }"
     x-show="open"
     class="fixed inset-0 z-50 bg-black/10 backdrop-blur-sm"
     x-cloak>

    <div class="flex items-center justify-center min-h-screen p-4">
        <div @click.away="open = false"
             class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-lg p-6 z-50">

            <!-- Título -->
            @isset($title)
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
                    {{ $title }}
                </h2>
            @endisset

            <!-- Contenido -->
            <div class="mb-4">
                {{ $slot }}
            </div>

            <!-- Pie de página -->
            @isset($footer)
                <div class="mt-6 flex justify-end space-x-2">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>







{{-- <div x-data="{ open: @entangle($attributes->wire('model')) }"
     x-show="open"
     class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50"
     x-cloak>

    <div @click.away="open = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-lg p-6">


        @isset($title)
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
                {{ $title }}
            </h2>
        @endisset


        <div class="mb-4">
            {{ $slot }}
        </div>


        @isset($footer)
            <div class="mt-6 flex justify-end space-x-2">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div> --}}
