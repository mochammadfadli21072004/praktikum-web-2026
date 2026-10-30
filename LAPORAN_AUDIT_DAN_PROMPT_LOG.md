# Laporan Audit Output AI, Refactoring Code Smell, dan Prompt Log (Pertemuan 9 & 10)

**Mata Kuliah:** Framework Pemrograman Web (MK43)  
**Sub-CPMK-10-1-3:** Debugging, Refactoring, Testing Dasar, serta Menilai Kualitas Kode Laravel termasuk Output AI

---

## 1. Checklist Audit Output AI (Materi 1 — Pertemuan 9)

| No | Pertanyaan Checklist Audit | Status Awal (Mentah AI) | Status Sesudah Audit & Refactor |
|---|---|---|---|
| 1 | Logikanya sesuai kebutuhan, bukan sekadar "jalan"? | ❌ Gagal (flash session salah kunci, migrasi duplikat, kategori kosong hilang di laporan) | ✅ Sesuai kebutuhan bisnis aplikasi |
| 2 | Ada input yang tidak divalidasi atau disanitasi? | ⚠️ Sebagian (`description` belum divalidasi di Web, `stock: null` lolos di API memicu error 500) | ✅ Semua input divalidasi melalui `FormRequest` (`$request->validated()`) |
| 3 | Query rawan N+1 atau celah SQL injection? | ⚠️ `show()` belum eager load `category`; `laporan()` memakai `DB::raw()` & bypass Eloquent | ✅ Semua relasi memakai Eloquent (`with`, `load`, `withCount`, `withSum`) |
| 4 | Penamaan variabel/method konsisten dengan konvensi Laravel? | ⚠️ URL hardcode di Blade, namespace `ProductRequest` tertukar, `$fillable` duplikat | ✅ Mengikuti konvensi Laravel (Named Routes, FormRequest, Policy, Service, Enum) |
| 5 | Ada test yang membuktikan perilakunya benar? | ❌ Test gagal berjalan karena migrasi duplikat & method model belum lengkap | ✅ Seluruh Unit Test & Feature Test lulus (`PASS`) |

---

## 2. Daftar Temuan Audit Output AI (`A-01` s/d `A-05`)

| Kode | Lokasi File | Masalah yang Ditemukan | Dampak | Perbaikan yang Dilakukan |
|---|---|---|---|---|
| **A-01** | `database/migrations/2026_10_21_042504_add_image_to_products_table.php` & `app/Models/Product.php` | AI membuat migrasi baru penambah kolom `image` tanpa memeriksa bahwa migrasi `2026_09_27_221952_add_image_to_products_table.php` sudah ada; `'image'` juga ditulis dua kali di `$fillable`. | `migrate:fresh` & `RefreshDatabase` langsung gagal (`duplicate column name: image`). | Menghapus file migrasi duplikat dan merapikan `$fillable` di `Product.php`. |
| **A-02** | `app/Http/Controllers/ProductController.php` | AI menggunakan `->with('success', ...)` sedangkan `layouts/app.blade.php` membaca `session('status')`. | Pesan notifikasi sukses tidak pernah tampil ke pengguna setelah tambah/ubah/hapus produk. | Mengubah seluruh flash session di `ProductController` menjadi `->with('status', ...)`. |
| **A-03** | `app/Http/Controllers/Api/V1/ProductController.php` & `app/Http/Requests/ProductRequest.php` | Validasi API mengizinkan `'stock' => ['nullable', ...]` padahal kolom DB `NOT NULL DEFAULT 0`. Selain itu `app/Http/Requests/ProductRequest.php` salah berisi namespace `Api`. | Mengirim JSON `"stock": null` menyebabkan crash HTTP `500 Internal Server Error`. | Memakai `['sometimes', 'integer', 'min:0']` di `Api\ProductRequest` (menolak `null` dengan `422`) dan memperbaiki class Web `App\Http\Requests\ProductRequest`. |
| **A-04** | `app/Http/Controllers/ProductController.php` (`laporan()` & `show()`) | `laporan()` mem-bypass Eloquent menggunakan `DB::table(...)->join(...)->select(DB::raw(...))` dan menghilangkan kategori yang belum punya produk; `show()` belum melakukan eager load `category`. | Melanggar konvensi Eloquent Laravel dan tidak menampilkan kategori berstok 0. | Mengubah `laporan()` memakai `Category::withCount('products as jumlah')->withSum('products as total_stok', 'stock')` dan menambahkan `$product->load('category')` di `show()`. |
| **A-05** | `routes/api.php` & `app/Http/Requests/Api/ProductRequest.php` | Endpoint tulis API (`POST`, `PUT`, `DELETE` `/api/v1/products`) belum dilindungi autentikasi token (`auth:sanctum`). | Siapa pun tanpa token masih dapat mengubah data lewat API v1. | Dicatat sebagai temuan keamanan untuk tahap *Security & Maintainability* (Pertemuan 11) agar kontrak pengujian Pertemuan 7 tetap kompatibel. |

---

## 3. Analisis Code Smell & Teknik Refactoring (Materi 2, 3, 4 — Pertemuan 9)

