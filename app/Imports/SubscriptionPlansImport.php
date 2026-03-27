<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\Models\SubscriptionPlan;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;



class SubscriptionPlansImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    public function model(array $row): ?SubscriptionPlan
    {
        $name = mb_convert_case(
            mb_strtolower(trim((string) ($row['name'] ?? '')), 'UTF-8'),
            MB_CASE_TITLE,
            'UTF-8'
        );

        if (empty($name)) {
            return null;
        }

        return SubscriptionPlan::updateOrCreate(
            ['name' => $name],
            [
                'interval_count' => (int) ($row['interval_count'] ?? 1),
                'interval_unit'  => in_array($row['interval_unit'] ?? '', ['day', 'week', 'month', 'year'])
                    ? $row['interval_unit'] : 'month',
                'active' => (int) ($row['active'] ?? 1) === 1,
                'order'  => (int) ($row['order'] ?? 0),
            ]
        );
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:100'],
            'interval_count' => ['nullable', 'integer', 'min:1', 'max:365'],
            'interval_unit'  => ['nullable', 'in:day,week,month,year'],
            'active'         => ['nullable', 'in:0,1'],
            'order'          => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'name.required'           => 'La columna "name" es obligatoria.',
            'interval_count.min'      => 'El intervalo mínimo es 1.',
            'interval_unit.in'        => 'Unidad debe ser: day, week, month o year.',
        ];
    }
}
