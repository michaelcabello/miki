<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePartnerRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'company_type_id'   => ['nullable', 'exists:company_types,id'],
            'document_type_id'  => ['nullable', 'exists:document_types,id'],
            'document_number'   => ['nullable', 'string', 'max:20'],

            'order' => ['nullable', 'integer', 'min:0'],

            'image' => ['nullable', 'string', 'max:2048'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'website' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'youtube' => ['nullable', 'string', 'max:255'],
            'tiktok' => ['nullable', 'string', 'max:255'],

            'street' => ['nullable', 'string', 'max:255'],
            'street2' => ['nullable', 'string', 'max:255'],
            'zip' => ['nullable', 'string', 'max:10'],
            'map' => ['nullable', 'string', 'max:255'],

            'department_id' => ['nullable', 'exists:departments,id'],
            'province_id'   => ['nullable', 'exists:provinces,id'],
            'district_id'   => ['nullable', 'exists:districts,id'],

            'is_customer' => ['sometimes', 'boolean'],
            'is_supplier' => ['sometimes', 'boolean'],
            'status'      => ['sometimes', 'boolean'],

            'pricelist_id' => ['nullable', 'exists:pricelists,id'],
            'currency_id'  => ['nullable', 'exists:currencies,id'],

            'bank_account' => ['nullable', 'string', 'max:255'],

            'portal_access' => ['sometimes', 'boolean'],
            'portal_enabled_at' => ['nullable', 'date'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_customer' => (bool) $this->input('is_customer', false),
            'is_supplier' => (bool) $this->input('is_supplier', false),
            'status'      => (bool) $this->input('status', true),
            'portal_access' => (bool) $this->input('portal_access', false),
        ]);
    }
}
