<?php

namespace App\Services;

class TaxService
{
    protected int $precision;

    public function __construct(int $precision = 2)
    {
        $this->precision = $precision;
    }

    public function computeTaxes(float $price, float $quantity, $taxes, float $exchangeRate = 1.0): array
    {
        // 1. Convertimos a colección y ordenamos por secuencia (ISC siempre debe ser menor a IGV)
        $taxes = collect($taxes)->sortBy('sequence');

        $totalIncPercent = 0;
        $totalIncFixed = 0;

        foreach ($taxes as $tax) {
            if ($tax['price_include'] ?? false) {
                if ($tax['amount_type'] === 'percent') $totalIncPercent += ($tax['amount'] / 100);
                if ($tax['amount_type'] === 'fixed') $totalIncFixed += $tax['amount'];
            }
        }

        // Base unitaria neta
        $netUnitBase = ($price - $totalIncFixed) / (1 + $totalIncPercent);
        $netUnitBase = round($netUnitBase, $this->precision);

        $baseOriginal = $netUnitBase * $quantity;

        // 🚀 INICIALIZACIÓN CRÍTICA: Aquí evitamos el error de "Undefined variable"
        $baseAccumulated = $baseOriginal;

        $taxDetails = [];
        $totalTaxAmount = 0;

        // 2. Procesar cada impuesto (Lógica de Cascada SUNAT)
        foreach ($taxes as $tax) {
            if (!($tax['active'] ?? true)) continue;

            // 🚀 DETERMINAR LA BASE: ¿Este impuesto se calcula sobre la base original o la acumulada?
            // Si es IGV y hay ISC previo, debe usar $baseAccumulated
            $currentBase = ($tax['is_base_affected'] ?? false) ? $baseAccumulated : $baseOriginal;

            $amount = 0;
            $taxRate = floatval(($tax['amount'] ?? 0) / 100);

            if ($tax['amount_type'] === 'percent') {
                $amount = $currentBase * $taxRate;
            } elseif ($tax['amount_type'] === 'fixed') {
                $amount = ($tax['amount'] ?? 0) * $quantity;
            }

            $amount = round($amount, $this->precision);
            $totalTaxAmount += $amount;

            // 🚀 SI EL IMPUESTO AFECTA A LOS SIGUIENTES (Caso del ISC)
            if ($tax['include_base_amount'] ?? false) {
                $baseAccumulated += $amount;
            }

            $taxDetails[] = [
                'name' => $tax['name'],
                'amount' => $amount,
                'amount_base_currency' => round($amount * $exchangeRate, $this->precision),
            ];
        }

        $totalIncluded = $baseOriginal + $totalTaxAmount;

        return [
            'total_excluded' => round($baseOriginal, $this->precision),
            'total_included' => round($totalIncluded, $this->precision),
            'total_taxes'    => round($totalTaxAmount, $this->precision),
            'taxes'          => $taxDetails,
            'base_currency' => [
                'total_excluded' => round($baseOriginal * $exchangeRate, $this->precision),
                'total_included' => round($totalIncluded * $exchangeRate, $this->precision),
                'total_taxes'    => round($totalTaxAmount * $exchangeRate, $this->precision),
                'rate_used'      => $exchangeRate,
            ]
        ];
    }
}
