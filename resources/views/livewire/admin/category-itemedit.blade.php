{{-- <li class="ml-{{ $category->parent_id ? 6 : 0 }}">
    <div class="flex items-center justify-between">
        <span class="{{ $selectedParent == $category->id ? 'font-bold text-green-600' : '' }}">
            {{ $category->name }}
            @if ($selectedParent == $category->id)
                (Papá actual)
            @endif
        </span>
        <a href="{{ route('category.edit', $category->id) }}" class="text-blue-600 hover:underline text-sm">
            Editar
        </a>
    </div>

    @if ($category->children->count())
        <ul class="ml-6 list-disc text-gray-600 dark:text-gray-300">
            @foreach ($category->children as $child)
                @livewire('admin.category-itemedit', ['category' => $child, 'selectedParent' => $selectedParent], key($child->id))
            @endforeach
        </ul>
    @endif
</li> --}}

{{-- <li
  class="p-2 rounded odd:bg-gray-50 even:bg-white dark:odd:bg-gray-700/50 dark:even:bg-gray-800"
  style="margin-left: {{ $depth * 16 }}px"
>
  <div class="flex items-center justify-between">
    <span class="{{ $selectedParent == $category->id ? 'font-bold text-green-600' : '' }}">
      {{ $category->name }}
      @if ($selectedParent == $category->id)
        <span class="text-xs text-green-500">(Papá actual)</span>
      @endif
    </span>

    <a href="{{ route('category.edit', $category->id) }}"
       class="text-blue-600 hover:underline text-sm">
      ✏️ Editar
    </a>
  </div>

  @if ($category->children->count())
    <ul class="mt-1 border-l border-gray-300 dark:border-gray-600 pl-3">
      @foreach ($category->children as $child)
        @livewire('admin.category-itemedit', ['category' => $child, 'selectedParent' => $selectedParent], key($child->id))
      @endforeach
    </ul>
  @endif
</li> --}}

{{-- <li
  class="p-2 rounded bg-white dark:bg-gray-800"
  style="margin-left: {{ $depth * 16 }}px"
>
  <div class="flex items-center justify-between">
    <div class="flex items-center gap-2">

      <span class="text-gray-500">
        @if ($category->children->count())
          📂
        @else
          📄
        @endif
      </span>

      <span class="{{ $selectedParent == $category->id ? 'font-bold text-green-600' : '' }}">
        {{ $category->name }}
        @if ($selectedParent == $category->id)
          <span class="text-xs text-green-500">(Papá actual)</span>
        @endif
      </span>
    </div>

    <a href="{{ route('category.edit', $category->id) }}"
       class="text-blue-600 hover:underline text-sm">
      ✏️ Editar
    </a>
  </div>


  @if ($category->children->count())
    <ul class="mt-1 border-l border-gray-300 dark:border-gray-600 pl-3">
      @foreach ($category->children as $child)
        @livewire('admin.category-itemedit', [
          'category' => $child,
          'selectedParent' => $selectedParent,
          'depth' => $depth + 1
        ], key($child->id))
      @endforeach
    </ul>
  @endif
</li> --}}

{{-- <li
    class="ml-{{ $depth * 4 }} p-2 rounded-lg border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
    <div x-data="{ open: false }" class="flex items-center justify-between">
        <div class="flex items-center gap-2">

            @if ($category->children->count())
                <button @click="open = !open" type="button" class="w-5 h-5 text-gray-500 hover:text-gray-700">
                    <span x-show="!open">➕</span>
                    <span x-show="open">➖</span>
                </button>
            @else
                <span class="w-5"></span>
            @endif

            <span
                class="{{ $selectedParent == $category->id ? 'font-bold text-green-600' : 'text-gray-800 dark:text-gray-200' }}">
                {{ $category->name }}
                @if ($selectedParent == $category->id)
                    <span class="text-xs text-green-500">(Papá actual)</span>
                @endif
            </span>
        </div>

        <a href="{{ route('category.edit', $category->id) }}" class="text-blue-600 hover:underline text-sm">
            ✏️ Editar
        </a>
    </div>


    @if ($category->children->count())
        <ul x-show="open" x-transition class="ml-6 mt-1 space-y-1 border-l border-gray-300 dark:border-gray-600 pl-3">
            @foreach ($category->children as $child)
                @livewire(
                    'admin.category-itemedit',
                    [
                        'category' => $child,
                        'selectedParent' => $selectedParent,
                        'depth' => $depth + 1,
                    ],
                    key($child->id)
                )
            @endforeach
        </ul>

    @endif
</li> --}}

{{-- <li x-data="{ open: true }" class="ml-{{ $depth * 4 }} p-2">
  <div class="flex items-center justify-between">
    <div class="flex items-center space-x-2">
      @if ($category->children->count())
        <button @click="open = !open" class="text-gray-500 focus:outline-none">
          <span x-show="!open">➕</span>
          <span x-show="open">➖</span>
        </button>
      @endif

      <span class="{{ $selectedParent == $category->id ? 'font-bold text-green-600' : '' }}">
        {{ $category->name }}
      </span>
    </div>
    <a href="{{ route('category.edit', $category->id) }}"
       class="text-blue-600 hover:underline text-sm">✏️ Editar</a>
  </div>

  @if ($category->children->count())
    <ul x-show="open" x-transition
        class="ml-6 mt-1 space-y-1 border-l border-gray-300 dark:border-gray-600 pl-3">
      @foreach ($category->children as $child)
        @livewire('admin.category-itemedit', [
          'category' => $child,
          'selectedParent' => $selectedParent,
          'depth' => $depth + 1
        ], key($child->id))
      @endforeach
    </ul>
  @endif
</li> --}}

<li class="p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700/50"
    x-data="{ open: true }">

    <div class="flex items-center justify-between cursor-pointer"
         @click="open = !open">

        <div class="flex items-center space-x-2">
            <!-- Flecha con animación -->
            @if ($category->children->count())
                <i class="fas fa-chevron-right text-gray-500 transition-transform duration-300"
                   :class="{ 'rotate-90': open }"></i>
            @else
                <i class="fas fa-circle text-[6px] text-gray-400"></i>
            @endif

            <span class="{{ $selectedParent == $category->id ? 'font-bold text-green-600' : '' }}">
                {{ $category->name }}
                @if ($selectedParent == $category->id)
                    <span class="text-xs text-green-500">(Papá actual)</span>
                @endif
            </span>
        </div>

        <a href="{{ route('category.edit', $category->id) }}"
           class="text-blue-600 hover:underline text-sm">
           ✏️ Editar
        </a>
    </div>

    <!-- Hijos -->
    @if ($category->children->count())
        <ul class="mt-1 ml-6 border-l border-gray-300 dark:border-gray-600 pl-3 space-y-1"
            x-show="open"
            x-transition>
            @foreach ($category->children as $child)
                @livewire('admin.category-itemedit', ['category' => $child, 'selectedParent' => $selectedParent], key($child->id))
            @endforeach
        </ul>
    @endif
</li>

