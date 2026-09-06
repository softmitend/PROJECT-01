<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
            'content' => ['required', 'string', 'min:10', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Pilih rating untuk jajananmu.',
            'rating.between' => 'Rating harus berada di antara 1 sampai 5.',
            'content.required' => 'Ceritakan sedikit pengalaman jajananmu.',
            'content.min' => 'Cerita testimoni minimal 10 karakter.',
            'content.max' => 'Cerita testimoni maksimal 1.000 karakter.',
            'photo.image' => 'Foto testimoni harus berupa gambar.',
            'photo.mimes' => 'Gunakan foto JPG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran foto maksimal 4 MB.',
        ];
    }
}
