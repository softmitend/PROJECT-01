<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Member;
use App\Models\MemberOrder;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class GoKpopMerchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        $statuses = OrderStatus::whereIn('code', [
            'secured',
            'arrived-wh-korea',
            'flight-to-indonesia',
            'customs-clearance',
            'siap-distribusi',
            'selesai',
            'menunggu-dp',
            'menunggu-pelunasan',
            'lunas',
            'barang-kurang',
        ])->get()->keyBy('code');

        $products = collect([
            ['name' => 'SEVENTEEN 12th Mini Album', 'variant' => 'Weverse POB Set', 'default_price' => 315000],
            ['name' => 'NCT DREAM Official Photocard', 'variant' => 'Random Member', 'default_price' => 85000],
            ['name' => 'aespa Lightstick', 'variant' => 'Official SM', 'default_price' => 720000],
            ['name' => 'IVE Season Greetings 2027', 'variant' => 'Pre-order Benefit', 'default_price' => 565000],
            ['name' => 'NewJeans Get Up Album', 'variant' => 'Bunny Beach Bag Ver.', 'default_price' => 295000],
            ['name' => 'Stray Kids SKZOO Plush', 'variant' => 'Mini Original', 'default_price' => 410000],
            ['name' => 'LE SSERAFIM Photocard Binder', 'variant' => 'Official MD', 'default_price' => 220000],
            ['name' => 'BTS Proof Collector Edition', 'variant' => 'Collector Set', 'default_price' => 2850000, 'is_active' => false],
        ])->mapWithKeys(function (array $product) {
            $model = Product::updateOrCreate(
                ['name' => $product['name'], 'variant' => $product['variant']],
                [
                    'description' => 'Data contoh GO Kpop Merch.',
                    'default_price' => $product['default_price'],
                    'is_active' => $product['is_active'] ?? true,
                ],
            );

            return [$product['name'] => $model];
        });

        $members = collect([
            ['member_code' => 'GO-0001', 'display_name' => 'Mira Carat', 'email' => 'mira@example.com', 'phone' => '081234560001', 'address' => 'Jl. Melati No. 1, Jakarta Selatan', 'notes' => 'Pengiriman reguler, packing kayu untuk album.', 'is_active' => true],
            ['member_code' => 'GO-0002', 'display_name' => 'Dinda Czennie', 'email' => 'dinda@example.com', 'phone' => '081234560002', 'address' => 'Jl. Anggrek No. 2, Bandung', 'notes' => 'Prioritas member Mark dan Jeno.', 'is_active' => true],
            ['member_code' => 'GO-0003', 'display_name' => 'Rara MY', 'email' => 'rara@example.com', 'phone' => '081234560003', 'address' => 'Jl. Kenanga No. 3, Surabaya', 'notes' => 'Hubungi melalui WhatsApp sebelum pengiriman.', 'is_active' => true],
            ['member_code' => 'GO-0004', 'display_name' => 'Nay WIZ*ONE', 'email' => 'nay@example.com', 'phone' => '081234560004', 'address' => 'Jl. Mawar No. 4, Yogyakarta', 'notes' => 'Bisa pickup saat event.', 'is_active' => true],
            ['member_code' => 'GO-0005', 'display_name' => 'Kevin Stay', 'email' => 'kevin@example.com', 'phone' => '081234560005', 'address' => 'Jl. Tulip No. 5, Semarang', 'notes' => 'Konfirmasi DP melalui email.', 'is_active' => true],
            ['member_code' => 'GO-0006', 'display_name' => 'Sasa MOA', 'email' => 'sasa@example.com', 'phone' => '081234560006', 'address' => 'Jl. Flamboyan No. 6, Malang', 'notes' => 'Pelanggan lama yang sedang dinonaktifkan.', 'is_active' => false],
        ])->mapWithKeys(function (array $member) {
            $model = Member::updateOrCreate(
                ['member_code' => $member['member_code']],
                $member,
            );

            return [$member['member_code'] => $model];
        });

        $batches = collect([
            [
                'batch_number' => 'GO-SVT-2508',
                'batch_name' => 'GO SEVENTEEN Album Weverse POB',
                'status' => 'arrived-wh-korea',
                'description' => 'Group order album SEVENTEEN dengan benefit Weverse.',
                'notes' => 'ETA Indonesia sekitar 2-3 minggu setelah EMS.',
                'started_at' => now()->subDays(16),
            ],
            [
                'batch_number' => 'GO-NCT-2508',
                'batch_name' => 'GO NCT DREAM Photocard Claim',
                'status' => 'flight-to-indonesia',
                'description' => 'Group order photocard NCT DREAM random/member claim.',
                'notes' => 'EMS sudah dibuat, menunggu update customs.',
                'started_at' => now()->subDays(11),
            ],
            [
                'batch_number' => 'GO-SMMD-2507',
                'batch_name' => 'GO SM Official MD Lightstick',
                'status' => 'customs-clearance',
                'description' => 'Group order official MD dari SM Store.',
                'notes' => 'Paket masuk proses bea cukai.',
                'started_at' => now()->subDays(24),
            ],
            [
                'batch_number' => 'GO-MIX-2506',
                'batch_name' => 'GO Mixed Ready Stock Merch',
                'status' => 'siap-distribusi',
                'description' => 'Ready stock merch campuran untuk pickup atau kirim lokal.',
                'notes' => 'Buyer bisa pilih pickup atau ekspedisi lokal.',
                'started_at' => now()->subDays(38),
                'completed_at' => null,
            ],
            [
                'batch_number' => 'GO-ARCHIVE-2505',
                'batch_name' => 'GO Arsip Mei 2025',
                'status' => 'selesai',
                'description' => 'Contoh batch yang sudah selesai dan diarsipkan.',
                'notes' => 'Seluruh pesanan telah diterima customer.',
                'started_at' => now()->subDays(75),
                'completed_at' => now()->subDays(45),
                'is_archived' => true,
            ],
        ])->mapWithKeys(function (array $batch) use ($statuses, $admin) {
            $model = Batch::updateOrCreate(
                ['batch_number' => $batch['batch_number']],
                [
                    'batch_name' => $batch['batch_name'],
                    'current_status_id' => $statuses[$batch['status']]->id,
                    'description' => $batch['description'],
                    'notes' => $batch['notes'],
                    'started_at' => $batch['started_at'],
                    'completed_at' => $batch['completed_at'] ?? null,
                    'is_archived' => $batch['is_archived'] ?? false,
                ],
            );

            $model->statusHistories()->firstOrCreate([
                'new_status_id' => $statuses[$batch['status']]->id,
                'note' => 'Status awal batch GO Kpop Merch dari seeder.',
            ], [
                'old_status_id' => null,
                'changed_by' => $admin?->id,
            ]);

            return [$batch['batch_number'] => $model];
        });

        $this->seedOrder(
            orderCode: 'ORD-GO-SVT-0001',
            member: $members['GO-0001'],
            batch: $batches['GO-SVT-2508'],
            items: [
                ['product' => $products['SEVENTEEN 12th Mini Album'], 'quantity' => 2],
                ['product' => $products['LE SSERAFIM Photocard Binder'], 'quantity' => 1],
            ],
            paymentStatus: $statuses['menunggu-pelunasan'],
            note: 'Buyer minta ship bareng binder.',
            admin: $admin,
        );

        $this->seedOrder(
            orderCode: 'ORD-GO-NCT-0002',
            member: $members['GO-0002'],
            batch: $batches['GO-NCT-2508'],
            items: [
                ['product' => $products['NCT DREAM Official Photocard'], 'quantity' => 4, 'notes' => 'Prefer Mark/Jeno jika available.'],
            ],
            overrideStatus: $statuses['secured'],
            paymentStatus: $statuses['lunas'],
            note: 'Claim secured dari seller Korea.',
            admin: $admin,
        );

        $this->seedOrder(
            orderCode: 'ORD-GO-SMMD-0003',
            member: $members['GO-0003'],
            batch: $batches['GO-SMMD-2507'],
            items: [
                ['product' => $products['aespa Lightstick'], 'quantity' => 1],
                ['product' => $products['NewJeans Get Up Album'], 'quantity' => 1, 'status' => $statuses['barang-kurang'], 'notes' => 'POB belum ikut dalam paket utama.'],
            ],
            paymentStatus: $statuses['lunas'],
            note: 'Lightstick ikut batch official MD.',
            admin: $admin,
        );

        $this->seedOrder(
            orderCode: 'ORD-GO-MIX-0004',
            member: $members['GO-0004'],
            batch: $batches['GO-MIX-2506'],
            items: [
                ['product' => $products['IVE Season Greetings 2027'], 'quantity' => 1],
                ['product' => $products['LE SSERAFIM Photocard Binder'], 'quantity' => 2],
            ],
            overrideStatus: $statuses['siap-distribusi'],
            paymentStatus: $statuses['lunas'],
            note: 'Siap pickup di event cupsleeve.',
            admin: $admin,
        );

        $this->seedOrder(
            orderCode: 'ORD-GO-MIX-0005',
            member: $members['GO-0005'],
            batch: $batches['GO-MIX-2506'],
            items: [
                ['product' => $products['Stray Kids SKZOO Plush'], 'quantity' => 1],
                ['product' => $products['NCT DREAM Official Photocard'], 'quantity' => 2],
            ],
            paymentStatus: $statuses['menunggu-dp'],
            note: 'Slot ditahan maksimal 24 jam.',
            admin: $admin,
        );
    }

    private function seedOrder(
        string $orderCode,
        Member $member,
        Batch $batch,
        array $items,
        ?OrderStatus $overrideStatus = null,
        ?OrderStatus $paymentStatus = null,
        ?string $note = null,
        ?User $admin = null,
    ): void {
        $order = MemberOrder::updateOrCreate(
            ['order_code' => $orderCode],
            [
                'member_id' => $member->id,
                'batch_id' => $batch->id,
                'override_status_id' => $overrideStatus?->id,
                'payment_status_id' => $paymentStatus?->id,
                'payment_status' => null,
                'notes' => $note,
            ],
        );

        $keptItemIds = [];

        foreach ($items as $item) {
            /** @var Product $product */
            $product = $item['product'];
            $quantity = $item['quantity'];
            $unitPrice = $product->default_price;

            $orderItem = OrderItem::updateOrCreate(
                [
                    'member_order_id' => $order->id,
                    'item_name' => $product->name,
                    'variant' => $product->variant,
                ],
                [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $quantity * $unitPrice,
                    'override_status_id' => $item['status']->id ?? null,
                    'notes' => $item['notes'] ?? null,
                ],
            );

            $keptItemIds[] = $orderItem->id;

            if ($item['status'] ?? null) {
                $orderItem->statusHistories()->firstOrCreate([
                    'new_status_id' => $item['status']->id,
                    'note' => $item['notes'] ?? 'Status khusus item dari seeder GO Kpop Merch.',
                ], [
                    'old_status_id' => $overrideStatus?->id ?? $batch->current_status_id,
                    'changed_by' => $admin?->id,
                ]);
            }
        }

        $order->items()->whereNotIn('id', $keptItemIds)->delete();
        $order->update(['total_amount' => $order->items()->sum('subtotal')]);

        if ($overrideStatus) {
            $order->statusHistories()->firstOrCreate([
                'new_status_id' => $overrideStatus->id,
                'note' => $note ?: 'Override status buyer dari seeder GO Kpop Merch.',
            ], [
                'old_status_id' => $batch->current_status_id,
                'changed_by' => $admin?->id,
            ]);
        }
    }
}
