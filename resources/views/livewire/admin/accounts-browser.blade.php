<div class="p-6">
    {{-- Botones superiores --}}
    <div class="flex space-x-2 mb-6 justify-center">
        @for ($i = 0; $i <= 9; $i++)
            <button
                wire:click="selectGroup('{{ $i }}')"
                class="px-4 py-2 rounded-lg text-white
                       {{ $selectedGroup == $i ? 'bg-blue-600' : 'bg-blue-400 hover:bg-blue-500' }}">
                {{ $i }}
            </button>
        @endfor
    </div>

    {{-- Tabla de cuentas --}}
    @if ($selectedGroup !== null)
        <h2 class="text-lg font-bold mb-4">
            Cuentas del grupo {{ $selectedGroup }}
        </h2>

        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="w-full border-collapse">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-2 border">Código</th>
                        <th class="px-4 py-2 border">Nombre</th>
                        <th class="px-4 py-2 border">Tipo</th>
                        <th class="px-4 py-2 border">Conciliable</th>
                        <th class="px-4 py-2 border">Centro de Costos</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($accounts as $account)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border">{{ $account->code }}</td>
                            <td class="px-4 py-2 border">{{ $account->name }}</td>
                            <td class="px-4 py-2 border">
                                {{ $account->accountType->name ?? '-' }}
                            </td>
                            <td class="px-4 py-2 border text-center">
                                {{ $account->reconcile ? '✔' : '✖' }}
                            </td>
                            <td class="px-4 py-2 border text-center">
                                {{ $account->cost_center ? '✔' : '✖' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-2 border text-center text-gray-500">
                                No hay cuentas en este grupo
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
