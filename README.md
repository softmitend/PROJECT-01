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

## Deployment ke Vercel

Project sudah dilengkapi dengan `vercel.json`, entrypoint `api/index.php`, dan konfigurasi filesystem serverless. Runtime yang digunakan adalah `vercel-php@0.8.0` (PHP 8.4) agar sesuai dengan dependency Laravel dan Symfony pada `composer.lock`.

### 1. Siapkan database production

SQLite lokal tidak cocok untuk Vercel karena filesystem function tidak persisten. Gunakan database PostgreSQL atau MySQL eksternal, misalnya Neon, Supabase, atau layanan database lain yang dapat diakses dari Vercel.

Untuk PostgreSQL, siapkan nilai berikut:

```text
DB_CONNECTION=pgsql
DB_URL=postgresql://USER:PASSWORD@HOST:5432/DATABASE?sslmode=require
```

### 2. Tambahkan Environment Variables di Vercel

Tambahkan minimal variabel berikut untuk Production, Preview, dan Development sesuai kebutuhan:

```text
APP_NAME=OceanPaws Order
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...
APP_URL=https://nama-project.vercel.app
DB_CONNECTION=pgsql
DB_URL=postgresql://...
LOG_CHANNEL=stderr
CACHE_STORE=array
SESSION_DRIVER=cookie
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
QUEUE_CONNECTION=sync
```

Buat `APP_KEY` dari terminal lokal:

```bash
php artisan key:generate --show
```

Jangan menyimpan `APP_KEY`, URL database, atau password di `vercel.json` maupun repository.

### 3. Jalankan migrasi database production

Jalankan migrasi dari komputer lokal menggunakan URL database direct/non-pooling. Contoh PowerShell:

```powershell
$env:APP_ENV='production'
$env:DB_CONNECTION='pgsql'
$env:DB_URL='postgresql://USER:PASSWORD@HOST:5432/DATABASE?sslmode=require'
php artisan migrate --force
php artisan db:seed --force
```

Seeder membuat akun demo. Ganti password admin dan hapus data demo sebelum website production digunakan secara publik.

### 4. Hubungkan repository ke Vercel

Import repository GitHub pada dashboard Vercel dengan pengaturan:

- Framework Preset: `Other`
- Root Directory: `./`
- Build dan Output Directory: biarkan mengikuti `vercel.json`
- Install Command: biarkan otomatis

Setelah environment variables tersimpan, jalankan deploy. Setiap push berikutnya ke branch production akan membuat deployment baru secara otomatis.

### Catatan serverless

- Asset Vite dibangun saat deployment dan dilayani melalui entrypoint serverless.
- View, cache, dan file sementara Laravel diarahkan ke `/tmp`.
- Session menggunakan cookie terenkripsi sehingga login admin tidak bergantung pada filesystem function.
- Upload file permanen harus memakai object storage seperti S3; `/tmp` tidak persisten.
- Migrasi tidak dijalankan otomatis saat build untuk menghindari perubahan database dari Preview Deployment.
