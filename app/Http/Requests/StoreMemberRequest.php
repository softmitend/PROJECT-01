<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => mb_strtolower(trim((string) $this->input('email'))),
            'phone' => trim((string) $this->input('phone')),
        ]);
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $memberId = $this->route('member')?->id;

        return [
            'display_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:members,email,'.$memberId],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:2000'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
