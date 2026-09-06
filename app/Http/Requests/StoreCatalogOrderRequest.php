<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCatalogOrderRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->filled('customer_username')) {
            $this->merge([
                'customer_username' => mb_strtolower(ltrim(trim((string) $this->input('customer_username')), '@')),
            ]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_username' => [Rule::requiredIf(! $this->user()?->member_id), 'nullable', 'string', 'max:100'],
            'payment_type' => ['required', Rule::in(['dp', 'full'])],
            'payment_proof' => ['required', 'image', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Pilih minimal satu variasi barang.',
            'items.min' => 'Pilih minimal satu variasi barang.',
            'payment_proof.required' => 'Upload bukti pembayaran sebelum menekan tombol bayar.',
            'payment_proof.image' => 'Bukti pembayaran harus berupa file gambar.',
        ];
    }
}
