<div>
    <tr>
        <td class="border px-2 py-1" style="padding-left: {{ $depth * 20 }}px;">
            {{ $account->code }}
        </td>
        {{-- <td class="border px-2 py-1">{{ $account->equivalent_code }}</td> --}}
        <td class="border px-2 py-1">{{ $account->name }}</td>
        <td class="border px-2 py-1">{{ $account->accountType->name }}</td>
        <td class="border px-2 py-1">
            {{ $account->reconcile ? '✅' : '❌' }}
        </td>
    </tr>

    @if($account->children->isNotEmpty())
        @foreach($account->children as $child)
            @livewire('admin.account-item', ['account' => $child], key($child->id))
        @endforeach
    @endif
</div>

