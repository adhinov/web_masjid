# Website Masjid (web_masjid)

Website sederhana untuk Masjid Jami' Abi Musa Al Asy'ari. Berisi halaman utama jadwal sholat dan halaman beranda berisi foto masjid + mukadimah.

## Fitur Utama
- Halaman utama (jadwal sholat + countdown)
- Halaman beranda khusus foto masjid dan mukadimah
- Navbar sticky
- Responsive layout sederhana

## Cara Menjalankan (Lokal)
1. Salin file env:
   - `copy .env.example .env`
2. Install dependency:
   - `composer install`
3. Generate key:
   - `php artisan key:generate`
4. Jalankan server:
   - `php artisan serve`

Buka di browser:
- `http://127.0.0.1:8000/` (Halaman utama)
- `http://127.0.0.1:8000/beranda` (Foto masjid + mukadimah)

## Catatan
CSS utama ada di `public/css/app.css`.
