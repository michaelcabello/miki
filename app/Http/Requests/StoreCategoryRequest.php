<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         return [
            'name'               => ['required', 'string', 'max:120'],
            'description'        => ['required', 'string', 'max:10000'],
            'titlegoogle'        => ['required', 'string', 'max:120'],
            'descriptiongoogle'  => ['required', 'string', 'max:200'],
            'keywordsgoogle'     => ['nullable', 'string', 'max:255'],
            'state'              => ['nullable', 'boolean'],
            'order'              => ['nullable', 'integer', 'min:0'],
            'image'              => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'              => 'nombre',
            'description'       => 'descripción',
            'titlegoogle'       => 'título Google',
            'descriptiongoogle' => 'descripción Google',
            'keywordsgoogle'    => 'keywords Google',
            'state'             => 'estado',
            'order'             => 'orden',
            'image'             => 'imagen',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El :attribute es obligatorio.',
            'description.required' => 'La :attribute es obligatorio.',
            'titlegoogle.required' => 'El :attribute es obligatorio.',
            'descriptiongoogle.required' => 'La :attribute es obligatorio.',
            'image.image'   => 'El archivo debe ser una imagen válida.',
        ];
    }
}
