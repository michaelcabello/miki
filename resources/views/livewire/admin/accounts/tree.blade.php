<ul class="ml-4">
    @foreach ($accounts as $account)
        <li>
            <strong>{{ $account->code }}</strong> - {{ $account->name }}

            @if ($account->children->isNotEmpty())
                @include('livewire.admin.accounts.tree', ['accounts' => $account->children])
            @endif
        </li>
    @endforeach
</ul>
