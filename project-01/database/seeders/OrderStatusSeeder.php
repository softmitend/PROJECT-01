<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        collect([
            ['name' => 'Menunggu Pemesanan', 'code' => 'menunggu-pemesanan', 'description' => 'Pesanan sudah tercatat dan menunggu proses pembelian ke store atau supplier.', 'color' => '#64748b', 'sequence' => 10, 'status_type' => 'process', 'scope' => 'batch', 'is_initial' => true, 'is_final' => false],
            ['name' => 'Ordered', 'code' => 'sudah-dipesan', 'description' => 'Produk sudah berhasil dipesan ke store atau supplier.', 'color' => '#2563eb', 'sequence' => 20, 'status_type' => 'process', 'scope' => 'all', 'is_initial' => false, 'is_final' => false],
            ['name' => 'Arrived Warehouse Korea', 'code' => 'arrived-wh-korea', 'description' => 'Produk sudah tiba di warehouse Korea dan menunggu pengiriman ke Indonesia.', 'color' => '#7c3aed', 'sequence' => 30, 'status_type' => 'process', 'scope' => 'batch', 'is_initial' => false, 'is_final' => false],
            ['name' => 'Arrived Warehouse China', 'code' => 'arrived-wh-china', 'description' => 'Produk sudah tiba di warehouse China dan menunggu pengiriman ke Indonesia.', 'color' => '#9333ea', 'sequence' => 31, 'status_type' => 'process', 'scope' => 'batch', 'is_initial' => false, 'is_final' => false],
            ['name' => 'Arrived Warehouse Japan', 'code' => 'arrived-wh-japan', 'description' => 'Produk sudah tiba di warehouse Jepang dan menunggu pengiriman ke Indonesia.', 'color' => '#a855f7', 'sequence' => 32, 'status_type' => 'process', 'scope' => 'batch', 'is_initial' => false, 'is_final' => false],
            ['name' => 'Flight to Indonesia', 'code' => 'flight-to-indonesia', 'description' => 'Paket sedang dikirim ke Indonesia melalui jalur udara.', 'color' => '#0891b2', 'sequence' => 40, 'status_type' => 'process', 'scope' => 'batch', 'is_initial' => false, 'is_final' => false],
            ['name' => 'Sea to Indonesia', 'code' => 'sea-to-indonesia', 'description' => 'Paket sedang dikirim ke Indonesia melalui jalur laut.', 'color' => '#0284c7', 'sequence' => 41, 'status_type' => 'process', 'scope' => 'batch', 'is_initial' => false, 'is_final' => false],
            ['name' => 'Customs Clearance', 'code' => 'customs-clearance', 'description' => 'Paket sedang menjalani proses bea cukai di Indonesia.', 'color' => '#db2777', 'sequence' => 50, 'status_type' => 'process', 'scope' => 'batch', 'is_initial' => false, 'is_final' => false],
            ['name' => 'Arrived Admin', 'code' => 'arrived-admin', 'description' => 'Paket sudah tiba di admin dan sedang dicek atau disortir.', 'color' => '#0d9488', 'sequence' => 60, 'status_type' => 'process', 'scope' => 'batch', 'is_initial' => false, 'is_final' => false],
            ['name' => 'Siap Distribusi', 'code' => 'siap-distribusi', 'description' => 'Pesanan sudah siap diambil atau dikirim ke customer.', 'color' => '#059669', 'sequence' => 70, 'status_type' => 'process', 'scope' => 'all', 'is_initial' => false, 'is_final' => false],
            ['name' => 'Dikirim ke Customer', 'code' => 'dikirim-ke-customer', 'description' => 'Pesanan sudah diserahkan ke ekspedisi lokal.', 'color' => '#16a34a', 'sequence' => 80, 'status_type' => 'process', 'scope' => 'member_order', 'is_initial' => false, 'is_final' => false],
            ['name' => 'Selesai', 'code' => 'selesai', 'description' => 'Pesanan sudah diterima dan proses dinyatakan selesai.', 'color' => '#15803d', 'sequence' => 90, 'status_type' => 'success', 'scope' => 'all', 'is_initial' => false, 'is_final' => true],
            ['name' => 'Dibatalkan', 'code' => 'dibatalkan', 'description' => 'Pesanan dibatalkan dan tidak dilanjutkan.', 'color' => '#dc2626', 'sequence' => 99, 'status_type' => 'cancelled', 'scope' => 'all', 'is_initial' => false, 'is_final' => true],

            ['name' => 'Menunggu DP', 'code' => 'menunggu-dp', 'description' => 'Customer belum menyelesaikan pembayaran DP.', 'color' => '#ca8a04', 'sequence' => 10, 'status_type' => 'process', 'scope' => 'payment', 'is_initial' => true, 'is_final' => false],
            ['name' => 'Menunggu Pelunasan', 'code' => 'menunggu-pelunasan', 'description' => 'Pesanan sudah berjalan tetapi masih menunggu pelunasan.', 'color' => '#d97706', 'sequence' => 20, 'status_type' => 'process', 'scope' => 'payment', 'is_initial' => false, 'is_final' => false],
            ['name' => 'Lunas', 'code' => 'lunas', 'description' => 'Pembayaran customer telah diterima seluruhnya.', 'color' => '#059669', 'sequence' => 90, 'status_type' => 'success', 'scope' => 'payment', 'is_initial' => false, 'is_final' => true],
            ['name' => 'Refund', 'code' => 'refund', 'description' => 'Pembayaran pesanan sedang atau sudah dikembalikan.', 'color' => '#ea580c', 'sequence' => 95, 'status_type' => 'failed', 'scope' => 'payment', 'is_initial' => false, 'is_final' => true],
            ['name' => 'Secured', 'code' => 'secured', 'description' => 'Slot atau produk customer sudah berhasil diamankan.', 'color' => '#4f46e5', 'sequence' => 21, 'status_type' => 'process', 'scope' => 'all', 'is_initial' => false, 'is_final' => false],
            ['name' => 'Barang Kurang', 'code' => 'barang-kurang', 'description' => 'Jumlah produk yang diterima belum lengkap.', 'color' => '#be123c', 'sequence' => 90, 'status_type' => 'failed', 'scope' => 'order_item', 'is_initial' => false, 'is_final' => false],
            ['name' => 'Barang Rusak', 'code' => 'barang-rusak', 'description' => 'Produk diterima dalam kondisi rusak dan perlu tindak lanjut.', 'color' => '#b91c1c', 'sequence' => 91, 'status_type' => 'failed', 'scope' => 'order_item', 'is_initial' => false, 'is_final' => false],
        ])->each(function (array $status): void {
            OrderStatus::updateOrCreate(
                ['code' => $status['code']],
                $status + ['is_active' => true],
            );
        });
    }
}
