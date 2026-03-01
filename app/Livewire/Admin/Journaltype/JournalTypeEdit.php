<?php

namespace App\Livewire\Admin\Journaltype;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\JournalType;

class JournalTypeEdit extends Component
{

    public JournalType $jt; // model binding por {jt}

    public string $tab = 'general';

    // Campos editables
    public string $code = '';
    public string $name = '';
    public bool $state = true;
    public int $order = 0;

    public function mount(JournalType $jt): void
    {
        // ✅ Protege el acceso al formulario de edición
        // (Policy: JournalTypePolicy@update => permiso 'JournalType Update')
        $this->authorize('update', $jt);
        $this->jt = $jt;

        $this->code  = (string) $jt->code;
        $this->name  = (string) $jt->name;
        $this->state = (bool) $jt->state;
        $this->order = (int) $jt->order;
    }

    public function setTab(string $tab): void
    {
        $allowed = ['general'];
        $this->tab = in_array($tab, $allowed, true) ? $tab : 'general';
    }

    public function updatedCode($value): void
    {
        // Normaliza a formato tipo Odoo
        $value = strtoupper((string) $value);
        $value = preg_replace('/\s+/', '_', $value);
        $value = preg_replace('/[^A-Z0-9_]/', '', $value);
        $this->code = $value ?? '';
    }

    protected function rules(): array
    {
        return [
            'code'  => [
                'required',
                'string',
                'max:30',
                'regex:/^[A-Z0-9_]+$/',
                Rule::unique('journal_types', 'code')->ignore($this->jt->id),
            ],
            'name'  => ['required', 'string', 'max:120'],
            'state' => ['boolean'],
            'order' => ['required', 'integer', 'min:0', 'max:65535'],
        ];
    }

    protected array $messages = [
        'code.required' => 'El código es obligatorio.',
        'code.max' => 'El código no debe exceder 30 caracteres.',
        'code.regex' => 'Usa solo MAYÚSCULAS, números y guion bajo. Ej: SALE, BANK, CASH.',
        'code.unique' => 'Ese código ya existe.',
        'name.required' => 'El nombre es obligatorio.',
        'name.max' => 'El nombre no debe exceder 120 caracteres.',
        'order.required' => 'El orden es obligatorio.',
        'order.integer' => 'El orden debe ser un número.',
        'order.min' => 'El orden no puede ser negativo.',
    ];

   /*  public function update()
    {

        $this->authorize('update', $this->jt);
        $data = $this->validate();

        $this->jt->update([
            'code'  => $data['code'],
            'name'  => $data['name'],
            'state' => $data['state'],
            'order' => $data['order'],
        ]);


        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Bien Hecho',
            'text' => 'Tipo de diario actualizado correctamente',
        ]);

        return redirect()->route('admin.journaltypes.index');
    }
 */


    /**
     * Actualiza el tipo de diario.
     *
     * Flujo:
     *  1. Verifica permiso
     *  2. Valida el formulario
     *  3. Detecta si hubo cambios reales
     *  4. Abre transacción
     *  5. Actualiza el registro
     *  6. Confirma o revierte según resultado
     */
    public function update(): mixed
    {
        // 1. Doble verificación de permiso
        $this->authorize('update', $this->jt);

        // 2. Validación — si falla lanza ValidationException y detiene aquí
        $data = $this->validate();

        // 3. Si no hubo cambios reales, no toca la BD y avisa al usuario
        $sinCambios = $this->jt->code  === $data['code']
            && $this->jt->name  === $data['name']
            && $this->jt->state === $data['state']
            && $this->jt->order === $data['order'];

        if ($sinCambios) {
            session()->flash('swal', [
                'icon'  => 'info',
                'title' => 'Sin cambios',
                'text'  => 'No se detectaron cambios en el registro.',
            ]);
            return redirect()->route('admin.journaltypes.index');
        }

        // 4. Transacción — garantiza consistencia ante fallos de BD
        DB::beginTransaction();

        try {
            // 5. Actualiza el registro
            $this->jt->update([
                'code'  => $data['code'],
                'name'  => $data['name'],
                'state' => $data['state'],
                'order' => $data['order'],
            ]);

            // 6a. Todo correcto: confirma la transacción
            DB::commit();

            session()->flash('swal', [
                'icon'  => 'success',
                'title' => 'Bien hecho',
                'text'  => 'Tipo de diario "' . $data['code'] . '" actualizado correctamente.',
            ]);

            return redirect()->route('admin.journaltypes.index');
        } catch (QueryException $e) {
            // 6b. Error de BD (ej. race condition en unique constraint)
            DB::rollBack();

            if ($e->getCode() === '23000') {
                $this->addError('code', 'Ese código ya fue registrado por otro proceso. Intenta con uno diferente.');
                return null;
            }

            Log::error('Error al actualizar JournalType', [
                'id'      => $this->jt->id,
                'code'    => $data['code'],
                'error'   => $e->getMessage(),
                'usuario' => auth()->id(),
            ]);

            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Error de base de datos',
                'text'  => 'No se pudo actualizar el registro. Por favor intenta nuevamente.',
            ]);

            return null;
        } catch (AuthorizationException $e) {
            // 6c. Permiso revocado entre mount() y update()
            DB::rollBack();

            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Sin permiso',
                'text'  => 'No tienes permiso para actualizar tipos de diario.',
            ]);

            return redirect()->route('admin.journaltypes.index');
        } catch (\Throwable $e) {
            // 6d. Cualquier otro error inesperado
            DB::rollBack();

            Log::error('Error inesperado al actualizar JournalType', [
                'id'      => $this->jt->id,
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
        return view('livewire.admin.journaltype.journal-type-edit');
    }
}
