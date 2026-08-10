# OceanPaws Order

Sistem pengelolaan pesanan Kpop merchandise berdasarkan customer, batch pembelian, produk, dan status progress.

## Alur utama

- Customer dapat mengecek satu pesanan tanpa login melalui kode pesanan.
- Customer dapat mencari email yang didaftarkan admin untuk melihat seluruh riwayat pembeliannya tanpa login.
- Status pesanan mengikuti status batch secara default. Admin dapat memberi override pada satu pesanan atau satu item jika kondisinya berbeda.
- Admin mengelola nama, email, telepon, alamat customer, katalog produk, batch, pesanan, status, dan log perubahan status.
- Produk aktif muncul pada dropdown form pesanan dan otomatis mengisi nama, varian, serta harga awal.

Alur status awal mengikuti pola spreadsheet operasional OceanPaws: `Ordered → Arrived Warehouse → Flight/Sea to Indonesia → Arrived Admin → Siap Distribusi → Selesai`.

## Menjalankan project

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Konfigurasi bawaan menggunakan SQLite. Pastikan ekstensi `pdo_sqlite` dan `sqlite3` aktif, lalu buat file `database/database.sqlite` sebelum migrasi jika belum tersedia.

## Akun demo

- Admin: `admin@example.com` / `password`
- Pencarian riwayat customer: `dinda@example.com`
- Tracking publik: `ORD-GO-NCT-0002`

Ganti seluruh kredensial demo sebelum digunakan di production.

## Pengujian

```bash
php artisan test
npm run build
```
