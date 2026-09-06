<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Member;
use App\Models\MemberOrder;
use App\Models\OrderStatus;
use App\Models\Product;
use Illuminate\Database\Seeder;
use RuntimeException;

class CustomerOrderSeeder extends Seeder
{
    public function run(): void
    {
        $member = Member::query()->where('member_code', 'OP-CUSTOMER-DEMO')->first();
        if (! $member) {
            throw new RuntimeException('Jalankan CustomerMemberSeeder sebelum CustomerOrderSeeder.');
        }

        $statuses = OrderStatus::query()->get()->keyBy('code');
        $requiredStatuses = collect([
            'menunggu-pemesanan', 'secured', 'arrived-admin', 'dikirim-ke-customer',
            'selesai', 'refunded', 'menunggu-dp', 'menunggu-pelunasan', 'lunas', 'refund',
        ]);
        if ($requiredStatuses->contains(fn (string $code) => ! $statuses->has($code))) {
            throw new RuntimeException('Jalankan OrderStatusSeeder sebelum CustomerOrderSeeder.');
        }

        $products = Product::query()->get()->keyBy(fn (Product $product) => $product->name.'|'.$product->variant);
        $batches = Batch::query()->whereIn('batch_number', collect($this->orders())->pluck('batch'))->get()->keyBy('batch_number');
        if ($batches->count() !== count($this->orders())) {
            throw new RuntimeException('Jalankan CustomerCatalogSeeder sebelum CustomerOrderSeeder.');
        }

        foreach ($this->orders() as $index => $data) {
            $batch = $batches[$data['batch']];
            $paymentStatus = $statuses[$data['payment_status']];
            $overrideStatus = $data['override_status'] ? $statuses[$data['override_status']] : null;
            $createdAt = now()->subDays($data['age_days']);

            $order = MemberOrder::query()->updateOrCreate(
                ['member_id' => $member->id, 'batch_id' => $batch->id],
                [
                    'order_code' => $data['code'],
                    'override_status_id' => $overrideStatus?->id,
                    'payment_status_id' => $paymentStatus->id,
                    'order_source' => 'catalog',
                    'payment_type' => $data['payment_type'],
                    'payment_amount' => $data['payment_amount'],
                    'payment_proof_path' => null,
                    'payment_submitted_at' => $data['has_proof'] ? $createdAt->copy()->addHours(2) : null,
                    'ems_tax_amount' => $data['ems_amount'],
                    'ems_tax_status' => $data['ems_status'],
                    'ems_tax_due_date' => $data['ems_amount'] ? now()->addDays(7)->toDateString() : null,
                    'ems_tax_notes' => $data['ems_amount'] ? 'Rincian demo EMS, packing, dan pajak impor.' : null,
                    'payment_status' => null,
                    'notes' => $data['notes'],
                ],
            );
            $order->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->saveQuietly();

            $order->items()->delete();
            $total = 0;
            foreach ($data['items'] as $item) {
                $product = $products[$item['product']];
                $unitPrice = (float) $product->default_price;
                $subtotal = $unitPrice * $item['quantity'];
                $total += $subtotal;
                $order->items()->create([
                    'product_id' => $product->id,
                    'item_name' => $product->name,
                    'variant' => $product->variant,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'notes' => $item['notes'] ?? null,
                ]);
            }
            $order->forceFill(['total_amount' => $total])->saveQuietly();

            if ($overrideStatus) {
                $order->statusHistories()->updateOrCreate(
                    [
                        'new_status_id' => $overrideStatus->id,
                        'note' => 'Progress demo: '.$overrideStatus->name.'.',
                    ],
                    [
                        'old_status_id' => $batch->current_status_id,
                        'changed_by' => null,
                        'created_at' => $createdAt->copy()->addDays(max(1, (int) floor($data['age_days'] / 2))),
                    ],
                );
            }

            if ($index === 2) {
                $order->statusHistories()->updateOrCreate(
                    [
                        'new_status_id' => $statuses['arrived-admin']->id,
                        'note' => 'Paket tiba dan tagihan EMS diterbitkan.',
                    ],
                    [
                        'old_status_id' => $statuses['menunggu-pemesanan']->id,
                        'changed_by' => null,
                        'created_at' => now()->subDays(3),
                    ],
                );
            }
        }
    }

