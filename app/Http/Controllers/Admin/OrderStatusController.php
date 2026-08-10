<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderStatusRequest;
use App\Models\OrderStatus;
use Illuminate\Http\RedirectResponse;

class OrderStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuses = OrderStatus::query()
            ->withCount(['batches', 'memberOrders', 'orderItems', 'paymentMemberOrders', 'oldHistories', 'newHistories'])
            ->when(request('q'), function ($query, $term) {
                $query->where(fn ($query) => $query
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%"));
            })
            ->when(request()->filled('scope'), fn ($query) => $query->where('scope', request('scope')))
            ->when(request('active') === '1', fn ($query) => $query->where('is_active', true))
            ->when(request('active') === '0', fn ($query) => $query->where('is_active', false))
            ->orderBy('sequence')
            ->orderBy('name')
            ->get();

        return view('admin.statuses.index', compact('statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.statuses.form', ['orderStatus' => new OrderStatus]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderStatusRequest $request)
    {
        OrderStatus::create($request->validated() + [
            'is_initial' => $request->boolean('is_initial'),
            'is_final' => $request->boolean('is_final'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        session()->flash('status', 'Status berhasil ditambahkan.');

        return new RedirectResponse('/admin/order-statuses', 303);
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderStatus $orderStatus)
    {
        return new RedirectResponse('/admin/order-statuses/'.$orderStatus->id.'/edit', 303);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrderStatus $orderStatus)
    {
        return view('admin.statuses.form', compact('orderStatus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreOrderStatusRequest $request, OrderStatus $orderStatus)
    {
        $orderStatus->update($request->validated() + [
            'is_initial' => $request->boolean('is_initial'),
            'is_final' => $request->boolean('is_final'),
            'is_active' => $request->boolean('is_active'),
        ]);

        session()->flash('status', 'Status berhasil diperbarui.');

        return new RedirectResponse('/admin/order-statuses', 303);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderStatus $orderStatus)
    {
        if ($orderStatus->usedCount() > 0) {
            $orderStatus->update(['is_active' => false]);

            return back()->with('status', 'Status sudah digunakan, jadi dinonaktifkan.');
        }

        $orderStatus->delete();

        return back()->with('status', 'Status dihapus.');
    }
}
