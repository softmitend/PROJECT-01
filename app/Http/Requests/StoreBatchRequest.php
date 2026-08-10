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
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $batchId = $this->route('batch')?->id;

        return [
            'batch_number' => ['required', 'string', 'max:255', 'unique:batches,batch_number,'.$batchId],
            'batch_name' => ['nullable', 'string', 'max:255'],
            'current_status_id' => ['nullable', 'exists:order_statuses,id'],
            'description' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'started_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'is_archived' => ['sometimes', 'boolean'],
        ];
    }
}
