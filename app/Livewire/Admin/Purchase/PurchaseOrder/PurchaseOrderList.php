<?php

namespace App\Livewire\Admin\Purchase\PurchaseOrder;

use App\Models\PurchaseOrder;
use App\Services\Purchase\PurchaseOrderService;
use Livewire\Component;
use Livewire\WithPagination;
use App\Mail\Orders\PurchaseOrderMail;
use Illuminate\Support\Facades\Mail;

class PurchaseOrderList extends Component
{
    use WithPagination;

    public $search = '';

    public function sendEmailBack($id, PurchaseOrderService $service)
    {
        // 1. Buscamos la orden con sus relaciones necesarias
        $order = PurchaseOrder::with(['partner', 'currency'])->findOrFail($id);

        // 2. Validación de correo
        if (!$order->partner || !$order->partner->email) {
            $this->dispatch('show-swal', icon: 'error', text: 'El proveedor no tiene correo configurado.');
            return;
        }

        try {
            // 3. Generamos la URL firmada (Válida por 15 minutos)
            $url = $service->getSecureUrl($order);

            if (!$url) {
                $this->dispatch('show-swal', icon: 'error', text: 'Error: No se encontró la ruta del PDF en S3.');
                return;
            }

            // 4. 🚀 ENVIAR EL CORREO: Pasamos ($order, $url)
            \Illuminate\Support\Facades\Mail::to($order->partner->email)
                ->send(new \App\Mail\Orders\PurchaseOrderMail($order, $url));

            $this->dispatch('show-swal', icon: 'success', text: "Correo enviado a {$order->partner->email}");
        } catch (\Exception $e) {
            // Si algo falla (conexión S3 o SMTP), lo capturamos aquí
            \Illuminate\Support\Facades\Log::error("Error al enviar correo TICOM: " . $e->getMessage());
            $this->dispatch('show-swal', icon: 'error', text: 'Fallo crítico: ' . $e->getMessage());
        }
    }


    public function sendEmail($id, PurchaseOrderService $service)
    {
        $order = PurchaseOrder::with(['partner', 'currency'])->findOrFail($id);

        if (!$order->partner->email) {
            $this->dispatch('show-swal', icon: 'error', text: 'El proveedor no tiene correo.');
            return;
        }

        try {
            // 1. Generar URL privada (15 min)
            $url = $service->getSecureUrl($order);

            if (!$url) {
                $this->dispatch('show-swal', icon: 'error', text: 'Error: No hay ruta de PDF.');
                return;
            }

            // 2. Enviar Correo (Pasando Orden y URL)
            \Illuminate\Support\Facades\Mail::to($order->partner->email)
                ->send(new \App\Mail\Orders\PurchaseOrderMail($order, $url));

            $this->dispatch('show-swal', icon: 'success', text: "Correo enviado a {$order->partner->email}");
        } catch (\Exception $e) {
            // 🚀 TIP DE DEBUG: Si vuelve a fallar, revisa este log
            \Log::error("Fallo envío TICOM: " . $e->getMessage());
            $this->dispatch('show-swal', icon: 'error', text: 'Error técnico: ' . $e->getMessage());
        }
    }



    // 🚀 Acción: Ver/Descargar PDF
    public function viewPdf($id, PurchaseOrderService $service)
    {
        $order = PurchaseOrder::findOrFail($id);
        if ($order->pdf_path) {
            return redirect()->away($service->getSecureUrl($order));
        }
        $this->dispatch('show-swal', ['icon' => 'error', 'text' => 'PDF no encontrado']);
    }

    // 🚀 Acción: WhatsApp (Genera link con URL temporal)
    public function sendWhatsApp($id, PurchaseOrderService $service)
    {
        $order = PurchaseOrder::with('partner')->findOrFail($id);
        $tempUrl = $service->getSecureUrl($order);

        $phone = $order->partner->phone ?? ''; // Asegúrate de tener este campo
        $message = urlencode("Hola {$order->partner->name}, adjunto la solicitud de cotización {$order->name}: {$tempUrl}");

        $whatsappUrl = "https://wa.me/{$phone}?text={$message}";

        return redirect()->away($whatsappUrl);
    }

    public function render()
    {
        $orders = PurchaseOrder::with(['partner', 'currency'])
            ->where('name', 'like', "%{$this->search}%")
            ->orWhereHas('partner', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.admin.purchase.purchase-order.purchase-order-list', compact('orders'));
    }
}
