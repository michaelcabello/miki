<?php

namespace App\Livewire\Forms\Admin;

use Livewire\Form;
use App\Models\JournalType;
use Illuminate\Validation\Rule;

class JournalTypeForm extends Form
{
    public ?JournalType $journalType = null;

    public string $code = '';
    public string $name = '';
    public bool $state = true;
    public int $order = 0;

    public function setModel(JournalType $journalType): void
    {
        $this->journalType = $journalType;
        $this->code  = $journalType->code;
        $this->name  = $journalType->name;
        $this->state = (bool) $journalType->state;
        $this->order = (int) $journalType->order;
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:30',
                'regex:/^[A-Z0-9_]+$/',
                Rule::unique('journal_types', 'code')->ignore($this->journalType?->id)
            ],
            'name'  => ['required', 'string', 'max:120'],
            'state' => ['boolean'],
            'order' => ['required', 'integer', 'min:0'],
        ];
    }







    public function store(): JournalType
    {
        $this->validate();

        // 🚀 CORRECCIÓN: except() ya es un array.
        return JournalType::create($this->except('journalType'));
    }

    public function update(): bool
    {
        $this->validate();

        // 🚀 CORRECCIÓN: except() ya es un array.
        return $this->journalType->update($this->except('journalType'));
    }

    public function updatedCode($value): void
    {
        $this->code = strtoupper(preg_replace('/[^A-Z0-9_]/', '', str_replace(' ', '_', $value)));
    }
}
