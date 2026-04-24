<?php

namespace App\Livewire\Admin\Stock;

use App\Models\StockPicking;
use App\Services\Stock\StockService;
use Livewire\Component;

class StockPickingEdit extends Component
{
    public StockPicking $picking;
    public $lines = []; // Líneas de StockMove


    public function mount($id)
    {
        $this->picking = StockPicking::with(['moveLines.productVariant.template', 'partner', 'locationTo'])
            ->findOrFail($id);



        $this->lines = $this->picking->moveLines->map(fn($move) => [
            'id' => $move->id,
            'product_name' => $move->productVariant->template->name,
            'qty_demand' => $move->qty_demand,
            // 🚀 Si qty_done es 0 o null, sugerimos la demanda (estilo Odoo)
            'qty_done' => ($move->qty_done > 0) ? $move->qty_done : $move->qty_demand,
        ])->toArray();
    }

    // StockPickingEdit.php
    public function getStatusColorProperty()
    {
        return [
            'draft'     => 'bg-gray-100 text-gray-600',
            'confirmed' => 'bg-blue-100 text-blue-600',
            'assigned'  => 'bg-yellow-100 text-yellow-600',
            'done'      => 'bg-green-100 text-green-600', // ✅ El verde indica que ya se recibió
            'cancel'    => 'bg-red-100 text-red-600',
        ][$this->picking->state] ?? 'bg-gray-100 text-gray-600';
    }


    public function render()
    {
        return view('livewire.admin.stock.stock-picking-edit');
    }



    public function validatePickingBackBack()
    {
        try {
            $service = app(StockService::class);

            // 1. Ejecutamos la lógica
            $service->validateReception($this->picking, $this->lines);

            // 2. 💡 Mensaje de éxito persistente
            session()->flash('success', "Inventario de {$this->picking->name} validado correctamente.");

            // 3. 🚀 REDIRECCIÓN VITAL
            return redirect()->route('purchase.order.edit', $this->picking->purchase_order_id);
        } catch (\Throwable $e) {
            // Si hay un error de SQL (ej. columna mal escrita), aquí lo verás
            $this->dispatch('show-swal', [
                'icon' => 'error',
                'title' => 'Error en Validación',
                'text' => "Mensaje: " . $e->getMessage()
            ]);
        }
    }


    public function validatePicking()
    {
        try {
            $service = app(StockService::class);
            $service->validateReception($this->picking, $this->lines);

            session()->flash('success', "Inventario validado correctamente.");
            return redirect()->route('purchase.order.edit', $this->picking->purchase_order_id);
        } catch (\Exception $e) {
            // 🚨 ESTO ES LO MÁS IMPORTANTE: Ver el error real de SQL
            $this->dispatch('show-swal', [
                'icon'  => 'error',
                'title' => 'Fallo técnico',
                'text'  => $e->getMessage(), // <--- Esto te dirá qué columna falta
            ]);
        }
    }
}
