<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\AccountType;

class AccountController extends Controller
{


    public function create()
    {
        $accounts = Account::orderBy('code')->get();
        $accountTypes = AccountType::orderBy('name')->get();

        return view('admin.accounts.create', compact('accounts', 'accountTypes'));
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            //'code' => 'required|string|unique:accounts,code',
            //'code' => 'required|string|unique:accounts,code|max:6',
            'code' => 'required|digits_between:1,6|unique:accounts,code|max:6',
            'name' => 'required|string|max:255',
            'equivalent_code' => 'nullable|string|max:50',
            'account_type_id' => 'required|exists:account_types,id',
            'reconcile' => 'boolean',
            'cost_center' => 'boolean',
            'current_account' => 'boolean',
        ]);

        // Buscar el padre automático
        $parent = null;
        $code = $validated['code'];

        // Ir quitando dígitos hasta encontrar un padre
        for ($i = strlen($code) - 1; $i > 0; $i--) {
            $parentCode = substr($code, 0, $i);
            $parent = Account::where('code', $parentCode)->first();
            if ($parent) {
                break;
            }
        }

        $account = new Account($validated);
        $account->parent_id = $parent?->id;
        $account->depth = $parent ? $parent->depth + 1 : 0;
        $account->path = $parent ? $parent->path . '.' . $account->code : $account->code;
        $account->save();

        return redirect()->route('admin.accounts.index')
            ->with('success', 'Cuenta creada correctamente');
    }
}
