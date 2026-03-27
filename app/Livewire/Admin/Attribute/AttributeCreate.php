<?php

namespace App\Livewire\Admin\Attribute;

use Livewire\Component;
use App\Models\Attribute;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;


//php artisan make:livewire Admin/Attribute/AttributeCreate
#[Title('Crear Atributo')]
class AttributeCreate extends Component
{
    use AuthorizesRequests;

    // ── Tabs ─────────────────────────────────────────────────
    public string $tab = 'general';

    // ── Campos del formulario ────────────────────────────────
    public string $name  = '';
    public bool   $state = true;
    public int    $order = 0;

    // ── Ciclo de vida ────────────────────────────────────────

    public function mount(): void
    {
        // Verifica permiso antes de mostrar el formulario
        $this->authorize('create', Attribute::class);
    }

    // ── Tabs ─────────────────────────────────────────────────

    public function setTab(string $tab): void
    {
        $allowed   = ['general'];
        $this->tab = in_array($tab, $allowed, true) ? $tab : 'general';
    }

    // ── Normalización en tiempo real ─────────────────────────

    /**
     * Normaliza el nombre: capitaliza primera letra de cada palabra.
     * Ej: "talla grande" → "Talla Grande"
     */
    public function updatedName(string $value): void
    {
        $value      = trim($value);
        $value      = preg_replace('/\s+/', ' ', $value);
        $this->name = mb_convert_case(mb_strtolower($value, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }

    // ── Reglas de validación ─────────────────────────────────

    protected function rules(): array
    {
        return [
            'name'  => [
                'required',
                'string',
                'min:1',
                'max:100',
                Rule::unique('attributes', 'name'),
            ],
            'state' => ['boolean'],
            'order' => ['required', 'integer', 'min:0', 'max:65535'],
        ];
    }

    protected array $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'name.max'      => 'El nombre no debe exceder 100 caracteres.',
        'name.unique'   => 'Ya existe un atributo con ese nombre.',
        'order.required' => 'El orden es obligatorio.',
        'order.integer'  => 'El orden debe ser un número.',
        'order.min'      => 'El orden no puede ser negativo.',
    ];

    // ── Guardar ──────────────────────────────────────────────

    /**
     * Crea el atributo con transacción DB.
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
        // 1. Doble verificación de permiso
        $this->authorize('create', Attribute::class);

        // 2. Normaliza el nombre antes de validar
        $this->name = mb_convert_case(
            mb_strtolower(trim(preg_replace('/\s+/', ' ', $this->name)), 'UTF-8'),
            MB_CASE_TITLE,
            'UTF-8'
        );

        // 3. Validación — detiene si falla
        $data = $this->validate();

        // 4. Transacción
        DB::beginTransaction();

        try {
            Attribute::create([
                'name'  => $data['name'],
                'state' => $data['state'],
                'order' => $data['order'],
            ]);

            DB::commit();

            session()->flash('swal', [
                'icon'  => 'success',
                'title' => 'Bien hecho',
                'text'  => 'Atributo "' . $data['name'] . '" creado correctamente.',
            ]);

            return redirect()->route('admin.attributes.index');
        } catch (QueryException $e) {
            DB::rollBack();

            // Violación de restricción única (race condition)
            if ($e->getCode() === '23000') {
                $this->addError('name', 'Ya existe un atributo con ese nombre. Intenta con uno diferente.');
                return null;
            }

            Log::error('Error al crear Attribute', [
                'name'    => $data['name'],
                'error'   => $e->getMessage(),
                'usuario' => auth()->id(),
            ]);

            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Error de base de datos',
                'text'  => 'No se pudo guardar el registro. Por favor intenta nuevamente.',
            ]);

            return null;
        } catch (AuthorizationException $e) {
            DB::rollBack();

            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Sin permiso',
                'text'  => 'No tienes permiso para crear atributos.',
            ]);

            return redirect()->route('admin.attributes.index');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Error inesperado al crear Attribute', [
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

    // ── Render ───────────────────────────────────────────────

    public function render()
    {
        return view('livewire.admin.attribute.attribute-create');
    }
}



