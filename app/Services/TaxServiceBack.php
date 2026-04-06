<?php

namespace App\Services;

class TaxService
{
    protected int $precision;

    public function __construct(int $precision = 2)
    {
        $this->precision = $precision;
    }

    public function computeTaxes(float $price, float $quantity, $taxes): array
    {
        // Ordenar por secuencia es vital para la jerarquía de base
        $taxes = collect($taxes)->sortBy('sequence');

        // 1. Extraer el valor neto real si hay impuestos incluidos
        $totalIncPercent = 0;
        $totalIncFixed = 0;
        foreach ($taxes as $tax) {
            if ($tax['price_include']) {
                if ($tax['amount_type'] === 'percent') $totalIncPercent += ($tax['amount'] / 100);
                if ($tax['amount_type'] === 'fixed') $totalIncFixed += $tax['amount'];
            }
        }

        // Base unitaria neta (fórmula Odoo)
        $netUnitBase = ($price - $totalIncFixed) / (1 + $totalIncPercent);
        $netUnitBase = round($netUnitBase, $this->precision);

        // --- VARIABLES DE CONTROL DE BASE ---
        $baseOriginal = $netUnitBase * $quantity; // La base pura sin impuestos
        $baseAccumulated = $baseOriginal;         // La base que puede ser inflada

        $taxDetails = [];
        $totalTaxAmount = 0;

        // 2. Procesar cada impuesto
        foreach ($taxes as $tax) {
            if (!($tax['active'] ?? true)) continue;

            // 🚀 VALIDACIÓN RADICAL: ¿Usamos la base original o la acumulada?
            // Si is_base_affected es true, usa la base que ya incluye impuestos anteriores (cascada)
            // Si es false, ignora los impuestos anteriores y se calcula sobre el precio neto
            // 🚀 USAMOS ?? true PARA EVITAR EL ERROR SI LA LLAVE NO EXISTE
            $currentBase = ($tax['is_base_affected'] ?? true) ? $baseAccumulated : $baseOriginal;

            $amount = 0;
            $taxRate = floatval($tax['amount'] / 100);

            if ($tax['amount_type'] === 'percent') {
                $amount = $currentBase * $taxRate;
            } elseif ($tax['amount_type'] === 'fixed') {
                $amount = $tax['amount'] * $quantity;
            } elseif ($tax['amount_type'] === 'division') {
                $amount = $currentBase / (1 - $taxRate) - $currentBase;
            }

            $amount = round($amount, $this->precision);
            $totalTaxAmount += $amount;

            // 🚀 ACTUALIZACIÓN DE BASE: Si este impuesto dice "inclúyeme en la base del siguiente"
            if ($tax['include_base_amount']) {
                $baseAccumulated += $amount;
            }

            $taxDetails[] = [
                'id' => $tax['id'] ?? null,
                'name' => $tax['name'] ?? 'Impuesto',
                'amount' => $amount,
                'base' => round($currentBase, $this->precision),
            ];
        }

        return [
            'total_excluded' => round($baseOriginal, $this->precision), // Subtotal
            'total_included' => round($baseOriginal + $totalTaxAmount, $this->precision), // Total
            'total_taxes'    => round($totalTaxAmount, $this->precision),
            'taxes'          => $taxDetails,
        ];
    }
}
