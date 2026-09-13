<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::query()
            ->when(request('q'), fn ($query, $q) => $query->where(fn ($query) => $query
                ->where('name', 'like', "%{$q}%")
                ->orWhere('variant', 'like', "%{$q}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.products.form', ['product' => new Product]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        Product::create($request->validated() + ['is_active' => true]);

        session()->flash('status', 'Produk berhasil ditambahkan.');

        return new RedirectResponse('/admin/products', 303);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->loadCount(['batches', 'orderItems']);

        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('admin.products.form', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        session()->flash('status', 'Produk berhasil diperbarui.');

        return new RedirectResponse('/admin/products/'.$product->id, 303);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->update(['is_active' => false]);

        return back()->with('status', 'Produk dinonaktifkan.');
    }

    public function updateStatus(Request $request, Product $product)
    {
        abort_unless($request->user()?->can('access-admin'), 403);

        $data = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $product->update(['is_active' => (bool) $data['is_active']]);

        return back()->with('status', $product->is_active
            ? 'Produk berhasil diaktifkan.'
            : 'Produk berhasil dinonaktifkan.');
    }
}
