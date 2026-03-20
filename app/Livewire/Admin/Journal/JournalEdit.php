<?php

namespace App\Livewire\Admin\Journal;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Journal;
use App\Models\JournalType;
use App\Models\Currency;
use App\Models\Account;

class JournalEdit extends Component
{
    use AuthorizesRequests;

    public Journal $record; // model binding por ruta

    public string $tab = 'general';

    // ── Tab: General ─────────────────────────────────────────────
    public string $name           = '';
    public string $code           = '';
    public bool   $state          = true;
    public bool   $active         = true;
    public bool   $use_documents  = false;
    public string $journal_type_id = '';
    public string $currency_id    = '';

    // ── Tab: Cuentas ─────────────────────────────────────────────
    public string $account_id               = '';
    public string $default_debit_account_id  = '';
    public string $default_credit_account_id = '';
    public string $suspense_account_id       = '';
    public string $exchange_gain_account_id  = '';
    public string $exchange_loss_account_id  = '';

    // ── Tab: Banco ───────────────────────────────────────────────
    public string $bank_name           = '';
    public string $bank_account_number = '';
    public string $cci                 = '';
    public string $swift               = '';
    public string $iban                = '';

    // ── Tab: Documentos ──────────────────────────────────────────
    public bool   $use_document_sequence = true;
    public string $document_prefix       = '';
    public int    $document_next_number  = 1;
    public bool   $allow_manual_entries  = true;
    public string $settings_raw          = '';

