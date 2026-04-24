<div class="space-y-6">
    {{-- Breadcrumb --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => route('dashboard'),
            'Journal Types' => route('admin.journaltypesdos.index'),
            'Crear' => '#',
        ]" />
    </div>

    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Crear tipo de diario</h1>
            <p class="text-sm text-gray-500 dark:text-gray-300">Define las categorías contables de tus operaciones.</p>
        </div>
        <a href="{{ route('admin.journaltypes.index') }}" class="px-5 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold hover:bg-gray-200 transition">
            <i class="fa-solid fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    {{-- Card del Formulario --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        {{-- Tabs --}}
        <div class="px-6 pt-5 border-b border-gray-200 dark:border-gray-700">
            <button wire:click="setTab('general')" class="px-4 py-2 rounded-t-xl text-sm font-semibold {{ $tab === 'general' ? 'bg-indigo-600 text-white' : 'text-gray-500' }}">
                <i class="fa-regular fa-file-lines mr-2"></i> Datos Generales
            </button>
        </div>

        <div class="p-6">
            @include('livewire.admin.journaltype.partials.form-fields')

            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                <button wire:click="save" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-sky-600 text-white font-bold shadow-lg hover:scale-105 transition-all">
                    <i class="fa-regular fa-floppy-disk"></i>
                    <span wire:loading.remove>Guardar Registro</span>
                    <span wire:loading>Procesando...</span>
                </button>
            </div>
        </div>
    </div>
</div>
