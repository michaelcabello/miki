@foreach ($children as $child)
    <option value="{{ $child->id }}">
        {{ $prefix }} {{ $child->name }}
    </option>
    @if ($child->children->count())
        @include('livewire.admin.partials.category-options', ['children' => $child->children, 'prefix' => $prefix . '--'])
    @endif
@endforeach
