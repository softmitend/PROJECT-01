<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberOrderRequest;
use App\Models\Batch;
use App\Models\Member;
use App\Models\MemberOrder;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Services\StatusTransitionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class MemberOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = MemberOrder::query()
            ->with(['member', 'batch.currentStatus', 'overrideStatus'])
            ->withCount('items')
            ->when(request('q'), function ($query, $q) {
                $query->where('order_code', 'like', "%{$q}%")
                    ->orWhereHas('member', fn ($query) => $query->where('display_name', 'like', "%{$q}%")->orWhere('member_code', 'like', "%{$q}%"))
                    ->orWhereHas('batch', fn ($query) => $query->where('batch_number', 'like', "%{$q}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.orders.form', $this->formData(new MemberOrder));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberOrderRequest $request, StatusTransitionService $statuses)
    {
        $order = DB::transaction(function () use ($request, $statuses) {
            $order = MemberOrder::create($request->safe()->except(['override_status_id', 'items']));
            $this->syncItems($order, $request->validated('items'), $statuses, $request->user());
            $order->update(['total_amount' => $order->items()->sum('subtotal')]);

            if ($request->filled('override_status_id')) {
                $statuses->transition($order, OrderStatus::findOrFail($request->integer('override_status_id')), $request->user(), 'Override status pesanan.');
            }

            return $order;
        });

        session()->flash('status', 'Pesanan berhasil ditambahkan.');

        return new RedirectResponse('/admin/member-orders/'.$order->id, 303);
    }

    /**
     * Display the specified resource.
     */
    public function show(MemberOrder $memberOrder)
    {
        $memberOrder->load(['member', 'batch.currentStatus', 'overrideStatus', 'items.product', 'items.overrideStatus', 'statusHistories.oldStatus', 'statusHistories.newStatus', 'statusHistories.changedBy']);

        return view('admin.orders.show', ['order' => $memberOrder]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MemberOrder $memberOrder)
    {
        $memberOrder->load('items');

        return view('admin.orders.form', $this->formData($memberOrder));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreMemberOrderRequest $request, MemberOrder $memberOrder, StatusTransitionService $statuses)
    {
        DB::transaction(function () use ($request, $memberOrder, $statuses) {
            $memberOrder->update($request->safe()->except(['override_status_id', 'items']));
            $this->syncItems($memberOrder, $request->validated('items'), $statuses, $request->user());
            $memberOrder->update(['total_amount' => $memberOrder->items()->sum('subtotal')]);

            $newStatusId = $request->integer('override_status_id') ?: null;

            if ($newStatusId && $newStatusId !== $memberOrder->override_status_id) {
                $statuses->transition($memberOrder, OrderStatus::findOrFail($newStatusId), $request->user(), 'Override status pesanan diperbarui.');
            } elseif (! $newStatusId && $memberOrder->override_status_id) {
                $statuses->clearOverride($memberOrder, $request->user());
            }
        });

        session()->flash('status', 'Pesanan berhasil diperbarui.');

        return new RedirectResponse('/admin/member-orders/'.$memberOrder->id, 303);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MemberOrder $memberOrder)
    {
        $memberOrder->delete();

        session()->flash('status', 'Pesanan dihapus.');

        return new RedirectResponse('/admin/member-orders', 303);
    }

    private function formData(MemberOrder $order): array
    {
        return [
            'order' => $order,
            'members' => Member::where('is_active', true)->orderBy('display_name')->get(),
            'batches' => Batch::where('is_archived', false)->orderByDesc('created_at')->get(),
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
            'orderStatuses' => OrderStatus::activeFor('member_order')->get(),
            'itemStatuses' => OrderStatus::activeFor('order_item')->get(),
        ];
    }

    private function syncItems(MemberOrder $order, array $items, StatusTransitionService $statuses, $user): void
    {
        $keptIds = [];

        foreach ($items as $itemData) {
            $statusId = $itemData['override_status_id'] ?? null;
            unset($itemData['override_status_id']);

            $itemData['subtotal'] = isset($itemData['unit_price'])
                ? ((int) $itemData['quantity']) * (float) $itemData['unit_price']
                : null;

            $item = $order->items()->updateOrCreate(
                ['id' => $itemData['id'] ?? null],
                $itemData
            );

            $keptIds[] = $item->id;

            if ($statusId && (int) $statusId !== $item->override_status_id) {
                $statuses->transition($item, OrderStatus::findOrFail($statusId), $user, 'Override status item.');
            } elseif (! $statusId && $item->override_status_id) {
                $statuses->clearOverride($item, $user);
            }
        }

        $order->items()->whereNotIn('id', $keptIds)->delete();
    }
}
