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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

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
        return view('admin.batches.form', $this->formData(new Batch));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBatchRequest $request, StatusTransitionService $statuses)
    {
        $catalogImageDisk = $this->catalogUploadDisk();
        $newCatalogImagePath = $request->hasFile('catalog_image')
            ? $this->storeVerifiedCatalogImage($request->file('catalog_image'), $catalogImageDisk)
            : null;

        try {
            $batch = DB::transaction(function () use ($request, $statuses, $newCatalogImagePath, $catalogImageDisk) {
                $data = $request->safe()->except([
                    'current_status_id', 'status_note', 'is_archived', 'catalog_image', 'remove_catalog_image', 'qris_image', 'variants',
                ]);

                if ($newCatalogImagePath) {
                    $data['catalog_image_path'] = $newCatalogImagePath;
                    $data['catalog_image_disk'] = $catalogImageDisk;
                }
                if ($request->hasFile('qris_image')) {
                    $data['qris_image_path'] = $request->file('qris_image')->store('catalog/qris', 'public');
                }

                $batch = Batch::create($data + [
                    'batch_number' => $this->generateBatchNumber(),
                    'is_catalog_visible' => $request->boolean('is_catalog_visible'),
                    'is_archived' => false,
                ]);

                $this->syncCatalogVariants($batch, $request->validated('variants', []));

                if ($request->filled('current_status_id')) {
                    $statuses->transition($batch, OrderStatus::findOrFail($request->integer('current_status_id')), $request->user(), 'Status awal batch.');
                }

                return $batch;
            });
        } catch (Throwable $exception) {
            if ($newCatalogImagePath) {
                Storage::disk($catalogImageDisk)->delete($newCatalogImagePath);
            }

            throw $exception;
        }

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
            'orders.member',
            'orders.overrideStatus',
            'orders.paymentStatus',
            'orders.items.overrideStatus',
            'products',
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

        return view('admin.batches.form', $this->formData($batch));
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
        $oldCatalogImagePath = $batch->catalog_image_path;
        $oldCatalogImageDisk = $batch->catalog_image_disk ?: 'public';
        $catalogImageDisk = $this->catalogUploadDisk();
        $newCatalogImagePath = $request->hasFile('catalog_image')
            ? $this->storeVerifiedCatalogImage($request->file('catalog_image'), $catalogImageDisk)
            : null;
        $removeCatalogImage = $request->boolean('remove_catalog_image');

        try {
            DB::transaction(function () use ($request, $batch, $statuses, $oldStatusId, $newCatalogImagePath, $removeCatalogImage, $catalogImageDisk) {
                $data = $request->safe()->except([
                    'current_status_id', 'status_note', 'catalog_image', 'remove_catalog_image', 'qris_image', 'variants',
                ]);

                if ($newCatalogImagePath) {
                    $data['catalog_image_path'] = $newCatalogImagePath;
                    $data['catalog_image_disk'] = $catalogImageDisk;
                } elseif ($removeCatalogImage) {
                    $data['catalog_image_path'] = null;
                    $data['catalog_image_disk'] = null;
                }
                if ($request->hasFile('qris_image')) {
                    $data['qris_image_path'] = $request->file('qris_image')->store('catalog/qris', 'public');
                }

                $batch->update($data + [
                    'is_catalog_visible' => $request->boolean('is_catalog_visible'),
                    'is_archived' => $request->boolean('is_archived'),
                ]);

                $this->syncCatalogVariants($batch, $request->validated('variants', []));

                if ($request->integer('current_status_id') && $request->integer('current_status_id') !== $oldStatusId) {
                    $statuses->transition($batch, OrderStatus::findOrFail($request->integer('current_status_id')), $request->user(), $request->input('status_note'));
                }
            });
        } catch (Throwable $exception) {
            if ($newCatalogImagePath) {
                Storage::disk($catalogImageDisk)->delete($newCatalogImagePath);
            }

            throw $exception;
        }

        if ($oldCatalogImagePath && ($newCatalogImagePath || $removeCatalogImage)) {
            Storage::disk($oldCatalogImageDisk)->delete($oldCatalogImagePath);
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

    private function formData(Batch $batch): array
    {
        $batch->loadMissing(['currentStatus', 'products']);

        return [
            'batch' => $batch,
            'statuses' => OrderStatus::activeFor('batch')->get(),
        ];
    }

    private function syncCatalogVariants(Batch $batch, array $variants): void
    {
        $existingProductIds = $batch->products()->pluck('products.id')->map(fn ($id) => (int) $id)->all();
        $sync = [];

        foreach (array_values($variants) as $index => $variant) {
            $product = null;

            if (! empty($variant['product_id']) && in_array((int) $variant['product_id'], $existingProductIds, true)) {
                $product = Product::find((int) $variant['product_id']);
            }

            $product ??= new Product;
            $product->fill([
                'name' => $batch->batch_name ?: $batch->batch_number,
                'variant' => trim($variant['name']),
                'default_price' => $variant['full_price'],
                'description' => $batch->description,
                'is_active' => true,
            ])->save();

            $sync[$product->id] = [
                'dp_price' => $variant['dp_price'],
                'full_price' => $variant['full_price'],
                'sort_order' => $index,
                'is_available' => filter_var($variant['is_available'] ?? true, FILTER_VALIDATE_BOOL),
            ];
        }

        $batch->products()->sync($sync);
    }

    private function storeVerifiedCatalogImage(UploadedFile $file, string $disk): string
    {
        $mime = strtolower((string) ($file->getMimeType() ?: ''));
        $extension = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => throw new RuntimeException('Format foto batch tidak didukung.'),
        };
        $path = 'catalog/batches/'.now()->format('Y/m').'/'.Str::uuid().'.'.$extension;
        $storage = Storage::disk($disk);
        $stream = fopen($file->getRealPath(), 'rb');

        if (! is_resource($stream)) {
            throw new RuntimeException('File foto batch tidak dapat dibaca.');
        }

        try {
            $written = $storage->put($path, $stream, ['visibility' => 'public']);
        } finally {
            fclose($stream);
        }

        $storedSize = $written && $storage->exists($path) ? (int) $storage->size($path) : 0;
        if (! $written || $storedSize !== (int) $file->getSize()) {
            $storage->delete($path);

            throw new RuntimeException('Foto batch gagal diverifikasi setelah upload.');
        }

        return $path;
    }

    private function catalogUploadDisk(): string
    {
        $disk = (string) config('filesystems.catalog_upload_disk', 'public');

        if (! config("filesystems.disks.{$disk}")) {
            throw new RuntimeException("Disk upload foto katalog [{$disk}] tidak tersedia.");
        }

        return $disk;
    }
}
