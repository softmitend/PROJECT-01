<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MemberHistoryLookupRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => mb_strtolower(trim((string) $this->input('email'))),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
        ];
    }
}
