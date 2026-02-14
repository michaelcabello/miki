<?php

namespace App\Imports;

use App\Models\Brand;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\DB;

class BrandsImport implements ToCollection, WithHeadingRow, WithValidation
{

    /**
     * Procesar cada fila del Excel
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                DB::beginTransaction();

                // Normalizar el estado
                $state = $this->normalizeState($row['estado'] ?? null);

                // Crear o actualizar marca
                Brand::updateOrCreate(
                    ['name' => $row['nombre']], // Buscar por nombre
                    [
                        'slug' => $row['slug'] ?? Str::slug($row['nombre']),
                        'state' => $state,
                        'order' => $row['orden'] ?? 0,
                        'title' => $row['titulo_seo'] ?? null,
                        'description' => $row['descripcion_seo'] ?? null,
                        'keywords' => $row['keywords'] ?? null,
                        // Nota: La imagen debe manejarse manualmente desde AWS S3
                        // No se importa automáticamente desde Excel
                    ]
                );

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                // Log del error (opcional)
                \Log::error('Error importando marca: ' . $e->getMessage(), [
                    'row' => $row->toArray()
                ]);

                // Continuar con la siguiente fila
                continue;
            }
        }
    }

    /**
     * Normalizar el valor del estado
     */
    private function normalizeState($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            $value = strtolower(trim($value));

            // Valores que se consideran "activo"
            $activeValues = ['activo', 'active', 'si', 'sí', 'yes', '1', 'true'];

            return in_array($value, $activeValues);
        }

        if (is_numeric($value)) {
            return (bool) $value;
        }

        return false; // Default: inactivo
    }

    /**
     * Reglas de validación para cada fila
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'estado' => 'nullable',
            'orden' => 'nullable|integer|min:0',
            'titulo_seo' => 'nullable|string|max:255',
            'descripcion_seo' => 'nullable|string',
            'keywords' => 'nullable|string',
        ];
    }

    /**
     * Mensajes de error personalizados
     */
    public function customValidationMessages()
    {
        return [
            'nombre.required' => 'El nombre de la marca es obligatorio',
            'nombre.max' => 'El nombre no puede exceder los 255 caracteres',
            'orden.integer' => 'El orden debe ser un número entero',
            'orden.min' => 'El orden debe ser un número positivo',
        ];
    }

    /**
     * Especificar la fila donde comienzan los encabezados
     */
    public function headingRow(): int
    {
        return 1;
    }

    /* public function model(array $row)
    {
        return new Brand([

        ]);
    } */
}