    private function orders(): array
    {
        return [
            [
                'code' => 'ORD-DEMO-001', 'batch' => 'OP-DEMO-001', 'age_days' => 2,
                'payment_status' => 'menunggu-dp', 'payment_type' => 'dp', 'payment_amount' => null,
                'override_status' => null, 'has_proof' => false, 'ems_amount' => null, 'ems_status' => 'not_billed',
                'notes' => 'Menunggu pembayaran DP pertama.',
                'items' => [['product' => 'Made My Night Album|Blue Hour Ver.', 'quantity' => 1]],
            ],
            [
                'code' => 'ORD-DEMO-002', 'batch' => 'OP-DEMO-002', 'age_days' => 8,
                'payment_status' => 'menunggu-pelunasan', 'payment_type' => 'dp', 'payment_amount' => 37000,
                'override_status' => 'secured', 'has_proof' => true, 'ems_amount' => null, 'ems_status' => 'not_billed',
                'notes' => 'DP diterima, menunggu pelunasan.',
                'items' => [['product' => 'Billlie SNAPISM Photocard|Random Member', 'quantity' => 2]],
            ],
            [
                'code' => 'ORD-DEMO-003', 'batch' => 'OP-DEMO-003', 'age_days' => 24,
                'payment_status' => 'lunas', 'payment_type' => 'full', 'payment_amount' => 570000,
                'override_status' => 'arrived-admin', 'has_proof' => true, 'ems_amount' => 78500, 'ems_status' => 'unpaid',
                'notes' => 'Pesanan lunas dengan tagihan EMS aktif.',
                'items' => [
                    ['product' => 'KWON EUNBI DEJAVU|Photobook A Ver.', 'quantity' => 1],
                    ['product' => 'KWON EUNBI DEJAVU|Photobook B Ver.', 'quantity' => 1],
                ],
            ],
            [
                'code' => 'ORD-DEMO-004', 'batch' => 'OP-DEMO-004', 'age_days' => 40,
                'payment_status' => 'lunas', 'payment_type' => 'full', 'payment_amount' => 298000,
                'override_status' => 'dikirim-ke-customer', 'has_proof' => true, 'ems_amount' => 42000, 'ems_status' => 'paid',
                'notes' => 'Paket sedang dikirim ke customer.',
                'items' => [['product' => 'JISOO Single Album CLICK|Red Ver.', 'quantity' => 1]],
            ],
            [
                'code' => 'ORD-DEMO-005', 'batch' => 'OP-DEMO-005', 'age_days' => 70,
                'payment_status' => 'lunas', 'payment_type' => 'full', 'payment_amount' => 265000,
                'override_status' => 'selesai', 'has_proof' => true, 'ems_amount' => 38000, 'ems_status' => 'paid',
                'notes' => 'Pesanan telah diterima customer.',
                'items' => [['product' => 'tripleS LOVElution|MYSTARG00DS POB', 'quantity' => 1]],
            ],
            [
                'code' => 'ORD-DEMO-006', 'batch' => 'OP-DEMO-006', 'age_days' => 95,
                'payment_status' => 'refund', 'payment_type' => 'full', 'payment_amount' => 245000,
                'override_status' => 'refunded', 'has_proof' => true, 'ems_amount' => null, 'ems_status' => 'not_billed',
                'notes' => 'Pesanan dibatalkan dan dana dikembalikan.',
                'items' => [['product' => 'RESCENE Official Plush Keyring|Mini Ver.', 'quantity' => 1]],
            ],
        ];
    }

}
