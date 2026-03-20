<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\Models\Journal;
use App\Models\JournalType;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

//php artisan make:import JournalsImport

class JournalsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    public function model(array $row): ?Journal
    {
        // Normaliza código igual que el formulario
        $code = strtoupper(trim((string) ($row['code'] ?? '')));
        $code = preg_replace('/\s+/', '_', $code);
        $code = preg_replace('/[^A-Z0-9_\-]/', '', $code);

        if (empty($code)) return null;

        // Resuelve el journal_type_id a partir del código
        $journalType = JournalType::where('code', strtoupper(trim((string) ($row['journal_type_code'] ?? ''))))->first();
        if (!$journalType) return null;

        // updateOrCreate permite re-importar sin duplicar
        return Journal::updateOrCreate(
            ['code' => $code],
            [
                'name'            => trim((string) ($row['name'] ?? '')),
                'journal_type_id' => $journalType->id,
                'state'           => (int) ($row['state']  ?? 1) === 1,
                'active'          => (int) ($row['active'] ?? 1) === 1,
                'use_documents'   => (int) ($row['use_documents'] ?? 0) === 1,
                'bank_name'           => $row['bank_name'] ?: null,
                'bank_account_number' => $row['bank_account_number'] ?: null,
                'cci'                 => $row['cci'] ?: null,
                'document_prefix'       => $row['document_prefix'] ?: null,
                'document_next_number'  => (int) ($row['document_next_number'] ?? 1) ?: 1,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'max:190'],
            'code'              => ['required', 'string', 'max:20'],
            'journal_type_code' => ['required', 'string'],
            'state'             => ['nullable', 'in:0,1'],
            'active'            => ['nullable', 'in:0,1'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'name.required'              => 'La columna "name" es obligatoria.',
            'code.required'              => 'La columna "code" es obligatoria.',
            'code.max'                   => 'El código no debe exceder 20 caracteres.',
            'journal_type_code.required' => 'La columna "journal_type_code" es obligatoria.',
            'state.in'                   => 'El estado debe ser 0 o 1.',
        ];
    }
}

