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
            'on-hand-korea',
            'ems-korea-id',
            'customs-clearance',
            'ready-pickup',
            'selesai',
            'menunggu-dp',
            'menunggu-pelunasan',
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
        ])->mapWithKeys(function (array $product) {
            $model = Product::updateOrCreate(
                ['name' => $product['name'], 'variant' => $product['variant']],
                [
                    'description' => 'Data contoh GO Kpop Merch.',
                    'default_price' => $product['default_price'],
                    'is_active' => true,
                ],
            );

            return [$product['name'] => $model];
        });

        $members = collect([
            ['member_code' => 'GO-0001', 'display_name' => 'Mira Carat', 'username' => 'mira_carat', 'access_code' => 'carat-mira-1811', 'group_name' => 'CARAT'],
            ['member_code' => 'GO-0002', 'display_name' => 'Dinda Czennie', 'username' => 'dinda_nct', 'access_code' => 'czennie-dinda', 'group_name' => 'NCTzen'],
            ['member_code' => 'GO-0003', 'display_name' => 'Rara MY ✨', 'username' => 'rara_my', 'access_code' => 'my-rara-aespa', 'group_name' => 'MY'],
            ['member_code' => 'GO-0004', 'display_name' => 'Nay WIZ*ONE', 'username' => 'nay_ive', 'access_code' => 'ive-nay', 'group_name' => 'DIVE'],
            ['member_code' => 'GO-0005', 'display_name' => 'Kevin Stay', 'username' => 'kevin_stay', 'access_code' => 'stay-kevin', 'group_name' => 'STAY'],
        ])->mapWithKeys(function (array $member) {
            $model = Member::updateOrCreate(
                ['member_code' => $member['member_code']],
                $member + ['notes' => 'Buyer contoh untuk GO Kpop Merch.', 'is_active' => true],
            );

            return [$member['member_code'] => $model];
        });

        $batches = collect([
            [
                'batch_number' => 'GO-SVT-2508',
                'batch_name' => 'GO SEVENTEEN Album Weverse POB',
                'status' => 'on-hand-korea',
                'description' => 'Group order album SEVENTEEN dengan benefit Weverse.',
                'notes' => 'ETA Indonesia sekitar 2-3 minggu setelah EMS.',
                'started_at' => now()->subDays(16),
            ],
            [
                'batch_number' => 'GO-NCT-2508',
                'batch_name' => 'GO NCT DREAM Photocard Claim',
                'status' => 'ems-korea-id',
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
                'status' => 'ready-pickup',
                'description' => 'Ready stock merch campuran untuk pickup atau kirim lokal.',
                'notes' => 'Buyer bisa pilih pickup atau ekspedisi lokal.',
                'started_at' => now()->subDays(38),
                'completed_at' => null,
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
                    'is_archived' => false,
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
            overrideStatus: $statuses['menunggu-pelunasan'],
            paymentStatus: 'DP lunas, menunggu pelunasan EMS',
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
            paymentStatus: 'Lunas',
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
            paymentStatus: 'Lunas',
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
            overrideStatus: $statuses['ready-pickup'],
            paymentStatus: 'Lunas',
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
            overrideStatus: $statuses['menunggu-dp'],
            paymentStatus: 'Menunggu DP',
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
        ?string $paymentStatus = null,
        ?string $note = null,
        ?User $admin = null,
    ): void {
        $order = MemberOrder::updateOrCreate(
            ['member_id' => $member->id, 'batch_id' => $batch->id],
            [
                'order_code' => $orderCode,
                'override_status_id' => $overrideStatus?->id,
                'payment_status' => $paymentStatus,
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
