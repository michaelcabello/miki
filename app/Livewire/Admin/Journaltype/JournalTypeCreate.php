<?php

namespace App\Livewire\Admin\Journaltype;

use Livewire\Component;
use Illuminate\Validation\Rule;
use App\Models\JournalType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;





class JournalTypeCreate extends Component
{

    public string $tab = 'general';

    // Campos
    public string $code = '';
    public string $name = '';
    public bool $state = true;
    public int $order = 0;


    public function mount(): void
    {
        $this->authorize('create', JournalType::class);
    }


    public function setTab(string $tab): void
    {
        $allowed = ['general'];
        $this->tab = in_array($tab, $allowed, true) ? $tab : 'general';
    }

    protected function rules(): array
    {
        return [
            'code'  => ['required', 'string', 'max:30', 'regex:/^[A-Z0-9_]+$/', Rule::unique('journal_types', 'code')],
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

    public function updatedCode($value): void
    {
        // Auto-normaliza a formato tipo Odoo: MAYÚSCULAS + _ y números
        $value = strtoupper((string) $value);
        $value = preg_replace('/\s+/', '_', $value);
        $value = preg_replace('/[^A-Z0-9_]/', '', $value);
        $this->code = $value ?? '';
    }

    public function savebasico()
    {
        $this->authorize('create', JournalType::class);
        $data = $this->validate();

        JournalType::create([
            'code'  => $data['code'],
            'name'  => $data['name'],
            'state' => $data['state'],
            'order' => $data['order'],
        ]);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Bien Hecho',
            'text' => 'Tipo de diario creado correctamente',
        ]);

        //session()->flash('success', 'Tipo de diario creado correctamente.');
        return redirect()->route('admin.journaltypes.index');
    }



    /**
     * Guarda el nuevo tipo de diario.
     *
     * Flujo:
     *  1. Verifica permiso
     *  2. Valida el formulario
     *  3. Abre transacción
     *  4. Crea el registro
     *  5. Confirma o revierte según resultado
     */
    public function save(): mixed
    {
        // 1. Doble verificación de permiso (aunque mount() ya lo hizo,
        //    protege contra llamadas directas al método desde el frontend)
        $this->authorize('create', JournalType::class);

        // 2. Validación — si falla lanza ValidationException y detiene aquí
        $data = $this->validate();

        // 3. Transacción — garantiza consistencia ante fallos de BD
        DB::beginTransaction();

        try {
            // 4. Crea el registro
            JournalType::create([
                'code'  => $data['code'],
                'name'  => $data['name'],
                'state' => $data['state'],
                'order' => $data['order'],
            ]);

            // 5a. Todo correcto: confirma la transacción
            DB::commit();

            session()->flash('swal', [
                'icon'  => 'success',
                'title' => 'Bien hecho',
                'text'  => 'Tipo de diario "' . $data['code'] . '" creado correctamente.',
            ]);

            return redirect()->route('admin.journaltypes.index');
        } catch (QueryException $e) {
            // 5b. Error de BD (ej. race condition en unique constraint)
            DB::rollBack();

            // Detecta violación de unique key para dar mensaje amigable
            if ($e->getCode() === '23000') {
                $this->addError('code', 'Ese código ya fue registrado por otro proceso. Intenta con uno diferente.');
                return null;
            }

            // Registra el error técnico para el desarrollador
            Log::error('Error al crear JournalType', [
                'code'      => $data['code'],
                'error'     => $e->getMessage(),
                'usuario'   => auth()->id(),
            ]);

            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Error de base de datos',
                'text'  => 'No se pudo guardar el registro. Por favor intenta nuevamente.',
            ]);

            return null;
        } catch (AuthorizationException $e) {
            // 5c. Permiso revocado entre mount() y save()
            DB::rollBack();

            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Sin permiso',
                'text'  => 'No tienes permiso para crear tipos de diario.',
            ]);

            return redirect()->route('admin.journaltypes.index');
        } catch (\Throwable $e) {
            // 5d. Cualquier otro error inesperado
            DB::rollBack();

            Log::error('Error inesperado al crear JournalType', [
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

        return view('livewire.admin.journaltype.journal-type-create');
    }
}
