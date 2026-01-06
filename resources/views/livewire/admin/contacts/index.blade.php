<?php

use Livewire\Volt\Component;
use App\Models\Contact;
use App\Models\Marketing;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public ?int $sendContactId = null;
    public ?int $selectedMarketingId = null;
    public bool $showSendModal = false;

    public function with(): array
    {
        $contacts = Contact::query()
            ->when($this->search, fn($q) =>
                $q->where('name','like',"%{$this->search}%")
                  ->orWhere('email','like',"%{$this->search}%")
            )
            ->orderByDesc('id')
            ->paginate(10);

        $actives = Marketing::with('category')
            ->where('state', true)
            ->orderByDesc('id')
            ->get();

        return compact('contacts','actives');
    }

    public function toggleSend(int $id): void
    {
        $c = Contact::findOrFail($id);
        $c->update(['send' => !$c->send]);
        $this->dispatch('toast', type:'success', text:'Preferencia de envío actualizada.');
    }

    public function confirmDelete(int $id): void
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    public function destroy(int $id): void
    {
        Contact::findOrFail($id)->delete();
        $this->dispatch('toast', type:'success', text:'Contacto eliminado.');
    }

    public function openSend(int $contactId): void
    {
        $this->sendContactId = $contactId;
        $this->selectedMarketingId = null;
        $this->showSendModal = true;
    }

    public function send(): void
    {
        $contact = Contact::findOrFail($this->sendContactId);
        $marketing = Marketing::findOrFail($this->selectedMarketingId);

        // Dispatch job
        \App\Jobs\SendMarketingEmailJob::dispatch($contact, $marketing);

        $this->showSendModal = false;
        $this->dispatch('toast', type:'success', text:'Envío encolado.');
    }
}; ?>

<div class="p-6 space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Contactos</h1>
        <a href="{{ route('contacts.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:opacity-90">Nuevo</a>
    </div>

    <div class="flex gap-2">
        <input wire:model.debounce.500ms="search" type="text" placeholder="Buscar..." class="w-72 border rounded-xl px-3 py-2">
    </div>

    <div class="overflow-x-auto bg-white rounded-2xl shadow">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left">
                    <th class="p-3">ID</th>
                    <th class="p-3">Name</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Phone</th>
                    <th class="p-3">Contador</th>
                    <th class="p-3">Send</th>
                    <th class="p-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @foreach($contacts as $c)
                <tr class="border-t">
                    <td class="p-3">{{ $c->id }}</td>
                    <td class="p-3">{{ $c->name }}</td>
                    <td class="p-3">{{ $c->email }}</td>
                    <td class="p-3">{{ $c->phone ?? $c->movil }}</td>
                    <td class="p-3">{{ $c->contador }}</td>
                    <td class="p-3">
                        <button wire:click="toggleSend({{ $c->id }})"
                                class="px-2 py-1 rounded-xl {{ $c->send ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                            {{ $c->send ? 'ON' : 'OFF' }}
                        </button>
                    </td>
                    <td class="p-3 flex gap-2">
                        <a href="{{ route('contacts.show',$c) }}" class="px-2 py-1 bg-slate-100 rounded-xl">Ver</a>
                        <a href="{{ route('contacts.edit',$c) }}" class="px-2 py-1 bg-yellow-100 rounded-xl">Editar</a>
                        <button wire:click="openSend({{ $c->id }})" class="px-2 py-1 bg-indigo-100 rounded-xl">Enviar</button>
                        <button wire:click="confirmDelete({{ $c->id }})" class="px-2 py-1 bg-rose-100 rounded-xl text-rose-700">Eliminar</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="p-3">{{ $contacts->links() }}</div>
    </div>

    {{-- Modal enviar --}}
    <x-modal wire:model="showSendModal">
        <x-slot:title>Enviar marketing</x-slot:title>
        <div class="space-y-3">
            <select wire:model="selectedMarketingId" class="w-full border rounded-xl px-3 py-2">
                <option value="">-- Selecciona marketing activo --</option>
                @foreach($actives as $m)
                    <option value="{{ $m->id }}">{{ $m->titulo }} ({{ $m->category?->name }})</option>
                @endforeach
            </select>
            <div class="flex justify-end gap-2">
                <button wire:click="$set('showSendModal', false)" class="px-3 py-2 rounded-xl bg-gray-100">Cancelar</button>
                <button wire:click="send" @disabled(!$selectedMarketingId) class="px-3 py-2 rounded-xl bg-blue-600 text-white disabled:opacity-40">Enviar</button>
            </div>
        </div>
    </x-modal>
</div>

@push('scripts')
<script>
    // SweetAlert helpers
    window.addEventListener('confirm-delete', (e)=>{
        Swal.fire({
            title: '¿Eliminar?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning', showCancelButton: true, confirmButtonText: 'Sí, eliminar',
        }).then((r)=>{
            if (r.isConfirmed) { Livewire.dispatch('destroy', { id: e.detail.id }); }
        });
    });

    window.addEventListener('toast', (e)=>{
        Swal.fire({ toast:true, position:'top-end', timer:2500, showConfirmButton:false, icon:e.detail.type, title:e.detail.text });
    });
</script>
@endpush
