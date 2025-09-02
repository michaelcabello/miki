<tr>
    <td style="padding-left: {{ $level * 20 }}px;">
        {{ $account->code }}
    </td>
    <td>
        {{ $account->name }}
    </td>
    <td>
        Nivel {{ $level }}
    </td>
</tr>

@if($account->children && $account->children->count())
    @foreach($account->children as $child)
        @include('partials.account-row', ['account' => $child, 'level' => $level + 1])
    @endforeach
@endif

