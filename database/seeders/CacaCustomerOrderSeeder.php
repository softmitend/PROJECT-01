<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Member;
use App\Models\MemberOrder;
use App\Models\OrderStatus;
use App\Models\Product;
use Illuminate\Database\Seeder;
use RuntimeException;

class CacaCustomerOrderSeeder extends Seeder
{
    public function run(): void
    {
        $member = Member::query()
            ->whereRaw('LOWER(display_name) = ?', ['caca'])
            ->orWhereRaw('LOWER(username) = ?', ['caca'])
            ->first();

        if (! $member) {
            throw new RuntimeException('Customer caca belum tersedia. Login dengan akun LINE caca terlebih dahulu.');
        }

        $statuses = OrderStatus::query()->get()->keyBy('code');
        $requiredStatuses = collect([
            'secured', 'arrived-admin', 'dikirim-ke-customer', 'selesai', 'refunded',
            'menunggu-dp', 'menunggu-pelunasan', 'lunas', 'refund',
        ]);

        if ($requiredStatuses->contains(fn (string $code) => ! $statuses->has($code))) {
            throw new RuntimeException('Jalankan OrderStatusSeeder sebelum CacaCustomerOrderSeeder.');
        }

        $batchNumbers = collect($this->orders())->pluck('batch');
        $batches = Batch::query()->whereIn('batch_number', $batchNumbers)->get()->keyBy('batch_number');
        $products = Product::query()->get()->keyBy(fn (Product $product) => $product->name.'|'.$product->variant);

        if ($batches->count() !== $batchNumbers->unique()->count()) {
            throw new RuntimeException('Jalankan CustomerCatalogSeeder sebelum CacaCustomerOrderSeeder.');
        }

        foreach ($this->orders() as $data) {
            $batch = $batches[$data['batch']];
            $createdAt = now()->subDays($data['age_days']);
            $paymentStatus = $statuses[$data['payment_status']];
            $overrideStatus = $data['override_status'] ? $statuses[$data['override_status']] : null;

            $order = MemberOrder::query()->updateOrCreate(
                ['member_id' => $member->id, 'batch_id' => $batch->id],
                [
                    'order_code' => $data['code'],
                    'override_status_id' => $overrideStatus?->id,
                    'payment_status_id' => $paymentStatus->id,
                    'order_source' => 'catalog',
                    'payment_type' => $data['payment_type'],
                    'payment_amount' => $data['payment_amount'],
                    'payment_submitted_at' => $data['payment_amount'] ? $createdAt->copy()->addHours(2) : null,
                    'ems_tax_amount' => $data['ems_amount'],
                    'ems_tax_status' => $data['ems_status'],
                    'ems_tax_due_date' => $data['ems_amount'] ? now()->addDays(7)->toDateString() : null,
                    'ems_tax_notes' => $data['ems_amount'] ? 'Dummy EMS, packing, dan pajak impor untuk customer caca.' : null,
                    'payment_status' => null,
                    'notes' => $data['notes'],
                ],
            );

            $order->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->saveQuietly();

            $total = 0;
            foreach ($data['items'] as $itemData) {
                $product = $products->get($itemData['product']);
                if (! $product) {
                    throw new RuntimeException('Produk dummy tidak ditemukan: '.$itemData['product']);
                }

                $unitPrice = (float) $product->default_price;
                $subtotal = $unitPrice * $itemData['quantity'];
                $total += $subtotal;

                $order->items()->updateOrCreate(
                    ['product_id' => $product->id],
                    [
                        'item_name' => $product->name,
                        'variant' => $product->variant,
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                        'notes' => 'Data dummy customer caca.',
                    ],
                );
            }

            $order->forceFill(['total_amount' => $total])->saveQuietly();

            if ($overrideStatus) {
                $order->statusHistories()->updateOrCreate(
                    [
                        'new_status_id' => $overrideStatus->id,
                        'note' => 'Progress dummy caca: '.$overrideStatus->name.'.',
                    ],
                    [
                        'old_status_id' => $batch->current_status_id,
                        'changed_by' => null,
                        'created_at' => $createdAt->copy()->addDays(max(1, (int) floor($data['age_days'] / 2))),
                    ],
                );
            }
        }
    }

    private function orders(): array
    {
        return [
            [
                'code' => 'ORD-CACA-001', 'batch' => 'OP-DEMO-001', 'age_days' => 2,
                'payment_status' => 'menunggu-dp', 'payment_type' => 'dp', 'payment_amount' => null,
                'override_status' => null, 'ems_amount' => null, 'ems_status' => 'not_billed',
                'notes' => 'Dummy caca: menunggu pembayaran DP.',
                'items' => [['product' => 'Made My Night Album|Blue Hour Ver.', 'quantity' => 1]],
            ],
            [
                'code' => 'ORD-CACA-002', 'batch' => 'OP-DEMO-002', 'age_days' => 9,
                'payment_status' => 'menunggu-pelunasan', 'payment_type' => 'dp', 'payment_amount' => 92000,
                'override_status' => 'secured', 'ems_amount' => null, 'ems_status' => 'not_billed',
                'notes' => 'Dummy caca: DP diterima dan menunggu pelunasan.',
                'items' => [['product' => 'Billlie SNAPISM Photocard|Random Member', 'quantity' => 2]],
            ],
            [
                'code' => 'ORD-CACA-003', 'batch' => 'OP-DEMO-003', 'age_days' => 23,
                'payment_status' => 'lunas', 'payment_type' => 'full', 'payment_amount' => 570000,
                'override_status' => 'arrived-admin', 'ems_amount' => 78500, 'ems_status' => 'unpaid',
                'notes' => 'Dummy caca: paket tiba di admin dan memiliki tagihan EMS.',
                'items' => [
                    ['product' => 'KWON EUNBI DEJAVU|Photobook A Ver.', 'quantity' => 1],
                    ['product' => 'KWON EUNBI DEJAVU|Photobook B Ver.', 'quantity' => 1],
                ],
            ],
            [
                'code' => 'ORD-CACA-004', 'batch' => 'OP-DEMO-004', 'age_days' => 41,
                'payment_status' => 'lunas', 'payment_type' => 'full', 'payment_amount' => 298000,
                'override_status' => 'dikirim-ke-customer', 'ems_amount' => 42000, 'ems_status' => 'paid',
                'notes' => 'Dummy caca: paket sedang dikirim ke customer.',
                'items' => [['product' => 'JISOO Single Album CLICK|Red Ver.', 'quantity' => 1]],
            ],
            [
                'code' => 'ORD-CACA-005', 'batch' => 'OP-DEMO-005', 'age_days' => 72,
                'payment_status' => 'lunas', 'payment_type' => 'full', 'payment_amount' => 265000,
                'override_status' => 'selesai', 'ems_amount' => 38000, 'ems_status' => 'paid',
                'notes' => 'Dummy caca: pesanan telah diterima.',
                'items' => [['product' => 'tripleS LOVElution|MYSTARG00DS POB', 'quantity' => 1]],
            ],
            [
                'code' => 'ORD-CACA-006', 'batch' => 'OP-DEMO-006', 'age_days' => 96,
                'payment_status' => 'refund', 'payment_type' => 'full', 'payment_amount' => 245000,
                'override_status' => 'refunded', 'ems_amount' => null, 'ems_status' => 'not_billed',
                'notes' => 'Dummy caca: pesanan dibatalkan dan dana dikembalikan.',
                'items' => [['product' => 'RESCENE Official Plush Keyring|Mini Ver.', 'quantity' => 1]],
            ],
        ];
    }
}
