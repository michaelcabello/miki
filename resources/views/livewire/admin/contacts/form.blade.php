<?php

use Livewire\Volt\Component;
use App\Models\Contact;

new class extends Component {
    public Contact $contact;

    public function mount(?Contact $contact = null): void
    {
        $this->contact = $contact ?? new Contact([
            'dateofregistration' => now(),
            'send' => true,
        ]);
    }

    public function save(): mixed
    {
        $this->validate([
            'contact.name' => 'required|string|max:255',
            'contact.email' => 'required|email|unique:contacts,email,'.($this->contact->id ?? 'null'),
            'contact.dni' => 'nullable|string|max:50',
            'contact.phone' => 'nullable|string|max:50',
            'contact.movil' => 'nullable|string|max:50',
            'contact.birthdate' => 'nullable|date',
            'contact.dateofregistration' => 'required|date',
            'contact.message' => 'nullable|string',
            'contact.send' => 'boolean',
            'contact.user_id' => 'required|exists:users,id',
        ]);

        $this->contact->save();

        session()->flash('ok','Contacto guardado');
        return redirect()->route('contacts.index');
    }
}; ?>

<div class="p-6 space-y-4">
    <h1 class="text-2xl font-semibold">{{ $contact->exists ? 'Editar' : 'Crear' }} contacto</h1>

    <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded-2xl shadow">
        <x-input label="Nombre" wire:model="contact.name"/>
        <x-input label="Email" wire:model="contact.email" type="email"/>
        <x-input label="DNI" wire:model="contact.dni"/>
        <x-input label="Phone" wire:model="contact.phone"/>
        <x-input label="Movil" wire:model="contact.movil"/>
        <x-input label="Fecha nacimiento" wire:model="contact.birthdate" type="date"/>
        <x-input label="Fecha registro" wire:model="contact.dateofregistration" type="datetime-local"/>
        <div class="md:col-span-2">
            <label class="text-sm text-gray-600">Mensaje</label>
            <textarea wire:model="contact.message" class="w-full border rounded-xl px-3 py-2" rows="4"></textarea>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" wire:model="contact.send" class="rounded"> <span>Permitir envíos</span>
        </div>
        <x-input label="User ID" wire:model="contact.user_id" type="number"/>

        <div class="md:col-span-2 flex justify-end gap-2">
            <a href="{{ route('contacts.index') }}" class="px-4 py-2 bg-gray-100 rounded-xl">Cancelar</a>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-xl">Guardar</button>
        </div>
    </form>
</div>
