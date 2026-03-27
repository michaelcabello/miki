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


//php artisan make:livewire Admin/Attribute/AttributeEdit
#[Title('Editar Atributo')]
class AttributeEdit extends Component
{
    use AuthorizesRequests;

    // ── Model binding ─────────────────────────────────────────
    public Attribute $attribute;

    // ── Tabs ─────────────────────────────────────────────────
    public string $tab = 'general';

    // ── Campos editables ─────────────────────────────────────
    public string $name  = '';
    public bool   $state = true;
    public int    $order = 0;

    // ── Ciclo de vida ────────────────────────────────────────

    public function mount(Attribute $attribute): void
    {
        // Verifica permiso antes de mostrar el formulario de edición
        $this->authorize('update', $attribute);

        $this->attribute = $attribute;

        // Hidrata los campos con los valores actuales
        $this->name  = (string) $attribute->name;
        $this->state = (bool)   $attribute->state;
        $this->order = (int)    $attribute->order;
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
                // Ignora el registro actual al verificar unicidad
                Rule::unique('attributes', 'name')->ignore($this->attribute->id),
            ],
            'state' => ['boolean'],
            'order' => ['required', 'integer', 'min:0', 'max:65535'],
        ];
    }

    protected array $messages = [
        'name.required'  => 'El nombre es obligatorio.',
        'name.max'       => 'El nombre no debe exceder 100 caracteres.',
        'name.unique'    => 'Ya existe un atributo con ese nombre.',
        'order.required' => 'El orden es obligatorio.',
        'order.integer'  => 'El orden debe ser un número.',
        'order.min'      => 'El orden no puede ser negativo.',
    ];

    // ── Actualizar ───────────────────────────────────────────

    /**
     * Actualiza el atributo con transacción DB.
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
        $this->authorize('update', $this->attribute);

        // 2. Normaliza el nombre antes de validar
        $this->name = mb_convert_case(
            mb_strtolower(trim(preg_replace('/\s+/', ' ', $this->name)), 'UTF-8'),
            MB_CASE_TITLE,
            'UTF-8'
        );

        // 3. Validación
        $data = $this->validate();

        // 4. Detecta si hubo cambios reales para evitar writes innecesarios
        $sinCambios = $this->attribute->name  === $data['name']
            && $this->attribute->state === $data['state']
            && $this->attribute->order === $data['order'];

        if ($sinCambios) {
            session()->flash('swal', [
                'icon'  => 'info',
                'title' => 'Sin cambios',
                'text'  => 'No se detectaron cambios en el registro.',
            ]);
            return redirect()->route('admin.attributes.index');
        }

        // 5. Transacción
        DB::beginTransaction();

        try {
            $this->attribute->update([
                'name'  => $data['name'],
                'state' => $data['state'],
                'order' => $data['order'],
            ]);

            DB::commit();

            session()->flash('swal', [
                'icon'  => 'success',
                'title' => 'Bien hecho',
                'text'  => 'Atributo "' . $data['name'] . '" actualizado correctamente.',
            ]);

            return redirect()->route('admin.attributes.index');
        } catch (QueryException $e) {
            DB::rollBack();

            if ($e->getCode() === '23000') {
                $this->addError('name', 'Ese nombre ya fue registrado por otro proceso. Intenta con uno diferente.');
                return null;
            }

            Log::error('Error al actualizar Attribute', [
                'id'      => $this->attribute->id,
                'name'    => $data['name'],
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
            DB::rollBack();

            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Sin permiso',
                'text'  => 'No tienes permiso para actualizar atributos.',
            ]);

            return redirect()->route('admin.attributes.index');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Error inesperado al actualizar Attribute', [
                'id'      => $this->attribute->id,
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
        return view('livewire.admin.attribute.attribute-edit');
    }
}
