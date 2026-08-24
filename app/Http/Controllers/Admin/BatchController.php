<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBatchRequest;
use App\Models\Batch;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Services\StatusTransitionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'view' => ['nullable', 'in:active,archived'],
            'q' => ['nullable', 'string', 'max:255'],
            'status_id' => ['nullable', 'integer', 'exists:order_statuses,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);
        $archiveView = ($filters['view'] ?? null) === 'archived' ? 'archived' : 'active';
        $batches = Batch::query()
            ->with('currentStatus')
            ->withCount(['orders', 'orders as items_count' => fn ($query) => $query->join('order_items', 'member_orders.id', '=', 'order_items.member_order_id')])
            ->where('is_archived', $archiveView === 'archived')
            ->when($filters['q'] ?? null, fn ($query, $q) => $query->where(fn ($query) => $query
                ->where('batch_number', 'like', "%{$q}%")
                ->orWhere('batch_name', 'like', "%{$q}%")))
            ->when($filters['status_id'] ?? null, fn ($query, $statusId) => $query->where('current_status_id', $statusId))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate(15)
            ->withQueryString();
        $statuses = OrderStatus::activeFor('batch')->get();
        $batchCounts = [
            'active' => Batch::where('is_archived', false)->count(),
            'archived' => Batch::where('is_archived', true)->count(),
        ];

        return view('admin.batches.index', compact('batches', 'statuses', 'archiveView', 'batchCounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.batches.form', [
            'batch' => new Batch,
            'statuses' => OrderStatus::activeFor('batch')->get(),
            'products' => Product::where('is_active', true)->orderBy('name')->orderBy('variant')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBatchRequest $request, StatusTransitionService $statuses)
    {
        $batch = DB::transaction(function () use ($request, $statuses) {
            $batch = Batch::create($request->safe()->except(['current_status_id', 'product_ids', 'status_note', 'is_archived']) + [
                'batch_number' => $this->generateBatchNumber(),
                'is_archived' => false,
            ]);
            $batch->products()->sync($request->validated('product_ids'));

            if ($request->filled('current_status_id')) {
                $statuses->transition($batch, OrderStatus::findOrFail($request->integer('current_status_id')), $request->user(), 'Status awal batch.');
            }

            return $batch;
        });

        session()->flash('status', 'Batch berhasil ditambahkan.');

        return new RedirectResponse('/admin/batches/'.$batch->id, 303);
    }

    /**
     * Display the specified resource.
     */
    public function show(Batch $batch)
    {
        $batch->load([
            'currentStatus',
            'products',
            'orders.member',
            'orders.overrideStatus',
            'orders.items.overrideStatus',
            'statusHistories.oldStatus',
            'statusHistories.newStatus',
            'statusHistories.changedBy',
        ]);
        $statuses = OrderStatus::activeFor('batch')->get();

        return view('admin.batches.show', compact('batch', 'statuses'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Batch $batch)
    {
        if ($batch->is_archived) {
            return redirect()->route('admin.batches.show', $batch)->withErrors([
                'batch' => 'Batch yang sudah diarsipkan tidak dapat diedit lagi.',
            ]);
        }

        $batch->load(['products', 'currentStatus']);
        $existingProductIds = $batch->products->pluck('id');

        return view('admin.batches.form', [
            'batch' => $batch,
            'statuses' => OrderStatus::activeFor('batch')->get(),
            'products' => Product::query()
                ->where(fn ($query) => $query->where('is_active', true)->orWhereIn('id', $existingProductIds))
                ->orderBy('name')
                ->orderBy('variant')
                ->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreBatchRequest $request, Batch $batch, StatusTransitionService $statuses)
    {
        if ($batch->is_archived) {
            return redirect()->route('admin.batches.show', $batch)->withErrors([
                'batch' => 'Batch yang sudah diarsipkan tidak dapat diedit lagi.',
            ]);
        }

        $batch->loadMissing('currentStatus');
        if ($batch->progress_locked && $request->integer('current_status_id') !== (int) $batch->current_status_id) {
            return back()->withInput()->withErrors([
                'current_status_id' => 'Progress batch sudah final dan tidak dapat diubah lagi.',
            ]);
        }

        $oldStatusId = $batch->current_status_id;
        DB::transaction(function () use ($request, $batch, $statuses, $oldStatusId) {
            $batch->update($request->safe()->except(['current_status_id', 'product_ids', 'status_note']) + [
                'is_archived' => $request->boolean('is_archived'),
            ]);

            if (! $batch->orders()->exists()) {
                $batch->products()->sync($request->validated('product_ids'));
            }

            if ($request->integer('current_status_id') && $request->integer('current_status_id') !== $oldStatusId) {
                $statuses->transition($batch, OrderStatus::findOrFail($request->integer('current_status_id')), $request->user(), $request->input('status_note'));
            }
        });

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

    public function transition(Request $request, Batch $batch, StatusTransitionService $statuses)
    {
        abort_unless($request->user()?->can('access-admin'), 403);

        if ($batch->is_archived) {
            return back()->withErrors([
                'status_id' => 'Progress batch arsip tidak dapat diubah lagi.',
            ]);
        }

        $batch->loadMissing('currentStatus');
        if ($batch->progress_locked) {
            return back()->withErrors([
                'status_id' => 'Progress batch sudah final dan tidak dapat diubah lagi.',
            ]);
        }

        $data = $request->validate([
            'status_id' => ['required', 'exists:order_statuses,id'],
            'note' => ['nullable', 'string'],
        ]);

        $statuses->transition($batch, OrderStatus::findOrFail($data['status_id']), $request->user(), $data['note'] ?? null);

        return back()->with('status', 'Status batch diperbarui.');
    }

    private function generateBatchNumber(): string
    {
        $prefix = 'BTH-'.now()->format('ym').'-';
        $latestNumber = Batch::query()
            ->where('batch_number', 'like', $prefix.'%')
            ->orderByDesc('batch_number')
            ->value('batch_number');

        $sequence = $latestNumber && preg_match('/(\d{4})$/', $latestNumber, $matches)
            ? ((int) $matches[1]) + 1
            : 1;

        do {
            $batchNumber = $prefix.str_pad((string) $sequence++, 4, '0', STR_PAD_LEFT);
        } while (Batch::where('batch_number', $batchNumber)->exists());

        return $batchNumber;
    }
}
