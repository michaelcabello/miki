{{-- LIVEWIRE: admin.shared.ubigeo-select --}}
@php
    $controlBase = "w-full h-12 px-4 rounded-xl border border-gray-300 dark:border-gray-600
        bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
        focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition";
@endphp

<div class="grid grid-cols-1 md:grid-cols-3 gap-5">

    {{-- Department --}}
    <div class="space-y-2">
        <x-label for="department_id">Departamento</x-label>

        <select wire:model.live="department_id" class="{{ $controlBase }}">
            <option value="">— Seleccionar —</option>
            @foreach($this->departments as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
            @endforeach
        </select>

        {{-- hidden para POST normal --}}
        <input type="hidden" name="department_id" value="{{ $department_id }}">
    </div>

    {{-- Province --}}
    <div class="space-y-2">
        <x-label for="province_id">Provincia</x-label>

        <select wire:model.live="province_id" @disabled(!$department_id) class="{{ $controlBase }}">
            <option value="">— Seleccionar —</option>
            @foreach($this->provinces as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
            @endforeach
        </select>

        <input type="hidden" name="province_id" value="{{ $province_id }}">
    </div>

    {{-- District --}}
    <div class="space-y-2">
        <x-label for="district_id">Distrito</x-label>

        <select wire:model.live="district_id" @disabled(!$province_id) class="{{ $controlBase }}">
            <option value="">— Seleccionar —</option>
            @foreach($this->districts as $di)
                <option value="{{ $di->id }}">{{ $di->name }}</option>
            @endforeach
        </select>

        <input type="hidden" name="district_id" value="{{ $district_id }}">
    </div>

</div>
