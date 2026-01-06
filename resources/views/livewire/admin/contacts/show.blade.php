<?php

use Livewire\Volt\Component;
use App\Models\Contact;

new class extends Component {
    public Contact $contact;

    public function with(): array
    {
        $this->contact->load(['marketings'=>fn($q)=>$q->with('category')]);
        return [];
    }
}; ?>

<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Contacto #{{ $contact->id }}</h1>
        <a href="{{ route('contacts.index') }}" class="px-3 py-2 bg-gray-100 rounded-xl">Volver</a>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl p-4 shadow">
            <div class="text-sm text-gray-600">Nombre</div>
            <div class="font-medium">{{ $contact->name }}</div>
            <div class="mt-2 text-sm text-gray-600">Email</div>
            <div class="font-medium">{{ $contact->email }}</div>
            <div class="mt-2 text-sm text-gray-600">Teléfonos</div>
            <div class="font-medium">{{ $contact->phone }} {{ $contact->movil ? ' / '.$contact->movil : '' }}</div>
            <div class="mt-2 text-sm text-gray-600">Contador total</div>
            <div class="font-medium">{{ $contact->contador }}</div>
            <div class="mt-2 text-sm text-gray-600">Send</div>
            <div class="font-medium">{{ $contact->send ? 'Sí' : 'No' }}</div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow">
            <h2 class="font-semibold mb-2">Historial por marketing</h2>
            <table class="w-full text-sm">
                <thead><tr class="text-left"><th class="py-2">Marketing</th><th>Categoria</th><th>Veces</th></tr></thead>
                <tbody>
                    @forelse($contact->marketings as $m)
                        <tr class="border-t">
                            <td class="py-2">{{ $m->titulo }}</td>
                            <td>{{ $m->category?->name }}</td>
                            <td>{{ $m->pivot->number }}</td>
                        </tr>
                    @empty
                        <tr><td class="py-3 text-gray-500" colspan="3">Sin envíos</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
