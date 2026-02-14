<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

//php artisan make:request StoreBrandRequest
class StoreBrandRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:brands,name',
            'slug' => 'nullable|string|max:255|unique:brands,slug',
            'state' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'order' => 'nullable|integer|min:0',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'keywords' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la marca es obligatorio',
            'name.unique' => 'Ya existe una marca con este nombre',
            'name.max' => 'El nombre no puede exceder los 255 caracteres',
            'slug.unique' => 'Este slug ya está en uso',
            'image.image' => 'El archivo debe ser una imagen',
            'image.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif o webp',
            'image.max' => 'La imagen no puede superar los 2MB',
            'order.integer' => 'El orden debe ser un número entero',
            'order.min' => 'El orden debe ser un número positivo',
            'title.max' => 'El título SEO no puede exceder los 255 caracteres',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Convertir el checkbox de estado a booleano
        $this->merge([
            'state' => $this->has('state') ? true : false,
        ]);
    }
}
