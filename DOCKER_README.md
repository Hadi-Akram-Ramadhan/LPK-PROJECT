# Panduan Setup Docker (Laravel Sail)

Panduan ini ditujukan bagi anggota tim agar dapat menjalankan projek LPK CBT di komputer lokal tanpa perlu menginstal PHP, MySQL, atau Node.js secara manual.

## 📋 Prasyarat
1. **Docker Desktop**: Pastikan Docker sudah terinstal dan berjalan.
2. **WSL 2 (Untuk Windows)**: Sangat disarankan menjalankan Docker di backend WSL 2.

## 🚀 Memulai (Setup Awal)

1. **Clone dan Masuk ke Folder Projek**
   ```bash
   git clone <repository-url>
   cd LPK
   ```

2. **Salin Konfigurasi Environment**
   ```bash
   cp .env.docker.example .env
   ```

3. **Install Dependensi & Jalankan Container**
   Jika Anda sudah punya PHP & Composer di lokal:
   ```bash
   composer install
   ./vendor/bin/sail up -d
   ```
   Jika **TIDAK** punya PHP di lokal sama sekali:
   ```bash
   docker run --rm \
       -u "$(id -u):$(id -g)" \
       -v "$(pwd):/var/www/html" \
       -w /var/www/html \
       laravelsail/php84-composer:latest \
       composer install --ignore-platform-reqs
   
   ./vendor/bin/sail up -d
   ```

4. **Generate Key & Migrasi Database**
   ```bash
   ./vendor/bin/sail artisan key:generate
   ./vendor/bin/sail artisan migrate --seed
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run dev
   ```

## 🛠️ Perintah Sehari-hari

- **Menjalankan Server**: `./vendor/bin/sail up -d`
- **Mematikan Server**: `./vendor/bin/sail stop`
- **Melihat Log**: `./vendor/bin/sail logs -f`
- **Akses Website**: [http://localhost](http://localhost)
- **Monitoring Email**: [http://localhost:8025](http://localhost:8025) (Mailpit)
- **Eksekusi Perintah**: Gunakan `./vendor/bin/sail` di depan perintah biasa.
  - Contoh: `./vendor/bin/sail artisan make:controller MyController`

## 📡 Networking & WebSocket (Reverb)

Container `reverb` akan otomatis berjalan. Jika Anda mengakses dari perangkat lain di jaringan yang sama:
1. Ubah `VITE_REVERB_HOST` di `.env` menjadi alamat IP komputer server Anda.
2. Jalankan ulang Vite: `./vendor/bin/sail npm run dev`.

---
*Dibuat untuk mempermudah kolaborasi tim LPK CBT.*
