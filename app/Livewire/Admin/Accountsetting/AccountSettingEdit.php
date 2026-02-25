<?php

namespace App\Livewire\Admin\Accountsetting;

use Livewire\Component;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Models\Account;
use App\Models\AccountSetting;



class AccountSettingEdit extends Component
{

   public string $tab = 'general';

    public array $accountOptions = [];

    // ===== Campos existentes =====
    public ?int $default_receivable_account_id = null;     // 12...
    public ?int $default_payable_account_id = null;        // 42...

    public ?int $default_income_account_id = null;         // fallback 70...
    public ?int $default_expense_account_id = null;        // fallback 60/61...

    public ?int $default_sales_tax_account_id = null;      // 40.. IGV venta
    public ?int $default_purchase_tax_account_id = null;   // 40.. IGV compra

    public ?int $rounding_account_id = null;
    public bool $active = true;

    // ===== NUEVOS CAMPOS (mejora goods vs service) =====
    public ?int $default_income_goods_account_id = null;   // 701...
    public ?int $default_income_service_account_id = null; // 704...

    public ?int $default_expense_goods_account_id = null;  // 601...
    public ?int $default_expense_service_account_id = null;// 63...

    public function mount(): void
    {
        // Opciones de cuentas (solo cuentas de registro)
        $this->accountOptions = Account::query()
            ->where('isrecord', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name'])
            ->map(fn($a) => [
                'id' => $a->id,
                'label' => trim(($a->code ? $a->code . ' - ' : '') . $a->name),
            ])
            ->all();

        // Traer el único registro (si existe)
        $row = AccountSetting::query()->first();

        // Si no existe, crear uno vacío para que siempre haya 1
        if (!$row) {
            $row = AccountSetting::create(['active' => true]);
        }

        // Cargar a propiedades
        $this->fillFromModel($row);

        // Defaults (si están null)
        $this->applyDefaultsByCode();
    }

    private function fillFromModel(AccountSetting $row): void
    {
        $this->default_receivable_account_id = $row->default_receivable_account_id;
        $this->default_payable_account_id = $row->default_payable_account_id;

        $this->default_income_account_id = $row->default_income_account_id;
        $this->default_expense_account_id = $row->default_expense_account_id;

        $this->default_income_goods_account_id = $row->default_income_goods_account_id;
        $this->default_income_service_account_id = $row->default_income_service_account_id;

        $this->default_expense_goods_account_id = $row->default_expense_goods_account_id;
        $this->default_expense_service_account_id = $row->default_expense_service_account_id;

        $this->default_sales_tax_account_id = $row->default_sales_tax_account_id;
        $this->default_purchase_tax_account_id = $row->default_purchase_tax_account_id;

        $this->rounding_account_id = $row->rounding_account_id;
        $this->active = (bool) $row->active;
    }

    private function accIdByCode(string $code): ?int
    {
        return Account::query()->where('code', $code)->value('id');
    }

    private function applyDefaultsByCode(): void
    {
        // CxC / CxP
        $this->default_receivable_account_id ??= $this->accIdByCode('1212');
        $this->default_payable_account_id    ??= $this->accIdByCode('421');

        // IGV (ajusta si separas IGV compra vs venta)
        $this->default_sales_tax_account_id    ??= $this->accIdByCode('40111');
        $this->default_purchase_tax_account_id ??= $this->accIdByCode('40111');

        // Fallback generales
        $this->default_income_account_id  ??= $this->accIdByCode('70111'); // fallback general (si no hay mejor)
        $this->default_expense_account_id ??= $this->accIdByCode('601');   // fallback general (si no hay mejor)

        // 🔥 Nuevos defaults sugeridos
        $this->default_income_goods_account_id   ??= $this->accIdByCode('70111'); // mercaderías
        $this->default_income_service_account_id ??= $this->accIdByCode('704');   // servicios (ajusta a tu PCGE real)

        $this->default_expense_goods_account_id  ??= $this->accIdByCode('601');   // mercaderías
        $this->default_expense_service_account_id??= $this->accIdByCode('63');    // servicios (ajusta)
    }

    public function setTab(string $tab): void
    {
        $allowed = ['general', 'income', 'expense', 'tax'];
        $this->tab = in_array($tab, $allowed, true) ? $tab : 'general';
    }

    protected function rules(): array
    {
        return [
            'default_receivable_account_id' => ['nullable', 'exists:accounts,id'],
            'default_payable_account_id'    => ['nullable', 'exists:accounts,id'],

            'default_income_account_id'  => ['nullable', 'exists:accounts,id'],
            'default_expense_account_id' => ['nullable', 'exists:accounts,id'],

            // NUEVOS
            'default_income_goods_account_id'   => ['nullable', 'exists:accounts,id'],
            'default_income_service_account_id' => ['nullable', 'exists:accounts,id'],
            'default_expense_goods_account_id'  => ['nullable', 'exists:accounts,id'],
            'default_expense_service_account_id'=> ['nullable', 'exists:accounts,id'],

            'default_sales_tax_account_id'    => ['nullable', 'exists:accounts,id'],
            'default_purchase_tax_account_id' => ['nullable', 'exists:accounts,id'],

            'rounding_account_id' => ['nullable', 'exists:accounts,id'],
            'active' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        DB::transaction(function () use ($data) {
            AccountSetting::query()->updateOrCreate(
                ['id' => AccountSetting::query()->value('id') ?? null],
                $data
            );
        });

        $this->dispatch('show-swal', [
            'title' => 'Guardado',
            'text'  => 'Configuración contable actualizada',
            'icon'  => 'success',
        ]);
    }



    public function render()
    {
        return view('livewire.admin.accountsetting.account-setting-edit');
    }
}
