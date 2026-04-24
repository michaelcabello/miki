<div class="space-y-6">
    {{-- 1. Breadcrumb Dinámico --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow px-6 py-3">
        <x-breadcrumb :links="[
            'Dashboard' => route('dashboard'),
            'Journal Types' => route('admin.journaltypesdos.index'),
            'Editar' => '#',
        ]" />
    </div>

    {{-- 2. Header de Edición Profesional --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Editar tipo de diario</h1>
            <p class="text-sm text-gray-500 dark:text-gray-300">
                Editando: <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $jt->name }}</span>
                <span class="mx-2 text-gray-300">|</span>
                Código: <span class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded text-xs">{{ $jt->code }}</span>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.journaltypesdos.index') }}"
                class="px-5 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold hover:bg-gray-200 transition">
                <i class="fa-solid fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
    </div>

    {{-- 3. Cuerpo del Formulario Reutilizable --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        {{-- Tabs (Odoo Style) --}}
        <div class="px-6 pt-5 border-b border-gray-200 dark:border-gray-700">
            <button wire:click="setTab('general')"
                class="px-4 py-2 rounded-t-xl text-sm font-semibold {{ $tab === 'general' ? 'bg-indigo-600 text-white' : 'text-gray-500' }}">
                <i class="fa-regular fa-file-lines mr-2"></i> Información General
            </button>
        </div>

        <div class="p-6">
            {{-- 🚀 INCLUIMOS LOS CAMPOS REUTILIZABLES --}}
            @include('livewire.admin.journaltype.partials.form-fields')

            {{-- Botón de Acción con estado de carga --}}
            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                <button wire:click="save" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-10 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold shadow-lg hover:shadow-blue-500/30 transition-all active:scale-95">

                    <i class="fa-regular fa-floppy-disk" wire:loading.remove wire:target="save"></i>
                    <i class="fa-solid fa-circle-notch animate-spin" wire:loading wire:target="save"></i>

                    <span wire:loading.remove wire:target="save">Actualizar Cambios</span>
                    <span wire:loading wire:target="save">Procesando...</span>
                </button>
            </div>
        </div>
    </div>
</div>
