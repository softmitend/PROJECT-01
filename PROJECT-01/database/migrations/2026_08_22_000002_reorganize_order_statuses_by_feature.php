<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $updates = [
            'menunggu-pemesanan' => ['name' => 'Menunggu Pemesanan ke Supplier', 'description' => 'Batch sudah dibuat dan menunggu proses pemesanan produk ke store atau supplier.', 'scope' => 'batch'],
            'sudah-dipesan' => ['name' => 'Sudah Dipesan ke Supplier', 'description' => 'Seluruh produk dalam batch sudah berhasil dipesan ke store atau supplier.', 'scope' => 'batch'],
            'arrived-wh-korea' => ['name' => 'Tiba di Gudang Korea', 'description' => 'Produk dalam batch sudah tiba di gudang Korea dan menunggu pengiriman ke Indonesia.', 'scope' => 'batch'],
            'arrived-wh-china' => ['name' => 'Tiba di Gudang China', 'description' => 'Produk dalam batch sudah tiba di gudang China dan menunggu pengiriman ke Indonesia.', 'scope' => 'batch'],
            'arrived-wh-japan' => ['name' => 'Tiba di Gudang Jepang', 'description' => 'Produk dalam batch sudah tiba di gudang Jepang dan menunggu pengiriman ke Indonesia.', 'scope' => 'batch'],
            'flight-to-indonesia' => ['name' => 'Dikirim via Udara ke Indonesia', 'description' => 'Batch sedang dikirim menuju Indonesia melalui jalur udara.', 'scope' => 'batch'],
            'sea-to-indonesia' => ['name' => 'Dikirim via Laut ke Indonesia', 'description' => 'Batch sedang dikirim menuju Indonesia melalui jalur laut.', 'scope' => 'batch'],
            'customs-clearance' => ['name' => 'Proses Bea Cukai', 'description' => 'Batch sedang menjalani pemeriksaan dan proses bea cukai di Indonesia.', 'scope' => 'batch'],
            'arrived-admin' => ['name' => 'Tiba di Gudang Admin', 'description' => 'Batch sudah tiba di gudang admin dan sedang diperiksa atau disortir.', 'scope' => 'batch'],
            'siap-distribusi' => ['name' => 'Siap Distribusi', 'description' => 'Barang dalam batch atau pesanan sudah siap diambil atau dikirim kepada pelanggan.', 'scope' => 'all'],
            'selesai' => ['name' => 'Selesai', 'description' => 'Barang sudah diterima pelanggan dan seluruh proses dinyatakan selesai.', 'scope' => 'all'],
            'dikirim-ke-customer' => ['name' => 'Pesanan Dikirim ke Pelanggan', 'description' => 'Pesanan pelanggan ini sudah diserahkan kepada ekspedisi lokal.', 'scope' => 'member_order'],
            'secured' => ['name' => 'Pesanan Berhasil Diamankan', 'description' => 'Slot atau seluruh produk pada pesanan pelanggan ini sudah berhasil diamankan.', 'scope' => 'member_order'],
            'barang-kurang' => ['name' => 'Item Kurang', 'description' => 'Jumlah produk yang diterima untuk item ini belum lengkap.', 'scope' => 'order_item'],
            'barang-rusak' => ['name' => 'Item Rusak', 'description' => 'Item diterima dalam kondisi rusak dan memerlukan tindak lanjut.', 'scope' => 'order_item'],
            'menunggu-dp' => ['name' => 'Menunggu Pembayaran DP', 'description' => 'Pelanggan belum menyelesaikan pembayaran uang muka atau DP.', 'scope' => 'payment'],
            'menunggu-pelunasan' => ['name' => 'Menunggu Pelunasan', 'description' => 'Pembayaran DP sudah diterima dan masih menunggu pelunasan dari pelanggan.', 'scope' => 'payment'],
            'lunas' => ['name' => 'Pembayaran Lunas', 'description' => 'Pembayaran pelanggan telah diterima seluruhnya.', 'scope' => 'payment'],
            'refund' => ['name' => 'Dana Dikembalikan (Refund)', 'description' => 'Dana pembayaran pesanan sedang atau sudah dikembalikan kepada pelanggan.', 'scope' => 'payment'],
        ];

        foreach ($updates as $code => $attributes) {
            DB::table('order_statuses')->where('code', $code)->update($attributes + ['updated_at' => now()]);
        }
    }

    public function down(): void
    {
        $updates = [
            'menunggu-pemesanan' => ['name' => 'Menunggu Pemesanan', 'description' => 'Pesanan sudah tercatat dan menunggu proses pembelian ke store atau supplier.', 'scope' => 'batch'],
            'sudah-dipesan' => ['name' => 'Ordered', 'description' => 'Produk sudah berhasil dipesan ke store atau supplier.', 'scope' => 'all'],
            'arrived-wh-korea' => ['name' => 'Arrived Warehouse Korea', 'description' => 'Produk sudah tiba di warehouse Korea dan menunggu pengiriman ke Indonesia.', 'scope' => 'batch'],
            'arrived-wh-china' => ['name' => 'Arrived Warehouse China', 'description' => 'Produk sudah tiba di warehouse China dan menunggu pengiriman ke Indonesia.', 'scope' => 'batch'],
            'arrived-wh-japan' => ['name' => 'Arrived Warehouse Japan', 'description' => 'Produk sudah tiba di warehouse Jepang dan menunggu pengiriman ke Indonesia.', 'scope' => 'batch'],
            'flight-to-indonesia' => ['name' => 'Flight to Indonesia', 'description' => 'Paket sedang dikirim ke Indonesia melalui jalur udara.', 'scope' => 'batch'],
            'sea-to-indonesia' => ['name' => 'Sea to Indonesia', 'description' => 'Paket sedang dikirim ke Indonesia melalui jalur laut.', 'scope' => 'batch'],
            'customs-clearance' => ['name' => 'Customs Clearance', 'description' => 'Paket sedang menjalani proses bea cukai di Indonesia.', 'scope' => 'batch'],
            'arrived-admin' => ['name' => 'Arrived Admin', 'description' => 'Paket sudah tiba di admin dan sedang dicek atau disortir.', 'scope' => 'batch'],
            'siap-distribusi' => ['name' => 'Siap Distribusi', 'description' => 'Pesanan sudah siap diambil atau dikirim ke customer.', 'scope' => 'all'],
            'selesai' => ['name' => 'Selesai', 'description' => 'Pesanan sudah diterima dan proses dinyatakan selesai.', 'scope' => 'all'],
            'dikirim-ke-customer' => ['name' => 'Dikirim ke Customer', 'description' => 'Pesanan sudah diserahkan ke ekspedisi lokal.', 'scope' => 'member_order'],
            'secured' => ['name' => 'Secured', 'description' => 'Slot atau produk customer sudah berhasil diamankan.', 'scope' => 'all'],
            'barang-kurang' => ['name' => 'Barang Kurang', 'description' => 'Jumlah produk yang diterima belum lengkap.', 'scope' => 'order_item'],
            'barang-rusak' => ['name' => 'Barang Rusak', 'description' => 'Produk diterima dalam kondisi rusak dan perlu tindak lanjut.', 'scope' => 'order_item'],
            'menunggu-dp' => ['name' => 'Menunggu DP', 'description' => 'Customer belum menyelesaikan pembayaran DP.', 'scope' => 'payment'],
            'menunggu-pelunasan' => ['name' => 'Menunggu Pelunasan', 'description' => 'Pesanan sudah berjalan tetapi masih menunggu pelunasan.', 'scope' => 'payment'],
            'lunas' => ['name' => 'Lunas', 'description' => 'Pembayaran customer telah diterima seluruhnya.', 'scope' => 'payment'],
            'refund' => ['name' => 'Refund', 'description' => 'Pembayaran pesanan sedang atau sudah dikembalikan.', 'scope' => 'payment'],
        ];

        foreach ($updates as $code => $attributes) {
            DB::table('order_statuses')->where('code', $code)->update($attributes + ['updated_at' => now()]);
        }
    }
};
