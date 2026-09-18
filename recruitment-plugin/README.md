# Recruitment Plugin

Custom WordPress plugin untuk sistem rekrutmen dengan dua area utama:

- **Public / User Area** — halaman Karir (`/karir/`) tanpa login.
- **Admin / HRD Area** — dashboard rekrutmen (`/recruitment-admin/`) dengan autentikasi.

## Struktur Modular

```text
recruitment-plugin/
├── assets/                  # CSS, JavaScript, images, icons
├── pages/                   # Entry point layout per halaman
│   ├── public/
│   └── admin/
├── components/              # UI reusable
│   ├── public/
│   └── admin/
├── routes/                  # Registry dan renderer route
├── database/                # Gateway koneksi dan query Oracle
├── includes/                # Helper, auth, dan bootstrap
├── public/                  # Template lama selama migrasi bertahap
├── admin/                   # Template lama selama migrasi bertahap
└── templates/               # Email dan halaman error
```

Alur request: `recruitment-plugin.php` -> `includes/class-plugin.php` -> `routes/class-router.php` -> `pages/*` -> `components/*`.

Konfigurasi Oracle dibaca otomatis dari `RECRUITMENT_ORACLE_DSN`, `RECRUITMENT_ORACLE_USER`, dan `RECRUITMENT_ORACLE_PASSWORD`, atau option WordPress `recruitment_oracle_config`. Pastikan ekstensi PHP `pdo_oci` tersedia. Page tidak boleh membuat koneksi atau menulis query langsung.

Folder `public/` dan `admin/` dipertahankan sebagai bridge kompatibilitas agar migrasi template dapat dilakukan per halaman tanpa mengubah output.

## Catatan Migrasi

`index.php` tetap menjadi entry point sandbox. Pada WordPress, bootstrap utama menggunakan hooks, shortcode, dan `Recruitment_Router`. Asset didaftarkan melalui `wp_enqueue_style()` dan `wp_enqueue_script()`, sedangkan akses data diarahkan melalui `database/class-database.php`.