<?php

namespace App\Http\Requests;

use App\Models\OrderStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderStatusRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['prohibited'],
            'description' => ['nullable', 'string'],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'status_type' => ['required', Rule::in(OrderStatus::TYPES)],
            'scope' => ['required', Rule::in(OrderStatus::SCOPES)],
            'is_initial' => ['sometimes', 'boolean'],
            'is_final' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'locks_order_editing' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->boolean('is_initial') || ! $this->boolean('is_active', true)) {
                return;
            }

            $statusId = $this->route('order_status')?->id;

            $exists = OrderStatus::query()
                ->where('scope', $this->input('scope'))
                ->where('is_initial', true)
                ->where('is_active', true)
                ->when($statusId, fn ($query) => $query->whereKeyNot($statusId))
                ->exists();

            if ($exists) {
                $validator->errors()->add('is_initial', 'Dalam satu scope hanya boleh ada satu status awal aktif.');
            }
        });
    }
}
