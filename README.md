# PUMA Website - Comprehensive Guide

## 📚 Daftar Isi
1. [Pengenalan Fitur](#1-pengenalan-fitur)
2. [Prasyarat Sistem](#2-prasyarat-sistem)
3. [Instalasi MySQL & Environment](#3-instalasi-mysql--environment)
4. [Instalasi Website (Lokal)](#4-instalasi-website-lokal)
5. [Panduan Deployment (Online)](#5-panduan-deployment-online)

---

## 1. Pengenalan Fitur

Website ini terdiri dari dua bagian utama: **Frontend** (Halaman Publik) dan **Backend/Admin Panel** (Halaman Pengelola).

### A. Frontend (Halaman Pengunjung)
*   **Home / Beranda**: Menampilkan Banner dinamis (bisa diganti dari Admin), Sambutan Ketua, dan berita terbaru.
*   **Events / Timeline**: Daftar kegiatan organisasi yang diambil dari database. Menampilkan status "Planned", "Upcoming", atau "Closed Case" (Selesai).
*   **News / Berita**: Artikel dan pengumuman organisasi.
*   **Organization**: Struktur organisasi, visi misi, dan profil anggota (Cabinet).

### B. Admin Dashboard (Halaman Pengurus)
Hanya bisa diakses oleh user yang login.
*   **Login System**: Keamanan akses menggunakan email dan password.
*   **Banner Management**: Upload gambar banner untuk halaman depan. Bisa mengaktifkan/menonaktifkan banner.
*   **Event Management**: CRUD (Create, Read, Update, Delete) data acara.
    *   Input: Judul, Deskripsi, Tanggal, Gambar, Status.
*   **News Management**: Menulis artikel berita dengan kategori.
*   **User Management**: Mengelola akun admin lain.

---

## 2. Prasyarat Sistem

Sebelum menginstall, pastikan komputer Anda memiliki:

1.  **XAMPP** (atau Laragon/Wamp): Untuk Database MySQL dan PHP.
    *   *Download*: [apachefriends.org](https://www.apachefriends.org/)
    *   Pastikan PHP versi **8.2** atau lebih baru.
2.  **Composer**: Untuk menginstall library PHP (Laravel).
    *   *Download*: [getcomposer.org](https://getcomposer.org/)
3.  **Node.js & NPM**: Untuk menginstall frontend (Vue.js).
    *   *Download*: [nodejs.org](https://nodejs.org/) (Pilih versi LTS).
4.  **Git** (Opsional): Untuk clone repository.

---

## 3. Instalasi MySQL & Environment

1.  **Jalankan XAMPP**:
    *   Buka XAMPP Control Panel.
    *   Klik **Start** pada **Apache** dan **MySQL**.
    *   Pastikan indikator berwarna hijau.

2.  **Siapkan Database**:
    *   Buka browser ke `http://localhost/phpmyadmin`.
    *   Klik **New**.
    *   Isi nama database: `puma_backend`.
    *   Klik **Create**.

---

## 4. Instalasi Website (Lokal)

Ikuti langkah ini jika Anda baru pertama kali menjalankan project di komputer baru.

### Langkah 1: Setup Backend (Laravel)
Buka terminal (Command Prompt/PowerShell) dan arahkan ke folder `PUMA-Backend`.

```bash
cd path/to/PUMA-Website/PUMA-Backend
```

1.  **Install Dependencies**:
    ```bash
    composer install
    ```
2.  **Konfigurasi Environment**:
    *   Copy file `.env.example` menjadi `.env`.
    *   Buka file `.env` dan atur koneksi database:
        ```ini
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=puma_backend
        DB_USERNAME=root
        DB_PASSWORD=
        ```
3.  **Generate App Key**:
    ```bash
    php artisan key:generate
    ```
4.  **Migrasi Database** (Membuat tabel):
    ```bash
    php artisan migrate:fresh --seed
    ```
    *(Note: `--seed` akan membuat satu user admin default)*.
5.  **Jalankan Server**:
    ```bash
    php artisan serve
    ```
    *Backend berjalan di: `http://localhost:8000`*

### Langkah 2: Setup Frontend (Vue.js)
Buka terminal **baru** (biarkan terminal backend tetap jalan), arahkan ke folder `PUMA-Website`.

```bash
cd path/to/PUMA-Website/PUMA-Website
```

1.  **Install Dependencies**:
    ```bash
    npm install
    ```
2.  **Jalankan Server Development**:
    ```bash
    npm run dev
    ```
    *Frontend berjalan di: `http://localhost:5173`*

---

## 5. Panduan Deployment (Online)

Untuk membuat website dapat diakses orang lain (Online), Anda membutuhkan **Hosting** (Shared Hosting atau VPS) dan **Domain**.

### Opsi A: Shared Hosting (Cpanel) - Lebih Mudah & Murah
*Cocok untuk website organisasi standar.*

#### 1. Persiapan File
*   **Backend**:
    *   Di komputer lokal, jalankan `composer install --optimize-autoloader --no-dev`.
    *   Zip/Compress semua isi folder `PUMA-Backend`.
*   **Frontend**:
    *   Di komputer lokal, jalankan `npm run build`.
    *   Akan muncul folder `dist`. Zip isi folder `dist` ini.

#### 2. Upload ke Hosting
1.  Masuk ke **File Manager** di Cpanel.
2.  **Backend**:
    *   Buat folder baru di luar `public_html`, misal `laravel_core`.
    *   Upload dan Extract zip backend ke sana.
3.  **Frontend**:
    *   Upload isi zip folder `dist` (hasil build) ke dalam `public_html`.
    *   *Penyesuaian*: Anda mungkin perlu mengedit `index.html` di `public_html` agar path asset (js/css) sesuai.

#### 3. Konfigurasi Database
1.  Di Cpanel, buka **MySQL Database Wizard**.
2.  Buat Database baru dan User Database baru. Catat nama database, username, dan passwordnya.
3.  Buka **phpMyAdmin** di Cpanel, import file SQL dari database lokal Anda (bisa di-export dulu dari localhost/phpmyadmin).
4.  Edit file `.env` di folder `laravel_core`:
    *   Sesuaikan `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` dengan yang ada di hosting.
    *   Ubah `APP_URL` ke nama domain Anda (misal `https://pumainformatics.com`).

#### 4. Menghubungkan Frontend ke Backend
*   Di folder `public_html`, pastikan file `index.html` memuat script yang benar.
*   Karena Frontend Vue kita adalah SPA (Single Page Application), Anda perlu mengatur `.htaccess` di `public_html` agar semua request diarahkan ke `index.html` (kecuali api).
*   **PENTING**: Endpoint API di frontend (file `.env` frontend saat build) harus mengarah ke URL backend online.
    *   Sebelum `npm run build`, ubah `.env` di frontend: `VITE_API_BASE_URL=https://pumainformatics.com/api` (atau sesuaikan dengan setup backend Anda).
    *   Di Shared Hosting, biasanya kita perlu memindahkan folder `public` dari laravel ke `public_html/api` atau cara lain agar index.php laravel bisa diakses.
    *   *Cara yang lebih mudah untuk pemula*: Gunakan subdomain untuk backend (misal `api.pumainformatics.com`) dan taruh file Laravel di subdomain tersebut. Lalu frontend di domain utama.

### Opsi B: VPS (Virtual Private Server) - Lebih Fleksibel
*Disarankan jika Anda familiar dengan Linux (Ubuntu).*

1.  Sewa VPS (DigitalOcean, Linode, IDCloudHost).
2.  Install **Nginx**, **MySQL**, **PHP**, **Node.js**.
3.  Clone repo project Anda ke VPS.
4.  Setup Nginx Server Block untuk melayani:
    *   Frontend (folder `PUMA-Website/dist`) sebagai static site.
    *   Backend (folder `PUMA-Backend/public`) sebagai PHP application.
5.  Setup SSL (HTTPS) menggunakan Certbot.

---
**Catatan Penting Deployment**:
Pastikan `APP_DEBUG=false` di file `.env` saat website sudah online untuk keamanan!