| Code Smell | Lokasi Sebelum Refactor | Teknik Refactoring | Lokasi Sesudah Refactor |
|---|---|---|---|
| **Fat Controller** | `ProductController` mencampur aturan validasi, otorisasi, upload/hapus file `Storage`, dan query di satu class. | **Extract Class/Service** & **Form Request** | Validasi & otorisasi dipindah ke `App\Http\Requests\ProductRequest`; urusan file gambar dipindah ke `App\Services\ProductImageService`. |
| **Duplicate Validation** | Aturan validasi ditulis berulang di `ProductController` (`store` & `update`) serta `Api\V1\ProductController` (`store` & `update`). | **Form Request** & **Extract Method** | Dipusatkan di `App\Http\Requests\ProductRequest` dan `App\Http\Requests\Api\ProductRequest`; query dropdown kategori disatukan lewat `categoryOptions()`. |
| **Duplicate Formatting Logic** | Rumus `'Rp ' . number_format(...)` ditulis ganda di `Product::formattedPrice()` dan `AppServiceProvider` (`@rupiah`). | **Extract Class** | Dipusatkan di `App\Support\PriceFormatter::rupiah()`. |
| **Magic Number & Inconsistent Rule** | Batas stok menipis ditulis angka *hardcode* `10` di `Badge.php` tetapi angka `5` di `V2\ProductResource.php`. | **Replace Magic Value (Enum & Constant)** | Dipusatkan di `App\Enums\StockStatus` (`LOW_STOCK_THRESHOLD = 10`) dan dipakai bersama oleh `Badge` serta `V2\ProductResource`. |
| **Magic String (`'admin'`, `'kasir'`)** | String `'admin'` dan `'kasir'` tersebar di `ProductPolicy`, `AppServiceProvider`, `UserController`, `UserFactory`, dan `dashboard.blade.php`. | **Replace Magic Value & Helper Method** | Dibuat konstanta `User::ROLE_ADMIN`, `User::ROLE_KASIR` serta method `isAdmin()` dan `isKasir()` pada model `User`. |

---

## 4. Prompt Log (Materi 5 — Pertemuan 9)

| Tanggal & Tool | Prompt | Ringkasan Output | Modifikasi Manual | Alasan Perubahan |
|---|---|---|---|---|
| 2026-10-21 — AI Coding Assistant (Pertemuan 8) | *"Buatkan fitur CRUD produk lengkap beserta upload gambar (create, store, edit, update, destroy) dan halaman laporan jumlah produk serta total stok per kategori di Laravel."* | Menghasilkan method CRUD di `ProductController`, view `create`/`edit`/`laporan`, menambahkan `'image'` di `Product::$fillable`, serta membuat migrasi baru `add_image_to_products_table`. | 1) Menghapus migrasi `2026_10_21_042504_add_image_to_products_table.php` dan duplikat `'image'` di `$fillable`.<br>2) Mengganti `with('success', ...)` menjadi `with('status', ...)`.<br>3) Mengganti `DB::table` + `DB::raw` pada `laporan()` menjadi Eloquent `Category::withCount()->withSum()`. | 1) Kolom `image` sudah ada di migrasi sebelumnya sehingga memicu error duplikat kolom saat testing.<br>2) Layout aplikasi membaca `session('status')`, bukan `session('success')`.<br>3) Mengikuti konvensi Eloquent dan tetap menampilkan kategori dengan 0 produk. |
| 2026-10-30 — Antigravity AI (Pertemuan 9) | *"Audit seluruh kode hasil AI pada project praktikum-laravel, identifikasi code smell (Fat Controller, Duplicate Validation, Magic Value), lalu refactor secara bertahap tanpa mengubah perilaku aplikasi."* | Mengusulkan pemisahan `ProductImageService`, `PriceFormatter`, Enum `StockStatus`, konstanta role di `User`, serta pemakaian `FormRequest` untuk Web dan API. | Memperbaiki namespace & otorisasi `App\Http\Requests\ProductRequest` agar memanggil `ProductPolicy` (`create`/`update`), memperbarui `Api\ProductRequest` untuk menolak `stock: null` (`422`), dan menambahkan field opsional `description` serta `colspan="7"` di Blade. | Memastikan tanggung jawab tunggal (SRP), menyamakan aturan stok menipis (`<= 10`) di Web & API v2, serta mencegah error database 500 ketika `stock` bernilai `null`. |
| 2026-10-30 — Antigravity AI (Pertemuan 10) | *"Buatkan Unit Test (logika murni) dan Feature Test (alur HTTP & database dengan RefreshDatabase) untuk memverifikasi hasil refactoring aplikasi praktikum-laravel."* | Menghasilkan Unit Test (`PriceFormatterTest`, `StockStatusTest`, `UserRoleTest`, `ProductFormattedPriceTest`) dan Feature Test (`AuthAndRoleTest`, `ProductWebTest`, `ProductApiTest`). | Menambahkan skenario uji batas (*boundary test* stok `0`, `1`, `10`, `11`), pengecekan `Storage::fake('public')` untuk hapus/simpan gambar, serta pengujian `stock: null` di API v1 mengembalikan status `422`. | Memastikan setiap hasil refactoring terverifikasi otomatis baik pada logika murni maupun alur HTTP + SQLite *in-memory*. |
