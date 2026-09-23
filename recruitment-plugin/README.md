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

## Workflow Tim Setelah Git Pull

Source code route dan template disimpan di Git, sedangkan page WordPress, login admin, permalink, cache, dan option `recruitment_public_page_id` berada di database lokal masing-masing developer. Jalankan langkah berikut setelah mengambil perubahan:

```powershell
git switch master
git pull --ff-only origin master
```

Pastikan plugin aktif di WordPress dan halaman Career berisi shortcode berikut:

```text
[recruitment_careers]
```

Kemudian buka **Settings > Permalinks** dan klik **Save Changes**, lalu bersihkan cache browser/plugin. Saat login sebagai administrator, **Screen Explorer** akan muncul sebagai popup floating dan dapat dipakai untuk membuka seluruh halaman preview. Preview manual memakai parameter berikut:

```text
?daw_ui_preview=1
```

Jika form dibuka sebagai administrator/developer, mode preview membantu melewati nonce lokal yang stale akibat cache. Pengunjung publik tetap menggunakan validasi nonce normal. Setelah submit, gunakan tombol **Tracking Lamaran** pada halaman konfirmasi karena URL tersebut membawa token lamaran secara otomatis.

## Catatan Migrasi

`index.php` tetap menjadi entry point sandbox. Pada WordPress, bootstrap utama menggunakan hooks, shortcode, dan `Recruitment_Router`. Asset didaftarkan melalui `wp_enqueue_style()` dan `wp_enqueue_script()`, sedangkan akses data diarahkan melalui `database/class-database.php`.