<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBatchRequest;
use App\Models\Batch;
use App\Models\Member;
use App\Models\MemberOrder;
use App\Models\OrderStatus;
use App\Services\StatusTransitionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $batches = Batch::query()
            ->with('currentStatus')
            ->withCount(['orders', 'orders as items_count' => fn ($query) => $query->join('order_items', 'member_orders.id', '=', 'order_items.member_order_id')])
            ->when(request('q'), fn ($query, $q) => $query->where('batch_number', 'like', "%{$q}%")->orWhere('batch_name', 'like', "%{$q}%"))
            ->when(request('status_id'), fn ($query, $statusId) => $query->where('current_status_id', $statusId))
            ->latest()
            ->paginate(15)
            ->withQueryString();
        $statuses = OrderStatus::activeFor('batch')->get();

        return view('admin.batches.index', compact('batches', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.batches.form', [
            'batch' => new Batch,
            'statuses' => OrderStatus::activeFor('batch')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBatchRequest $request, StatusTransitionService $statuses)
    {
        $batch = Batch::create($request->safe()->except('current_status_id') + [
            'is_archived' => $request->boolean('is_archived'),
        ]);

        if ($request->filled('current_status_id')) {
            $statuses->transition($batch, OrderStatus::findOrFail($request->integer('current_status_id')), $request->user(), 'Status awal batch.');
        }

        session()->flash('status', 'Batch berhasil ditambahkan.');

        return new RedirectResponse('/admin/batches/'.$batch->id, 303);
    }

    /**
     * Display the specified resource.
     */
    public function show(Batch $batch)
    {
        $batch->load(['currentStatus', 'orders.member', 'orders.overrideStatus', 'orders.items.overrideStatus']);
        $members = Member::where('is_active', true)->orderBy('display_name')->get();
        $statuses = OrderStatus::activeFor('batch')->get();
        $orderStatuses = OrderStatus::activeFor('member_order')->get();

        return view('admin.batches.show', compact('batch', 'members', 'statuses', 'orderStatuses'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Batch $batch)
    {
        return view('admin.batches.form', [
            'batch' => $batch,
            'statuses' => OrderStatus::activeFor('batch')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreBatchRequest $request, Batch $batch, StatusTransitionService $statuses)
    {
        $oldStatusId = $batch->current_status_id;
        $batch->update($request->safe()->except('current_status_id') + [
            'is_archived' => $request->boolean('is_archived'),
        ]);

        if ($request->integer('current_status_id') && $request->integer('current_status_id') !== $oldStatusId) {
            $statuses->transition($batch, OrderStatus::findOrFail($request->integer('current_status_id')), $request->user(), $request->input('status_note'));
        }

        session()->flash('status', 'Batch berhasil diperbarui.');

        return new RedirectResponse('/admin/batches/'.$batch->id, 303);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Batch $batch)
    {
        $batch->update(['is_archived' => true]);

        return back()->with('status', 'Batch diarsipkan.');
    }

    public function attachMember(Request $request, Batch $batch)
    {
        $data = $request->validate(['member_id' => ['required', 'exists:members,id']]);

        MemberOrder::firstOrCreate(
            ['member_id' => $data['member_id'], 'batch_id' => $batch->id],
            ['order_code' => 'ORD-'.$batch->batch_number.'-'.Str::upper(Str::random(6))]
        );

        return back()->with('status', 'Member ditambahkan ke batch.');
    }

    public function transition(Request $request, Batch $batch, StatusTransitionService $statuses)
    {
        $data = $request->validate([
            'status_id' => ['required', 'exists:order_statuses,id'],
            'note' => ['nullable', 'string'],
        ]);

        $statuses->transition($batch, OrderStatus::findOrFail($data['status_id']), $request->user(), $data['note'] ?? null);

        return back()->with('status', 'Status batch diperbarui.');
    }
}
