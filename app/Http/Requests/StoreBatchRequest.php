<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('access-admin') === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productsAreLocked = $this->route('batch')?->orders()->exists() ?? false;

        return [
            'batch_name' => ['nullable', 'string', 'max:255'],
            'current_status_id' => ['nullable', 'exists:order_statuses,id'],
            'description' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status_note' => ['nullable', 'string', 'max:1000'],
            'started_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'is_archived' => $this->route('batch') ? ['sometimes', 'boolean'] : ['prohibited'],
            'product_ids' => $productsAreLocked ? ['prohibited'] : ['required', 'array', 'min:1'],
            'product_ids.*' => ['integer', 'distinct', 'exists:products,id'],
        ];
    }
}
