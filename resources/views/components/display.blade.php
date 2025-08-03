@props(['label', 'value'])

<div>
    <x-label :value="$label" />
    <input type="text" readonly
        class="w-full mt-1 text-sm text-gray-900 bg-gray-100 dark:bg-gray-800 border border-gray-300 rounded-lg px-3 py-2"
        value="{{ $value }}">
</div>
