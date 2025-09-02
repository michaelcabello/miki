<div x-data="{ open: {{ $isOpen ? 'true' : 'false' }} }" class="mt-2 mb-4">

    <div @mousedown="toggle({{ $category->id }})" class="flex items-center justify-between cursor-pointer">
        <div @click="open = !open" class="flex items-center cursor-pointer">
            <div style="margin-left: {{ $depth * 30 }}px" class="flex">
                <div class="mr-2" x-show="!open">
                    <i class="fas fa-plus"></i>
                </div>
                <div class="mr-2" x-show="open">
                    <i class="fas fa-minus"></i>
                </div>
                {{-- <div class="flex-shrink-0 h-10 ml-2 mr-2 w-15">
                    @if ($category->image)
                        <img class="object-cover w-20 h-10 rounded-sm" src="{{ url($category->image) }}"
                            alt="{{ $category->name }}">

                    @else
                        <img class="object-cover h-6 rounded-full w-15"
                            src="{{ asset('storage/brands/category-default.jpg') }}" alt="{{ $category->name }}">
                    @endif

                </div> --}}

                <div class="flex-shrink-0 h-10 ml-2 mr-2 w-15 ">
                    @if ($category->image)
                        <p>image</p>
                        {{-- <img class="object-cover w-20 h-10 rounded-sm"
                        src="{{ Storage::disk('s3')->url($category->image) }}" alt="{{ $category->name }}"> --}}
                        {{-- src="{{ url($category->image) }}" alt="{{ $category->name }}"> --}}
                        {{-- src="{{ Storage::url($brand->image) }}" storage//storage/brand/default.jpg  en la bd esta puesto esto 	/storage/brands/default.jpg > --}}
                        {{-- url($brand->image) muestra tal como es la ruta en la bd esta puesto esto 	/storage/brands/default.jpg --}}
                        {{--  {{ Storage::disk("s3")->url($brand->image) }} --}}
                    @else
                        <img class="object-cover h-6 rounded-full w-15" src="{{ asset('img/categorydefault.jpg') }}"
                            alt="{{ $category->name }}" class="m-2">
                    @endif
                </div>


                <div>
                    <span>{{ $category->name }} ({{ $category->children->count() }})
                        {{-- {{ $selectedParentCategory }} --}}</span>
                </div>



            </div>

        </div>


        <div class="px-6 text-sm font-medium text-right whitespace-nowrap flex items-center gap-2">
            <!-- State -->
            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Activo</span>


            <a href="{{ route('category.edit', $category->id) }}" class="btn btn-green">
                <i class="fa-solid fa-pen-to-square"></i>
            </a>

            {{--
                ! style="display:inline" para que los botones se vean uno al lado de otro
                ? <button type="button">
                ? Muy importante: no envía el formulario automáticamente (si fuera submit lo enviaría sin confirmar). Solo lanza el onclick.
            --}}


            <form id="delete-form-{{ $category->id }}" method="POST"
                action="{{ route('category.destroy', $category) }}" style="display:inline">
                @csrf
                @method('DELETE')

                <button type="button" onclick="confirmDelete({{ $category->id }}, '{{ $category->name }}')"
                    class="relative group inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-red-600 transition">
                    <i class="fa-solid fa-trash"></i>
                    <span
                        class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100">Eliminar</span>
                </button>
            </form>


        </div>
    </div>


    <hr class="w-full my-2 border-t border-gray-300 dotted" style="margin-left: {{ $depth * 30 }}px">

    <ul x-show="open" @click.away="open = true">
        @foreach ($category->children as $child)
            <li>
                <div style="margin-left: {{ ($depth + 1) * 20 }}px">
                    <livewire:admin.category-itemlist :category="$child" :selectedParentCategory="$selectedParentCategory" :key="$child->id" />

                </div>
            </li>
        @endforeach
    </ul>


    <script>

        /* Muestra un modal de confirmación con SweetAlert2. */
        function confirmDelete(id, name) {
            Swal.fire({
                title: '¿Eliminar categoría?',
                text: `La categoría "${name}" será eliminada.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        }
    </script>



</div>
