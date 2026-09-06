<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCatalogOrderRequest;
use App\Models\Batch;
use App\Models\Member;
use App\Models\MemberOrder;
use App\Models\OrderStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class CatalogController extends Controller
{
    public function index()
    {
        $catalogBatches = Batch::query()
            ->where('is_catalog_visible', true)
            ->where('is_archived', false)
            ->with(['products' => fn ($query) => $query
                ->where('products.is_active', true)
                ->wherePivot('is_available', true)])
            ->orderByRaw('ordering_deadline is null')
            ->orderBy('ordering_deadline')
            ->latest('created_at')
            ->paginate(12);

        return view('catalog.index', compact('catalogBatches'));
    }

    public function show(Batch $batch)
    {
        abort_unless($batch->is_catalog_visible && ! $batch->is_archived, 404);

        $batch->load([
            'products' => fn ($query) => $query
                ->where('products.is_active', true)
                ->wherePivot('is_available', true),
        ]);

        $customer = request()->user()?->member;

        return view('catalog.show', compact('batch', 'customer'));
    }

    public function store(StoreCatalogOrderRequest $request, Batch $batch): RedirectResponse
    {
        $batch->loadMissing(['currentStatus', 'products', 'orders.paymentStatus']);

        if (! $batch->catalog_is_open) {
            throw ValidationException::withMessages([
                'catalog' => 'Pemesanan untuk batch ini sudah ditutup.',
            ]);
        }

        if (! $batch->qris_image_path) {
            throw ValidationException::withMessages([
                'payment_proof' => 'QRIS untuk batch ini belum tersedia. Hubungi admin sebelum melakukan pembayaran.',
            ]);
        }

        $requestedItems = collect($request->validated('items'))->keyBy('product_id');
        $availableProducts = $batch->products
            ->filter(fn ($product) => $product->is_active && $product->pivot->is_available)
            ->whereIn('id', $requestedItems->keys())
            ->keyBy('id');

        if ($availableProducts->count() !== $requestedItems->count()) {
            throw ValidationException::withMessages([
                'items' => 'Salah satu variasi sudah tidak tersedia. Muat ulang halaman dan pilih kembali.',
            ]);
        }

        $selection = $availableProducts->map(function ($product) use ($requestedItems): array {
            $quantity = (int) $requestedItems[$product->id]['quantity'];

            return [
                'product' => $product,
                'quantity' => $quantity,
                'dp_subtotal' => $quantity * (float) $product->pivot->dp_price,
                'full_subtotal' => $quantity * (float) $product->pivot->full_price,
            ];
        });

        $fullTotal = $selection->sum('full_subtotal');
        $paymentAmount = $request->validated('payment_type') === 'dp'
            ? $selection->sum('dp_subtotal')
            : $fullTotal;
        $paymentStatus = OrderStatus::query()
            ->where('scope', 'payment')
            ->where('code', $request->validated('payment_type') === 'dp' ? 'menunggu-pelunasan' : 'lunas')
            ->firstOrFail();

        $proofPath = $request->file('payment_proof')->store('catalog/payment-proofs');
        $oldProofPath = null;

        try {
            $order = DB::transaction(function () use ($request, $batch, $selection, $fullTotal, $paymentAmount, $paymentStatus, $proofPath, &$oldProofPath) {
                $authenticatedMember = $request->user()?->member;
                $member = $authenticatedMember
                    ?: Member::query()->firstOrNew([
                        'username' => $request->validated('customer_username'),
                    ]);

                if (! $member->exists) {
                    $member->member_code = 'CUS-'.Str::upper(Str::random(12));
                }

                $memberData = [
                    'display_name' => trim($request->validated('customer_name')),
                    'is_active' => true,
                ];
                if (! $authenticatedMember) {
                    $memberData['username'] = $request->validated('customer_username');
                }
                $member->fill($memberData)->save();

                $order = MemberOrder::query()->firstOrNew([
                    'member_id' => $member->id,
                    'batch_id' => $batch->id,
                ]);

                if ($order->exists) {
                    $order->loadMissing(['batch.currentStatus', 'paymentStatus', 'overrideStatus']);

                    if ($order->batch->orders_locked || $order->is_refunded) {
                        throw ValidationException::withMessages([
                            'catalog' => 'Pesanan ini sudah dikunci dan tidak dapat diubah lagi.',
                        ]);
                    }

                    if ($order->paymentStatus?->code === 'lunas' && $request->validated('payment_type') === 'dp') {
                        throw ValidationException::withMessages([
                            'payment_type' => 'Pesanan ini sudah lunas dan tidak dapat dikembalikan menjadi pembayaran DP.',
                        ]);
                    }

                    $oldProofPath = $order->payment_proof_path;
                } else {
                    $order->order_code = 'TMP-'.Str::uuid();
                }

                $order->fill([
                    'order_source' => 'catalog',
                    'payment_type' => $request->validated('payment_type'),
                    'payment_amount' => $paymentAmount,
                    'payment_proof_path' => $proofPath,
                    'payment_submitted_at' => now(),
                    'payment_status_id' => $paymentStatus->id,
                    'total_amount' => $fullTotal,
                    'notes' => $request->validated('notes'),
                ])->save();

                if (str_starts_with($order->order_code, 'TMP-')) {
                    $order->forceFill([
                        'order_code' => 'ORD-'.now()->format('ym').'-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
                    ])->save();
                }

                $order->items()->delete();
                foreach ($selection as $selected) {
                    $product = $selected['product'];
                    $order->items()->create([
                        'product_id' => $product->id,
                        'item_name' => $product->name,
                        'variant' => $product->variant,
                        'quantity' => $selected['quantity'],
                        'unit_price' => $product->pivot->full_price,
                        'subtotal' => $selected['full_subtotal'],
                    ]);
                }

                return $order;
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($proofPath);
            throw $exception;
        }

        if ($oldProofPath && $oldProofPath !== $proofPath) {
            Storage::disk('local')->delete($oldProofPath);
        }

        return redirect()
            ->route('catalog.show', $batch)
            ->with('status', 'Pembayaran berhasil dikirim ke admin.')
            ->with('catalog_order_code', $order->order_code);
    }
}
