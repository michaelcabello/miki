
 <div x-data="{ open: {{ $isOpen ? 'true' : 'false' }} }">
    <div @click="open = !open" class="flex items-center cursor-pointer">
        <div style="margin-left: {{ $depth * 20 }}px" class="flex">
            <div class="mr-2" x-show="!open"><i class="fas fa-plus"></i></div>
            <div class="mr-2" x-show="open"><i class="fas fa-minus"></i></div>
        </div>
        <div>
            <input type="radio" name="category_radio"
                   id="category_radio_{{ $category->id }}"
                   wire:model="selectedParentCategory"
                   value="{{ $category->id }}">
            <label for="category_radio_{{ $category->id }}">
                {{ $category->name }}
            </label>
        </div>
    </div>

    <ul x-show="open" x-collapse>
        @foreach ($category->children as $child)
            <li>
                <livewire:admin.category-item :category="$child"
                    wire:model="selectedParentCategory"
                    :key="$child->id" />
            </li>
        @endforeach
    </ul>
</div>
