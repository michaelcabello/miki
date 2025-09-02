<tr>
    <td class="px-3 py-2 font-mono">{{ $account->code }}</td>
    <td class="px-3 py-2">{!! str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) !!} {{ $account->name }}</td>
    <td class="px-3 py-2">{{ optional($account->accountType)->name }}</td>
    <td class="px-3 py-2">{{ $account->reconcile ? 'Sí' : 'No' }}</td>
</tr>

@if ($account->children && $account->children->count())
    @foreach ($account->children as $child)
        @include('livewire.admin.partials.account-row', ['account' => $child, 'level' => $level + 1])
    @endforeach
@endif
