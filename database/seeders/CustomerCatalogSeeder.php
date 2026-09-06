<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\OrderStatus;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class CustomerCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = OrderStatus::query()
            ->whereIn('code', ['menunggu-pemesanan', 'sudah-dipesan', 'selesai'])
            ->get()
            ->keyBy('code');

        if ($statuses->count() !== 3) {
            throw new RuntimeException('Jalankan OrderStatusSeeder sebelum CustomerCatalogSeeder.');
        }

        $qrisPath = $this->copyPublicAsset('assets/qris.png', 'demo/customer/qris.png');

        $products = collect([
            'made-my-night-blue' => ['Made My Night Album', 'Blue Hour Ver.', 'Album lengkap dengan benefit pre-order.', 315000],
            'made-my-night-pink' => ['Made My Night Album', 'Pink Moon Ver.', 'Album lengkap dengan photocard acak.', 315000],
            'billie-snapism' => ['Billlie SNAPISM Photocard', 'Random Member', 'Photocard event SNAPISM.', 92000],
            'eunbi-dejavu-a' => ['KWON EUNBI DEJAVU', 'Photobook A Ver.', 'Album resmi Korea dengan POB.', 285000],
            'eunbi-dejavu-b' => ['KWON EUNBI DEJAVU', 'Photobook B Ver.', 'Album resmi Korea dengan POB.', 285000],
            'jisoo-click' => ['JISOO Single Album CLICK', 'Red Ver.', 'Single album dengan inclusions resmi.', 298000],
            'triples-love' => ['tripleS LOVElution', 'MYSTARG00DS POB', 'Benefit eksklusif dan album sealed.', 265000],
            'rescene-plush' => ['RESCENE Official Plush Keyring', 'Mini Ver.', 'Plush keyring official merchandise.', 245000],
            'qwer-merch' => ['QWER 1993Studio Merchandise', 'T-Shirt Navy', 'Merchandise resmi dengan ukuran pilihan.', 435000],
            'you-dayeon' => ['YOU DAYEON Single Album', 'Buddy Ver.', 'Single album sealed dari Korea.', 235000],
        ])->mapWithKeys(function (array $data, string $key) {
            [$name, $variant, $description, $price] = $data;
            $product = Product::query()->updateOrCreate(
                ['name' => $name, 'variant' => $variant],
                ['description' => $description, 'default_price' => $price, 'is_active' => true],
            );

            return [$key => $product];
        });

        $batches = [
            [
                'number' => 'OP-DEMO-001',
                'name' => 'LE SSERAFIM — Made My Night',
                'description' => 'Open pre-order album Made My Night dengan pilihan dua versi.',
                'image' => 'https://images.pexels.com/photos/15158326/pexels-photo-15158326/free-photo-of-close-up-of-musical-instrument-items.jpeg?auto=compress&fit=crop&w=900&h=720',
                'source' => 'https://www.pexels.com/photo/close-up-of-musical-instrument-items-15158326/',
                'deadline' => now()->addDays(6)->endOfDay(),
                'status' => 'menunggu-pemesanan',
                'products' => ['made-my-night-blue', 'made-my-night-pink'],
            ],
            [
                'number' => 'OP-DEMO-002',
                'name' => 'Billlie SNAPISM Photocard',
                'description' => 'Claim photocard SNAPISM untuk seluruh member Billlie.',
                'image' => 'https://images.pexels.com/photos/3944104/pexels-photo-3944104.jpeg?auto=compress&fit=crop&w=900&h=720',
                'source' => 'https://www.pexels.com/photo/black-vinyl-record-on-white-table-3944104/',
                'deadline' => now()->addDays(9)->endOfDay(),
                'status' => 'menunggu-pemesanan',
                'products' => ['billie-snapism'],
            ],
            [
                'number' => 'OP-DEMO-003',
                'name' => 'KWON EUNBI — DEJAVU',
                'description' => 'Pre-order album DEJAVU dengan dua versi photobook.',
                'image' => 'https://images.pexels.com/photos/17624415/pexels-photo-17624415.jpeg?auto=compress&fit=crop&w=900&h=720',
                'source' => 'https://www.pexels.com/photo/set-of-guitar-accessories-17624423/',
                'deadline' => now()->addDays(3)->endOfDay(),
                'status' => 'menunggu-pemesanan',
                'products' => ['eunbi-dejavu-a', 'eunbi-dejavu-b'],
            ],
            [
                'number' => 'OP-DEMO-004',
                'name' => 'JISOO — CLICK Single Album',
                'description' => 'Batch contoh yang sudah melewati batas pemesanan.',
                'image' => 'https://images.pexels.com/photos/3721380/pexels-photo-3721380.jpeg?auto=compress&fit=crop&w=900&h=720',
                'source' => 'https://www.pexels.com/photo/messy-pile-of-vinyl-disks-and-guitar-3721380/',
                'deadline' => now()->subDays(2)->endOfDay(),
                'status' => 'sudah-dipesan',
                'products' => ['jisoo-click'],
            ],
            [
                'number' => 'OP-DEMO-005',
                'name' => 'tripleS LOVElution POB',
                'description' => 'PO album dan benefit eksklusif tripleS LOVElution.',
                'image' => 'https://images.pexels.com/photos/28878879/pexels-photo-28878879/free-photo-of-vintage-music-collection-with-vinyl-and-ukulele.jpeg?auto=compress&fit=crop&w=900&h=720',
                'source' => 'https://www.pexels.com/photo/vintage-music-collection-with-vinyl-and-ukulele-28878879/',
                'deadline' => now()->addDays(12)->endOfDay(),
                'status' => 'menunggu-pemesanan',
                'products' => ['triples-love'],
            ],
            [
                'number' => 'OP-DEMO-006',
                'name' => 'RESCENE Plush & QWER Merch',
                'description' => 'Batch merchandise arsip untuk contoh pesanan selesai dan refund.',
                'image' => 'https://images.pexels.com/photos/15004128/pexels-photo-15004128.jpeg?auto=compress&fit=crop&w=900&h=720',
                'source' => 'https://www.pexels.com/photo/design-guitar-picks-by-casette-and-music-notes-15004128/',
                'deadline' => now()->subDays(20)->endOfDay(),
                'status' => 'selesai',
                'products' => ['rescene-plush', 'qwer-merch', 'you-dayeon'],
            ],
        ];

        foreach ($batches as $index => $data) {
            $batchAttributes = [
                    'batch_name' => $data['name'],
                    'current_status_id' => $statuses[$data['status']]->id,
                    'description' => $data['description'],
                    'notes' => 'Data demo customer. Sumber foto stok: '.$data['source'],
                    'catalog_image_path' => $data['image'],
                    'qris_image_path' => $qrisPath,
                    'ordering_deadline' => $data['deadline'],
                    'is_catalog_visible' => true,
                    'started_at' => now()->subDays(30 - ($index * 3)),
                    'completed_at' => $data['status'] === 'selesai' ? now()->subDays(7) : null,
                    'is_archived' => false,
            ];
            if (Schema::hasColumn('batches', 'catalog_image_disk')) {
                $batchAttributes['catalog_image_disk'] = null;
            }

            $batch = Batch::query()->updateOrCreate(
                ['batch_number' => $data['number']],
                $batchAttributes,
            );

            $pivotData = [];
            foreach ($data['products'] as $sortOrder => $productKey) {
                $product = $products[$productKey];
                $fullPrice = (float) $product->default_price;
                $pivotData[$product->id] = [
                    'dp_price' => round($fullPrice * .4),
                    'full_price' => $fullPrice,
                    'sort_order' => $sortOrder + 1,
                    'is_available' => true,
                ];
            }
            $batch->products()->sync($pivotData);

            $batch->statusHistories()->updateOrCreate(
                [
                    'new_status_id' => $statuses[$data['status']]->id,
                    'note' => 'Status demo katalog: '.$statuses[$data['status']]->name.'.',
                ],
                ['old_status_id' => null, 'changed_by' => null, 'created_at' => now()->subDays(25 - ($index * 3))],
            );
        }
    }

    private function copyPublicAsset(string $source, string $destination): ?string
    {
        $sourcePath = public_path($source);
        if (! is_file($sourcePath)) {
            return null;
        }

        Storage::disk('public')->put($destination, file_get_contents($sourcePath));

        return $destination;
    }
}