    public function mount(Journal $record): void
    {
        $this->authorize('update', $record);
        $this->record = $record;

        // Hidrata los campos con los valores actuales del registro
        $this->name           = (string) $record->name;
        $this->code           = (string) $record->code;
        $this->state          = (bool)   $record->state;
        $this->active         = (bool)   $record->active;
        $this->use_documents  = (bool)   $record->use_documents;
        $this->journal_type_id = (string) ($record->journal_type_id ?? '');
        $this->currency_id    = (string) ($record->currency_id ?? '');

        // Cuentas
        $this->account_id               = (string) ($record->account_id ?? '');
        $this->default_debit_account_id  = (string) ($record->default_debit_account_id ?? '');
        $this->default_credit_account_id = (string) ($record->default_credit_account_id ?? '');
        $this->suspense_account_id       = (string) ($record->suspense_account_id ?? '');
        $this->exchange_gain_account_id  = (string) ($record->exchange_gain_account_id ?? '');
        $this->exchange_loss_account_id  = (string) ($record->exchange_loss_account_id ?? '');

        // Banco
        $this->bank_name           = (string) ($record->bank_name ?? '');
        $this->bank_account_number = (string) ($record->bank_account_number ?? '');
        $this->cci                 = (string) ($record->cci ?? '');
        $this->swift               = (string) ($record->swift ?? '');
        $this->iban                = (string) ($record->iban ?? '');

        // Documentos
        $this->use_document_sequence = (bool)   $record->use_document_sequence;
        $this->document_prefix       = (string) ($record->document_prefix ?? '');
        $this->document_next_number  = (int)    ($record->document_next_number ?? 1);
        $this->allow_manual_entries  = (bool)   $record->allow_manual_entries;

        // Serializa settings a JSON legible para edición
        $this->settings_raw = $record->settings
            ? json_encode($record->settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            : '';
    }

    public function setTab(string $tab): void
    {
        $allowed   = ['general', 'accounts', 'bank', 'documents'];
        $this->tab = in_array($tab, $allowed, true) ? $tab : 'general';
    }

    /** Normaliza código: MAYÚSCULAS + números + guiones */
    public function updatedCode($value): void
    {
        $value      = strtoupper((string) $value);
        $value      = preg_replace('/\s+/', '_', $value);
        $value      = preg_replace('/[^A-Z0-9_\-]/', '', $value);
        $this->code = $value;
    }

    protected function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:190'],
            'code'            => ['required', 'string', 'max:20',
                                  Rule::unique('journals', 'code')->ignore($this->record->id)],
            'state'           => ['boolean'],
            'active'          => ['boolean'],
            'use_documents'   => ['boolean'],
            'journal_type_id' => ['required', 'exists:journal_types,id'],
            'currency_id'     => ['nullable', 'exists:currencies,id'],
            'account_id'               => ['nullable', 'exists:accounts,id'],
            'default_debit_account_id'  => ['nullable', 'exists:accounts,id'],
            'default_credit_account_id' => ['nullable', 'exists:accounts,id'],
            'suspense_account_id'       => ['nullable', 'exists:accounts,id'],
            'exchange_gain_account_id'  => ['nullable', 'exists:accounts,id'],
            'exchange_loss_account_id'  => ['nullable', 'exists:accounts,id'],
            'bank_name'           => ['nullable', 'string', 'max:190'],
            'bank_account_number' => ['nullable', 'string', 'max:60'],
            'cci'                 => ['nullable', 'string', 'max:40'],
            'swift'               => ['nullable', 'string', 'max:40'],
            'iban'                => ['nullable', 'string', 'max:40'],
            'use_document_sequence' => ['boolean'],
            'document_prefix'       => ['nullable', 'string', 'max:20'],
            'document_next_number'  => ['required', 'integer', 'min:1'],
            'allow_manual_entries'  => ['boolean'],
            'settings_raw'          => ['nullable', 'string'],
        ];
    }

    protected array $messages = [
        'name.required'            => 'El nombre es obligatorio.',
        'name.max'                 => 'El nombre no debe exceder 190 caracteres.',
        'code.required'            => 'El código es obligatorio.',
        'code.max'                 => 'El código no debe exceder 20 caracteres.',
        'code.unique'              => 'Ese código ya existe en otro diario.',
        'journal_type_id.required' => 'El tipo de diario es obligatorio.',
        'journal_type_id.exists'   => 'El tipo de diario seleccionado no es válido.',
        'document_next_number.min' => 'El número inicial debe ser al menos 1.',
    ];

    /**
     * Actualiza el diario.
     * Flujo: Permiso → Validación → Detectar cambios → Transacción → Update → Commit/Rollback
     */
    public function update(): mixed
    {
        // 1. Doble verificación de permiso
        $this->authorize('update', $this->record);

        // 2. Validación
        $data = $this->validate();

        // 3. Parsear settings JSON si fue ingresado
        $settings = null;
        if (!empty($this->settings_raw)) {
            $decoded = json_decode($this->settings_raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->addError('settings_raw', 'El JSON ingresado no es válido.');
                return null;
            }
            $settings = $decoded;
        }

        // 4. Detectar cambios reales — evitar toque innecesario a la BD
        $sinCambios =
            $this->record->name            === $data['name']
            && $this->record->code         === $data['code']
            && $this->record->state        === $data['state']
            && $this->record->active       === $data['active']
            && (string)($this->record->journal_type_id ?? '') === (string)$data['journal_type_id']
            && (string)($this->record->currency_id ?? '')     === (string)($data['currency_id'] ?? '');

        if ($sinCambios && empty($this->settings_raw) && !$this->record->settings) {
            session()->flash('swal', ['icon' => 'info', 'title' => 'Sin cambios', 'text' => 'No se detectaron cambios.']);
            return redirect()->route('admin.journals.index');
        }

        // 5. Transacción
        DB::beginTransaction();

        try {
            $this->record->update([
                'name'            => $data['name'],
                'code'            => $data['code'],
                'state'           => $data['state'],
                'active'          => $data['active'],
                'use_documents'   => $data['use_documents'],
                'journal_type_id' => $data['journal_type_id'],
                'currency_id'     => $data['currency_id'] ?: null,
                'account_id'               => $data['account_id'] ?: null,
                'default_debit_account_id'  => $data['default_debit_account_id'] ?: null,
                'default_credit_account_id' => $data['default_credit_account_id'] ?: null,
                'suspense_account_id'       => $data['suspense_account_id'] ?: null,
                'exchange_gain_account_id'  => $data['exchange_gain_account_id'] ?: null,
                'exchange_loss_account_id'  => $data['exchange_loss_account_id'] ?: null,
                'bank_name'           => $data['bank_name'] ?: null,
                'bank_account_number' => $data['bank_account_number'] ?: null,
                'cci'                 => $data['cci'] ?: null,
                'swift'               => $data['swift'] ?: null,
                'iban'                => $data['iban'] ?: null,
                'use_document_sequence' => $data['use_document_sequence'],
                'document_prefix'       => $data['document_prefix'] ?: null,
                'document_next_number'  => $data['document_next_number'],
                'allow_manual_entries'  => $data['allow_manual_entries'],
                'settings'              => $settings,
            ]);

            DB::commit();

            session()->flash('swal', [
                'icon'  => 'success',
                'title' => 'Bien hecho',
                'text'  => 'Diario "' . $data['name'] . '" actualizado correctamente.',
            ]);

            return redirect()->route('admin.journals.index');

        } catch (QueryException $e) {
            DB::rollBack();
            if ($e->getCode() === '23000') {
                $this->addError('code', 'Ese código ya fue registrado por otro proceso.');
                return null;
            }
            Log::error('Error al actualizar Journal', ['id' => $this->record->id, 'error' => $e->getMessage(), 'usuario' => auth()->id()]);
            session()->flash('swal', ['icon' => 'error', 'title' => 'Error de base de datos', 'text' => 'No se pudo actualizar el diario.']);
            return null;

        } catch (AuthorizationException $e) {
            DB::rollBack();
            session()->flash('swal', ['icon' => 'error', 'title' => 'Sin permiso', 'text' => 'No tienes permiso para actualizar.']);
            return redirect()->route('admin.journals.index');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error inesperado al actualizar Journal', ['id' => $this->record->id, 'error' => $e->getMessage(), 'usuario' => auth()->id()]);
            session()->flash('swal', ['icon' => 'error', 'title' => 'Error inesperado', 'text' => 'Ocurrió un problema. Contacta al administrador.']);
            return null;
        }
    }

    public function render()
    {
        $journalTypes = JournalType::where('state', true)
            ->orderBy('order')->orderBy('name')
            ->get(['id', 'name', 'code']);

        $currencies = Currency::orderBy('name')
            ->get(['id', 'name']);

        $accounts = Account::orderBy('name')
            ->get(['id', 'name', 'code']);

        return view('livewire.admin.journal.journal-edit', compact('journalTypes', 'currencies', 'accounts'));
    }
}
