<?php

namespace App\Livewire\Admin\Tax;

use Livewire\Component;

use App\Models\Tax;
use App\Models\Account;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaxEdit extends Component
{
    use AuthorizesRequests;

    public Tax $tax; // model binding por ruta {tax}

    public string $tab = 'general';

    // ── Campos del formulario ────────────────────────────────────────────────
    public string $name                 = '';
    public float  $amount               = 0;
    public string $amount_type          = 'percent';
    public string $type_tax_use         = 'sale';
    public ?int   $account_id           = null;
    public string $tax_scope            = '';
    public int    $sequence             = 1;
    public bool   $price_include        = false;
    public bool   $include_base_amount  = false;
    public bool   $is_base_affected     = false;
    public bool   $active               = true;
    public string $description          = '';

    public function mount(Tax $tax): void
    {
        // Verifica permiso de edición al montar el componente
        $this->authorize('update', $tax);
        $this->tax = $tax;

        // Hidrata los campos con los valores actuales del registro
        $this->name                = (string) $tax->name;
        $this->amount              = (float)  $tax->amount;
        $this->amount_type         = (string) $tax->amount_type;
        $this->type_tax_use        = (string) $tax->type_tax_use;
        $this->account_id          = $tax->account_id ? (int) $tax->account_id : null;
        $this->tax_scope           = (string) ($tax->tax_scope ?? '');
        $this->sequence            = (int)    $tax->sequence;
        $this->price_include       = (bool)   $tax->price_include;
        $this->include_base_amount = (bool)   $tax->include_base_amount;
        $this->is_base_affected    = (bool)   $tax->is_base_affected;
        $this->active              = (bool)   $tax->active;
        $this->description         = (string) ($tax->description ?? '');
    }

    public function setTab(string $tab): void
    {
        $allowed   = ['general'];
        $this->tab = in_array($tab, $allowed, true) ? $tab : 'general';
    }

    protected function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'max:120'],
            'amount'              => ['required', 'numeric', 'min:0', 'max:999999'],
            'amount_type'         => ['required', 'in:percent,fixed,division,group'],
            'type_tax_use'        => ['required', 'in:sale,purchase,none'],
            'account_id'          => ['nullable', 'integer', 'exists:accounts,id'],
            'tax_scope'           => ['nullable', 'string', 'max:50'],
            'sequence'            => ['required', 'integer', 'min:0', 'max:65535'],
            'price_include'       => ['boolean'],
            'include_base_amount' => ['boolean'],
            'is_base_affected'    => ['boolean'],
            'active'              => ['boolean'],
            'description'         => ['nullable', 'string', 'max:255'],
        ];
    }

    protected array $messages = [
        'name.required'         => 'El nombre del impuesto es obligatorio.',
        'name.max'              => 'El nombre no debe exceder 120 caracteres.',
        'amount.required'       => 'El monto o porcentaje es obligatorio.',
        'amount.numeric'        => 'El monto debe ser un número.',
        'amount.min'            => 'El monto no puede ser negativo.',
        'amount_type.required'  => 'El tipo de cálculo es obligatorio.',
        'amount_type.in'        => 'El tipo de cálculo no es válido.',
        'type_tax_use.required' => 'El ámbito de uso es obligatorio.',
        'type_tax_use.in'       => 'El ámbito de uso no es válido.',
        'account_id.exists'     => 'La cuenta seleccionada no existe.',
        'sequence.required'     => 'La secuencia es obligatoria.',
        'sequence.integer'      => 'La secuencia debe ser un número entero.',
        'sequence.min'          => 'La secuencia no puede ser negativa.',
        'description.max'       => 'La descripción no debe exceder 255 caracteres.',
    ];

    /**
     * Actualiza el impuesto.
     *
     * Flujo:
     *  1. Verifica permiso
     *  2. Valida el formulario
     *  3. Detecta si hubo cambios reales — evita tocar la BD innecesariamente
     *  4. Abre transacción
     *  5. Actualiza el registro
     *  6. Confirma o revierte según resultado
     */
    public function update(): mixed
    {
        // 1. Doble verificación de permiso
        $this->authorize('update', $this->tax);

        // 2. Validación — si falla lanza ValidationException y detiene aquí
        $data = $this->validate();

        // 3. Detectar cambios reales — evita escrituras innecesarias en BD
        $sinCambios = $this->tax->name                === $data['name']
                   && (float) $this->tax->amount      === (float) $data['amount']
                   && $this->tax->amount_type         === $data['amount_type']
                   && $this->tax->type_tax_use        === $data['type_tax_use']
                   && $this->tax->account_id          == $data['account_id']
                   && ($this->tax->tax_scope ?? '')   === ($data['tax_scope'] ?: '')
                   && (int) $this->tax->sequence      === (int) $data['sequence']
                   && (bool) $this->tax->price_include       === (bool) $data['price_include']
                   && (bool) $this->tax->include_base_amount === (bool) $data['include_base_amount']
                   && (bool) $this->tax->is_base_affected    === (bool) $data['is_base_affected']
                   && (bool) $this->tax->active              === (bool) $data['active']
                   && ($this->tax->description ?? '')  === ($data['description'] ?: '');

        if ($sinCambios) {
            session()->flash('swal', [
                'icon'  => 'info',
                'title' => 'Sin cambios',
                'text'  => 'No se detectaron cambios en el registro.',
            ]);
            return redirect()->route('admin.taxes.index');
        }

        // 4. Transacción — garantiza consistencia ante fallos de BD
        DB::beginTransaction();

        try {
            // 5. Actualiza el registro
            $this->tax->update([
                'name'                => $data['name'],
                'amount'              => $data['amount'],
                'amount_type'         => $data['amount_type'],
                'type_tax_use'        => $data['type_tax_use'],
                'account_id'          => $data['account_id'],
                'tax_scope'           => $data['tax_scope'] ?: null,
                'sequence'            => $data['sequence'],
                'price_include'       => $data['price_include'],
                'include_base_amount' => $data['include_base_amount'],
                'is_base_affected'    => $data['is_base_affected'],
                'active'              => $data['active'],
                'description'         => $data['description'] ?: null,
            ]);

            // 6a. Todo correcto: confirma la transacción
            DB::commit();

            session()->flash('swal', [
                'icon'  => 'success',
                'title' => 'Bien hecho',
                'text'  => 'Impuesto "' . $data['name'] . '" actualizado correctamente.',
            ]);

            return redirect()->route('admin.taxes.index');

        } catch (QueryException $e) {
            // 6b. Error de BD
            DB::rollBack();

            Log::error('Error al actualizar Tax', [
                'id'      => $this->tax->id,
                'error'   => $e->getMessage(),
                'usuario' => auth()->id(),
            ]);

            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Error de base de datos',
                'text'  => 'No se pudo actualizar el impuesto. Por favor intenta nuevamente.',
            ]);

            return null;

        } catch (AuthorizationException $e) {
            // 6c. Permiso revocado entre mount() y update()
            DB::rollBack();

            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Sin permiso',
                'text'  => 'No tienes permiso para actualizar impuestos.',
            ]);

            return redirect()->route('admin.taxes.index');

        } catch (\Throwable $e) {
            // 6d. Cualquier otro error inesperado
            DB::rollBack();

            Log::error('Error inesperado al actualizar Tax', [
                'id'      => $this->tax->id,
                'error'   => $e->getMessage(),
                'usuario' => auth()->id(),
            ]);

            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Error inesperado',
                'text'  => 'Ocurrió un problema. Por favor contacta al administrador.',
            ]);

            return null;
        }
    }

    public function render()
    {
        // Carga solo id y name para el select de cuentas — previene N+1
        $accounts = Account::orderBy('name')->get(['id', 'name']);

        return view('livewire.admin.tax.tax-edit', compact('accounts'));
    }
}
