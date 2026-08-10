<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMemberOrderRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $rawItems = collect($this->input('items', []))
            ->filter(fn ($item) => filled($item['product_id'] ?? null) || filled($item['item_name'] ?? null));

        $products = Product::query()
            ->whereIn('id', $rawItems->pluck('product_id')->filter()->unique())
            ->get()
            ->keyBy('id');

        $items = $rawItems
            ->map(function (array $item) use ($products): array {
                $product = $products->get($item['product_id'] ?? null);

                if ($product) {
                    $item['item_name'] = $product->name;
                    $item['variant'] = $product->variant;
                    $item['unit_price'] = filled($item['unit_price'] ?? null)
                        ? $item['unit_price']
                        : $product->default_price;
                }

                return $item;
            })
            ->values()
            ->all();

        $this->merge(['items' => $items]);
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
        $orderId = $this->route('member_order')?->id;
        $currentPaymentStatusId = $this->route('member_order')?->payment_status_id;

        return [
            'order_code' => ['required', 'string', 'max:255', 'unique:member_orders,order_code,'.$orderId],
            'member_id' => ['required', 'exists:members,id'],
            'batch_id' => ['required', 'exists:batches,id'],
            'override_status_id' => ['nullable', 'exists:order_statuses,id'],
            'payment_status_id' => [
                'nullable',
                Rule::exists('order_statuses', 'id')->where(fn ($query) => $query
                    ->where('scope', 'payment')
                    ->where(fn ($query) => $query
                        ->where('is_active', true)
                        ->when($currentPaymentStatusId, fn ($query, $statusId) => $query->orWhere('id', $statusId)))),
            ],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'exists:order_items,id'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.variant' => ['nullable', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.override_status_id' => ['nullable', 'exists:order_statuses,id'],
            'items.*.notes' => ['nullable', 'string'],
        ];
    }
}
