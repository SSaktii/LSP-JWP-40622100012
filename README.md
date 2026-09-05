# Aplikasi To-Do List

## Deskripsi
Aplikasi sederhana berbasis PHP untuk mencatat tugas harian.

## Fitur
- Tambah tugas
- Tandai tugas selesai
- Hapus tugas

## Struktur Folder
- `index.php` – halaman utama (tampilan & routing aksi form)
- `functions.php` – kumpulan fungsi logika (tambah, toggle status, hapus, tampilkan daftar)

## Struktur Data
Data tugas disimpan dalam array of objects (array asosiatif) di dalam session:
```php
$tasks = [
    ["id" => 1, "title" => "Belajar PHP", "status" => "belum"],
    ["id" => 2, "title" => "Kerjakan tugas UX", "status" => "selesai"],
];
```

## Cara Menjalankan
1. Salin folder `todolist` ke direktori `htdocs/` pada instalasi XAMPP Anda.
2. Jalankan Apache melalui XAMPP Control Panel.
3. Buka browser dan akses `http://localhost/LSP-JWP-40622100012`.

## Teknologi
- PHP (native, tanpa framework)
- Bootstrap 5 (CDN) untuk tampilan
- PHP Session sebagai penyimpanan sementara data tugas

## Kontributor
- [Sakti Putra Setiawan](https://github.com/SSaktii)
