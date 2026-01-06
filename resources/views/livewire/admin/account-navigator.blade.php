<div class="p-4 space-y-4">
    <!-- Nivel 1 -->
    <div class="flex flex-wrap gap-2">
        @foreach($level1 as $code)
            <button wire:click="selectLevel1('{{ $code }}')"
                class="px-4 py-2 rounded-lg
                       {{ $selectedLevel1 == $code ? 'bg-blue-700' : 'bg-blue-500' }}
                       text-white hover:bg-blue-600">
                {{ $code }}
            </button>
        @endforeach
    </div>

    <!-- Nivel 2 -->
    @if($level2)
        <div class="flex flex-wrap gap-2">
            @foreach($level2 as $code)
                <button wire:click="selectLevel2('{{ $code }}')"
                    class="px-3 py-1 rounded-lg bg-green-500 text-white hover:bg-green-600">
                    {{ $code }}
                </button>
            @endforeach
        </div>
    @endif

    <!-- Tabla de cuentas -->
    @if($accounts)
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-200 rounded-lg shadow">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Código</th>
                        <th class="px-4 py-2 border">Nombre</th>
                        <th class="px-4 py-2 border">Tipo</th>
                        <th class="px-4 py-2 border">Equivalente</th>
                        <th class="px-4 py-2 border">EsRegistro</th>
                        <th class="px-4 py-2 border">Acciones</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $account)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border">{{ $account->code }}</td>
                            <td class="px-4 py-2 border">{{ $account->name }}</td>
                            <td class="px-4 py-2 border">{{ $account->accountType->name }}</td>
                            <td class="px-4 py-2 border">{{ $account->equivalent_code }}</td>
                            <td class="px-4 py-2 border">{{ $account->isrecord }}</td>
                            <td class="px-4 py-2 border">editar / eliminar</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-center">No hay cuentas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>

