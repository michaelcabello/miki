<?php

namespace Database\Seeders;

use App\Models\ComprobanteType;
use App\Models\DocumentSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class DocumentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // 1. Crear o actualizar el tipo de comprobante para Solicitudes de Cotización (RFQ)
            $rfqType = ComprobanteType::updateOrCreate(
                ['code' => 'RFQ'],
                [
                    'name'  => 'SOLICITUD DE COTIZACIÓN',
                    'state' => true,
                ]
            );

            // 2. Plantilla A4 (Indigo Modern)
            DocumentSetting::updateOrCreate(
                [
                    'comprobante_type_id' => $rfqType->id,
                    'template_name'       => 'Indigo Modern (Odoo Style)',
                ],
                [
                    'blade_path'    => 'admin.pdf.purchase-order',
                    'paper_size'    => 'A4',
                    'primary_color' => '#4f46e5',
                    'order'         => 1,
                    'activate'      => true, // Esta queda como predeterminada
                ]
            );

            // 3. 🚀 NUEVA PLANTILLA: Ticket Térmico 80mm
            DocumentSetting::updateOrCreate(
                [
                    'comprobante_type_id' => $rfqType->id,
                    'template_name'       => 'Ticket Térmico 80mm',
                ],
                [
                    'blade_path'    => 'admin.pdf.templates.purchase_order.ticket',
                    'paper_size'    => '80mm',
                    'primary_color' => '#000000', // Negro para ticketeras térmicas
                    'order'         => 2,
                    'activate'      => false, // El usuario la activará desde el panel
                ]
            );
        });

        $this->command->info('✅ Seeder de comprobantes y plantillas actualizado con éxito.');
    }
}
