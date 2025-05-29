# Aplikasi Manajemen Data Produk

Aplikasi ini dibangun untuk membantu pengelolaan data produk secara efisien dan interaktif. Fitur utama mencakup CRUD produk, pencarian, pagination, serta tampilan antarmuka yang responsif. Aplikasi dikembangkan menggunakan Laravel sebagai backend dan Blade sebagai frontend.

## Fitur Utama

- Manajemen data produk (Create, Read, Update, Delete)
- Pencarian produk berbasis kata kunci
- Pagination dan sorting
- Validasi form input
- Tampilan responsif (desktop & mobile)
- API RESTful yang terstruktur

## Tech Stack

- **Backend**: Laravel (versi 9.5)
- **Templating**: Blade
- **Database**: MySQL / PostgreSQL

## Struktur

```
├── app/
├── resources/
│   ├── views/       # Blade templates
├── routes/
│   └── web.php      # Route endpoint
├── public/
├── database/
│   ├── migrations/  # Struktur tabel
```

## Instalasi

1. Clone repo ini
   ```bash
   git clone https://github.com/Mirwan100/Laravel_project
   ```

2. Install dependency Laravel
   ```bash
   composer install
   ```

3. Install dependency frontend
   ```bash
   npm install && npm run dev
   ```

4. Setup file `.env`
   ```
   cp .env.example .env
   php artisan key:generate
   ```

5. Buat database dan jalankan migrasi
   ```bash
   php artisan migrate
   ```

6. Jalankan server
   ```bash
   php artisan serve
   ```

## Catatan Tambahan

- Aplikasi dapat dikembangkan lebih lanjut dengan fitur seperti upload gambar produk, autentikasi pengguna, dan export data.


