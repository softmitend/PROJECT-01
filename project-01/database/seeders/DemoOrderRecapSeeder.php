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

class DemoOrderRecapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        $statuses = OrderStatus::whereIn('code', [
            'tiba-di-warehouse',
            'dalam-perjalanan',
            'selesai',
            'menunggu-pelunasan',
            'barang-kurang',
        ])->get()->keyBy('code');

        $products = collect([
            ['name' => 'Basreng Daun Jeruk', 'variant' => 'Pedas', 'default_price' => 18000],
            ['name' => 'Cireng Ayam Suwir', 'variant' => 'Frozen', 'default_price' => 25000],
            ['name' => 'Keripik Kaca', 'variant' => 'Original', 'default_price' => 15000],
            ['name' => 'Seblak Instan', 'variant' => 'Level 3', 'default_price' => 14000],
            ['name' => 'Maklor Kering', 'variant' => 'Balado', 'default_price' => 12000],
        ])->mapWithKeys(function (array $product) {
            $model = Product::updateOrCreate(
                ['name' => $product['name'], 'variant' => $product['variant']],
                [
                    'description' => 'Data contoh untuk rekap jajanan.',
                    'default_price' => $product['default_price'],
                    'is_active' => true,
                ],
            );

            return [$product['name'] => $model];
        });

        $members = collect([
            ['member_code' => 'MBR-0001', 'display_name' => 'Fadhia ✨', 'username' => 'fadhia', 'access_code' => 'akses-fadhia', 'group_name' => 'F-J'],
            ['member_code' => 'MBR-0002', 'display_name' => 'Ayu Lestari', 'username' => 'ayu', 'access_code' => 'akses-ayu', 'group_name' => 'A-E'],
            ['member_code' => 'MBR-0003', 'display_name' => 'محمد Rizky', 'username' => 'rizky', 'access_code' => 'akses-rizky', 'group_name' => 'P-T'],
            ['member_code' => 'MBR-0004', 'display_name' => 'Nina & Co.', 'username' => 'nina', 'access_code' => 'akses-nina', 'group_name' => 'K-O'],
        ])->mapWithKeys(function (array $member) {
            $model = Member::updateOrCreate(
                ['member_code' => $member['member_code']],
                $member + ['notes' => 'Data member contoh.', 'is_active' => true],
            );

            return [$member['member_code'] => $model];
        });

        $batches = collect([
            ['batch_number' => '1811', 'batch_name' => 'Batch Jajanan Minggu 1', 'status' => 'tiba-di-warehouse', 'started_at' => now()->subDays(7)],
            ['batch_number' => '1812', 'batch_name' => 'Batch Jajanan Minggu 2', 'status' => 'dalam-perjalanan', 'started_at' => now()->subDays(2)],
            ['batch_number' => '1809', 'batch_name' => 'Batch Selesai Bulan Lalu', 'status' => 'selesai', 'started_at' => now()->subDays(35), 'completed_at' => now()->subDays(25)],
        ])->mapWithKeys(function (array $batch) use ($statuses, $admin) {
            $model = Batch::updateOrCreate(
                ['batch_number' => $batch['batch_number']],
                [
                    'batch_name' => $batch['batch_name'],
                    'current_status_id' => $statuses[$batch['status']]->id,
                    'description' => 'Batch contoh untuk demo rekap pesanan.',
                    'notes' => 'Dibuat dari seeder.',
                    'started_at' => $batch['started_at'],
                    'completed_at' => $batch['completed_at'] ?? null,
                    'is_archived' => false,
                ],
            );

            $model->statusHistories()->firstOrCreate([
                'new_status_id' => $statuses[$batch['status']]->id,
                'note' => 'Status awal dari seeder demo.',
            ], [
                'old_status_id' => null,
                'changed_by' => $admin?->id,
            ]);

            return [$batch['batch_number'] => $model];
        });

        $this->seedOrder(
            orderCode: 'ORD-1811-MBR0001',
            member: $members['MBR-0001'],
            batch: $batches['1811'],
            items: [
                ['product' => $products['Basreng Daun Jeruk'], 'quantity' => 2],
                ['product' => $products['Cireng Ayam Suwir'], 'quantity' => 1],
                ['product' => $products['Keripik Kaca'], 'quantity' => 3, 'status' => $statuses['barang-kurang'], 'notes' => 'Sisa 1 pcs menyusul.'],
            ],
            overrideStatus: $statuses['menunggu-pelunasan'],
            note: 'Menunggu konfirmasi transfer.',
            admin: $admin,
        );

        $this->seedOrder(
            orderCode: 'ORD-1811-MBR0002',
            member: $members['MBR-0002'],
            batch: $batches['1811'],
            items: [
                ['product' => $products['Seblak Instan'], 'quantity' => 4],
                ['product' => $products['Maklor Kering'], 'quantity' => 2],
            ],
            admin: $admin,
        );

        $this->seedOrder(
            orderCode: 'ORD-1812-MBR0003',
            member: $members['MBR-0003'],
            batch: $batches['1812'],
            items: [
                ['product' => $products['Basreng Daun Jeruk'], 'quantity' => 1],
                ['product' => $products['Keripik Kaca'], 'quantity' => 2],
            ],
            admin: $admin,
        );

        $this->seedOrder(
            orderCode: 'ORD-1809-MBR0004',
            member: $members['MBR-0004'],
            batch: $batches['1809'],
            items: [
                ['product' => $products['Cireng Ayam Suwir'], 'quantity' => 2],
                ['product' => $products['Seblak Instan'], 'quantity' => 2],
            ],
            admin: $admin,
        );
    }

    private function seedOrder(
        string $orderCode,
        Member $member,
        Batch $batch,
        array $items,
        ?OrderStatus $overrideStatus = null,
        ?string $note = null,
        ?User $admin = null,
    ): void {
        $order = MemberOrder::updateOrCreate(
            ['member_id' => $member->id, 'batch_id' => $batch->id],
            [
                'order_code' => $orderCode,
                'override_status_id' => $overrideStatus?->id,
                'payment_status' => $overrideStatus ? 'Belum lunas' : 'Lunas',
                'notes' => $note,
            ],
        );

        foreach ($items as $item) {
            /** @var Product $product */
            $product = $item['product'];
            $quantity = $item['quantity'];
            $unitPrice = $product->default_price;

            OrderItem::updateOrCreate(
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
        }

        $order->update(['total_amount' => $order->items()->sum('subtotal')]);

        if ($overrideStatus) {
            $order->statusHistories()->firstOrCreate([
                'new_status_id' => $overrideStatus->id,
                'note' => $note ?: 'Override status pesanan dari seeder demo.',
            ], [
                'old_status_id' => $batch->current_status_id,
                'changed_by' => $admin?->id,
            ]);
        }
    }
}
