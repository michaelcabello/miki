<?php

namespace App\Livewire\Forms\Admin;

use Livewire\Form;
use App\Models\Warehouse;
use Illuminate\Validation\Rule;

class WarehouseForm extends Form
{
    public ?Warehouse $model = null;

    // ─── PROPIEDADES ────────────────────────────────────────────
    public string $code        = '';
    public string $name        = '';
    public string $description = '';
    public string $address     = '';
    public bool   $is_main     = false;
    public int    $order       = 0;
    public bool   $state       = true;

    // lot_stock_id NO se gestiona desde el formulario:
    // se asigna automáticamente al crear las ubicaciones del almacén

    // ─── VALIDACIÓN ─────────────────────────────────────────────

    public function rules(): array
    {
        return [
            'code'        => [
                'required', 'string', 'max:10',
                'regex:/^[A-Z0-9_]+$/',
                Rule::unique('warehouses', 'code')->ignore($this->model?->id),
            ],
            'name'        => [
                'required', 'string', 'max:150',
                Rule::unique('warehouses', 'name')->ignore($this->model?->id),
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'address'     => ['nullable', 'string', 'max:250'],
            'is_main'     => ['required', 'boolean'],
            'order'       => ['required', 'integer', 'min:0', 'max:9999'],
            'state'       => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required'  => 'El código es obligatorio.',
            'code.max'       => 'El código no puede superar 10 caracteres.',
            'code.regex'     => 'Solo mayúsculas, números y guión bajo (ej: WH01, WH_LIMA).',
            'code.unique'    => 'Ya existe un almacén con este código.',
            'name.required'  => 'El nombre es obligatorio.',
            'name.max'       => 'El nombre no puede superar 150 caracteres.',
            'name.unique'    => 'Ya existe un almacén con este nombre.',
            'order.required' => 'El orden es obligatorio.',
            'order.integer'  => 'El orden debe ser un número entero.',
            'order.min'      => 'El orden no puede ser negativo.',
        ];
    }

    // ─── CARGA PARA EDICIÓN ─────────────────────────────────────

    public function setModel(Warehouse $model): void
    {
        $this->model       = $model;
        $this->code        = $model->code;
        $this->name        = $model->name;
        $this->description = $model->description ?? '';
        $this->address     = $model->address ?? '';
        $this->is_main     = $model->is_main;
        $this->order       = $model->order;
        $this->state       = $model->state;
    }

    // ─── PERSISTENCIA ───────────────────────────────────────────

    public function store(): Warehouse
    {
        $data = $this->validate();

        // Normalizar código a mayúsculas
        $data['code'] = strtoupper(trim($data['code']));

        return Warehouse::create($data);
    }

    public function update(): Warehouse
    {
        $data = $this->validate();

        $data['code'] = strtoupper(trim($data['code']));

        $this->model->update($data);

        return $this->model;
    }
}
