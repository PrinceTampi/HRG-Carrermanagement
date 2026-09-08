# Recruitment Plugin

Custom WordPress plugin untuk sistem rekrutmen dengan dua area utama:

- **Public / User Area** — halaman Karir (`/karir/`) yang dapat diakses pengunjung tanpa login.
- **Admin / HRD Area** — dashboard rekrutmen (`/recruitment-admin/`) yang membutuhkan autentikasi.

Project ini merupakan **sandbox PHP** yang meniru struktur WordPress plugin asli. Dapat dijalankan tanpa instalasi WordPress atau database.

---

## Cara Menjalankan Project

Project ini menggunakan PHP built-in development server.

### 1. Pastikan PHP tersedia

```powershell
& "C:\php-8.5.10\php.exe" -v
```

### 2. Masuk ke folder project

```powershell
cd D:\DAW\recruitment-plugin
```

### 3. Jalankan server PHP

```powershell
& "C:\php-8.5.10\php.exe" -S 127.0.0.1:8000
```

Buka di browser:

```
http://127.0.0.1:8000/
```

Tekan `Ctrl+C` untuk menghentikan server.

### Login Demo

```
Username: admin
Password: admin123
```

---

## Route yang Tersedia

| URL | Area | Fungsi |
| --- | --- | --- |
| `/?page=careers` | Public | Halaman utama Karir |
| `/?page=job-detail` | Public | Detail lowongan |
| `/?page=application-form` | Public | Form pendaftaran |
| `/?page=application-status` | Public | Cek status lamaran |
| `/?page=login` | Admin | Login HRD |
| `/?page=dashboard` | Admin | Dashboard HRD |
| `/?page=vacancies` | Admin | Manajemen lowongan |
| `/?page=applicants` | Admin | Daftar pelamar |
| `/?page=applications` | Admin | Proses rekrutmen |
| `/?page=settings` | Admin | Pengaturan plugin |
| `/?page=logout` | — | Logout |

---

## Struktur Project

```text
recruitment-plugin/
│
├── recruitment-plugin.php
│
├── includes/
│   ├── class-plugin.php
│   ├── class-router.php
│   ├── class-auth.php
│   ├── class-database.php
│   └── helpers.php
│
├── public/
│   ├── pages/
│   │   ├── careers.php
│   │   ├── job-detail.php
│   │   ├── application-form.php
│   │   └── application-status.php
│   │
│   ├── components/
│   │   ├── header.php
│   │   ├── footer.php
│   │   ├── job-card.php
│   │   └── application-status-card.php
│   │
│   └── public-loader.php
│
├── admin/
│   ├── pages/
│   │   ├── login.php
│   │   ├── dashboard.php
│   │   ├── vacancies.php
│   │   ├── applicants.php
│   │   ├── applications.php
│   │   └── settings.php
│   │
│   ├── components/
│   │   ├── header.php
│   │   ├── sidebar.php
│   │   ├── stats-card.php
│   │   └── application-table.php
│   │
│   └── admin-loader.php
│
├── templates/
│   ├── emails/
│   │   ├── application-received.php
│   │   └── application-status.php
│   │
│   └── errors/
│       ├── 404.php
│       └── unauthorized.php
│
├── assets/
│   ├── css/
│   │   ├── public.css
│   │   └── admin.css
│   │
│   └── js/
│       ├── public.js
│       └── admin.js
│
└── README.md
```

---

## Fungsi File dan Folder

### Root

- `recruitment-plugin.php` — plugin header WordPress (Plugin Name, Version, Author) dan bootstrap.
- `index.php` — entry point sandbox. Memuat dependency, membaca `?page=`, memproses login/logout.
- `README.md` — dokumentasi ini.

### `includes/`

| File | Fungsi |
|---|---|
| `class-plugin.php` | Plugin bootstrap / main controller (`Recruitment_Plugin`) |
| `class-router.php` | Routing `?page=` ke template yang sesuai (`Recruitment_Router`) |
| `class-auth.php` | Login, logout, capability check (`Recruitment_Auth`) |
| `class-database.php` | Abstraksi database — stub awal (`Recruitment_Database`) |
| `helpers.php` | Helper functions + mock WordPress functions (sandbox only) |

### `public/`

Semua file yang berhubungan dengan pengunjung biasa (tanpa login).

### `admin/`

Semua file yang berhubungan dengan HRD / administrator (memerlukan login).

### `templates/`

Template yang tidak langsung menjadi halaman: email dan error pages.

### `assets/`

- `css/public.css` — stylesheet public area
- `css/admin.css` — stylesheet admin area
- `js/public.js` — JavaScript public area
- `js/admin.js` — JavaScript admin area

---

## Alur Kerja Aplikasi

```text
Browser membuka URL
    |
    v
index.php membaca ?page= dan session
    |
    +---> Recruitment_Auth (login/logout)
    |
    +---> Recruitment_Router memilih template
    |
    v
public/pages/*.php  atau  admin/pages/*.php
    |
    v
public/components/  atau  admin/components/
    |
    v
assets/css/public.css + assets/js/public.js
```

---

## Catatan Untuk WordPress Asli

Saat dipindahkan ke WordPress:

- `index.php` dan router diganti dengan WordPress hooks (`add_action`, `add_rewrite_rule`).
- `includes/helpers.php` bagian mock dihapus — gunakan fungsi WordPress asli.
- Asset didaftarkan menggunakan `wp_enqueue_style()` dan `wp_enqueue_script()`.
- Public pages bisa menggunakan shortcode atau custom page template.
- Admin pages bisa menggunakan `add_menu_page()` atau custom URL dengan `add_rewrite_rule`.
- Proteksi halaman menggunakan `current_user_can()`.
- Database menggunakan `$wpdb` dengan prepared statements.
