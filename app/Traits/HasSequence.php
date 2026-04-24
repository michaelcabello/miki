<?php

namespace App\Traits;

use App\Models\Company;
use Illuminate\Support\Str;

trait HasSequence
{
    /**
     * Genera o transforma la secuencia del documento.
     * Ejemplo: RFQ0001 -> P0001
     */
    public function convertToPurchaseOrder(): void
    {
        if (Str::startsWith($this->name, 'RFQ')) {
            $number = Str::after($this->name, 'RFQ');
            $this->name = 'P' . $number;
        }
    }

    public function getPrecision(): int
    {
        return cache()->remember('company_precision', 3600, fn() =>
            Company::first()?->decimal_purchase ?? 2
        );
    }
}
