<?php

namespace App\Livewire\Forms\Admin;

use Livewire\Form;
use App\Models\WarehouseLocation;
use Illuminate\Validation\Rule;

class WarehouseLocationForm extends Form
{
    public ?WarehouseLocation $model = null;

    // ─── PROPIEDADES ────────────────────────────────────────────
    public string  $code           = '';
    public string  $name           = '';
    public int     $order          = 0;
    public ?int    $parent_id      = null;
    public ?int    $warehouse_id   = null;
    public string  $usage          = 'internal';
    public bool    $scrap_location = false;
    public bool    $state          = true;
    public ?string $capacity       = null;

    // complete_name se genera automáticamente — no en el formulario

    // ─── VALIDACIÓN ─────────────────────────────────────────────

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9_\-\/]+$/',
                // Unicidad de code POR warehouse_id (igual que la migración)
                Rule::unique('warehouse_locations', 'code')
                    ->where('warehouse_id', $this->warehouse_id)
                    ->ignore($this->model?->id),
            ],
            'name'           => ['required', 'string', 'max:150'],
            'order'          => ['required', 'integer', 'min:0', 'max:9999'],
            'parent_id'      => ['nullable', 'exists:warehouse_locations,id'],
            'warehouse_id'   => ['nullable', 'exists:warehouses,id'],
            'usage'          => ['required', Rule::in(array_keys(WarehouseLocation::$usageLabels))],
            'scrap_location' => ['required', 'boolean'],
            'state'          => ['required', 'boolean'],
            'capacity'       => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required'       => 'El código es obligatorio.',
            'code.max'            => 'El código no puede superar 50 caracteres.',
            'code.regex'          => 'Solo mayúsculas, números, guión, guión bajo y "/". Ej: STOCK, A-1, WH/IN.',
            'code.unique'         => 'Ya existe una ubicación con este código en el almacén seleccionado.',
            'name.required'       => 'El nombre es obligatorio.',
            'name.max'            => 'El nombre no puede superar 150 caracteres.',
            'usage.required'      => 'El tipo de ubicación es obligatorio.',
            'usage.in'            => 'El tipo de ubicación no es válido.',
            'parent_id.exists'    => 'La ubicación padre seleccionada no existe.',
            'warehouse_id.exists' => 'El almacén seleccionado no existe.',
            'capacity.numeric'    => 'La capacidad debe ser un número.',
            'capacity.min'        => 'La capacidad no puede ser negativa.',
        ];
    }

    // ─── CARGA PARA EDICIÓN ─────────────────────────────────────

    public function setModel(WarehouseLocation $model): void
    {
        $this->model         = $model;
        $this->code          = $model->code;
        $this->name          = $model->name;
        $this->order         = $model->order;
        $this->parent_id     = $model->parent_id;
        $this->warehouse_id  = $model->warehouse_id;
        $this->usage         = $model->usage;
        $this->scrap_location = $model->scrap_location;
        $this->state         = $model->state;
        $this->capacity      = $model->capacity ? (string) $model->capacity : null;
    }

    // ─── PERSISTENCIA ───────────────────────────────────────────

    public function store(): WarehouseLocation
    {
        $data = $this->validate();
        $data['code'] = strtoupper(trim($data['code']));

        // Crear el registro primero para obtener el ID
        $location = WarehouseLocation::create($data);

        // Generar complete_name cargando las relaciones necesarias
        $location->load(['parent', 'warehouse']);
        $location->complete_name = $location->generateCompleteName();
        $location->save();

        return $location;
    }

    public function update(): WarehouseLocation
    {
        $data = $this->validate();
        $data['code'] = strtoupper(trim($data['code']));

        $this->model->update($data);

        // Regenerar complete_name al actualizar
        $this->model->load(['parent', 'warehouse']);
        $this->model->complete_name = $this->model->generateCompleteName();
        $this->model->save();

        return $this->model;
    }
}
