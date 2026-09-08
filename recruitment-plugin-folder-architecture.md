# Specification Struktur Folder — Custom Recruitment WordPress Plugin

## Tujuan

Buat **satu WordPress plugin** yang memiliki dua area utama:

1. **Public / User Area** — halaman Karir yang dapat diakses pengunjung tanpa login.
2. **Admin / HRD Area** — dashboard recruitment yang diakses melalui URL khusus dan membutuhkan autentikasi.

Plugin harus memiliki struktur folder yang rapi, mudah dipahami, dan mudah dikembangkan.

Fokus dokumen ini adalah **arsitektur folder dan penamaan file**. Jangan membuat struktur yang terlalu kompleks.

---

# 1. Arsitektur Utama

Gunakan satu plugin:

```text
recruitment-plugin/
```

Di dalamnya pisahkan kode berdasarkan tanggung jawab:

```text
recruitment-plugin/
│
├── recruitment-plugin.php
│
├── includes/
│
├── public/
│
├── admin/
│
├── templates/
│
├── assets/
│
└── README.md
```

Konsepnya:

```text
                    Recruitment Plugin
                           │
             ┌─────────────┴─────────────┐
             │                           │
          PUBLIC                       ADMIN
             │                           │
          /karir/              /recruitment-admin/
             │                           │
       Career Pages                 HRD Dashboard
             │                           │
       Job Listings                 Applicants
       Job Detail                   Applications
       Application                  Vacancies
       Tracking                     Settings
```

---

# 2. Struktur Folder Final

Gunakan struktur berikut sebagai target:

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

# 3. Penjelasan Setiap Folder

## `recruitment-plugin.php`

Ini adalah **main plugin file**.

Tanggung jawab:

- plugin header
- load dependencies
- menjalankan plugin
- register activation/deactivation hook
- memanggil class utama

Jangan menaruh seluruh business logic di file ini.

Contoh:

```text
recruitment-plugin.php
        │
        ├── load includes/
        │
        └── initialize Recruitment_Plugin
```

---

# 4. Folder `includes/`

Folder ini berisi **core logic yang digunakan oleh public maupun admin**.

## `class-plugin.php`

Main controller/plugin bootstrap.

Tanggung jawab:

- initialize public module
- initialize admin module
- register hooks

---

## `class-router.php`

Mengatur URL atau routing yang dibutuhkan plugin.

Contoh:

```text
/karir/
/karir/lowongan/
/karir/lowongan/{id}/
/karir/lamar/
/karir/status/
/recruitment-admin/
/recruitment-admin/dashboard/
/recruitment-admin/applicants/
```

Jangan membuat framework routing sendiri.

Gunakan mekanisme WordPress yang sesuai jika memungkinkan.

---

## `class-auth.php`

Menangani authentication/authorization.

Gunakan sistem authentication WordPress.

Jangan membuat sistem password sendiri.

Tanggung jawab:

- login
- logout
- pengecekan user login
- pengecekan capability/role
- proteksi halaman admin

Contoh konsep:

```text
User
 ↓
WordPress Authentication
 ↓
class-auth.php
 ↓
Admin Access
```

---

## `class-database.php`

Tempat abstraction untuk operasi database plugin.

Untuk tahap awal jangan membuat database table jika belum diperlukan.

Jika nanti diperlukan:

```text
class-database.php
        ↓
wpdb
        ↓
Custom Recruitment Tables
```

Jangan menaruh query database tersebar di semua file.

---

## `helpers.php`

Berisi helper function sederhana yang dipakai lintas module.

Contoh:

```text
get_plugin_url()
get_plugin_path()
get_application_url()
get_admin_url()
```

Jangan menjadikan file ini tempat menaruh semua logic.

---

# 5. Folder `public/`

Berisi semua functionality yang berhubungan dengan **pengunjung/user biasa**.

User tidak harus login untuk mengakses halaman Karir.

Struktur:

```text
public/
│
├── pages/
├── components/
└── public-loader.php
```

---

# 6. `public/pages/`

## `careers.php`

Halaman utama Karir.

Contoh:

```text
/karir/
```

Berisi:

- Hero
- CTA
- daftar lowongan
- informasi perusahaan
- recruitment information

---

## `job-detail.php`

Detail lowongan.

Contoh:

```text
/karir/lowongan/frontend-developer/
```

Berisi:

- job title
- department
- location
- description
- requirements
- responsibilities
- CTA Apply

---

## `application-form.php`

Form pendaftaran pelamar.

Contoh:

```text
/karir/lamar/
```

Berisi:

- data pribadi
- kontak
- pendidikan
- pengalaman
- dokumen
- posisi yang dilamar

Gunakan WordPress security practices untuk form.

---

## `application-status.php`

Halaman untuk mengecek status lamaran.

Contoh:

```text
/karir/status/
```

Jika sistem menggunakan token:

```text
Application Token
       ↓
Status Lookup
       ↓
Application Status
```

User tidak perlu memiliki akun WordPress untuk mengecek status jika requirement menggunakan token.

---

# 7. `public/components/`

Berisi komponen UI yang digunakan oleh halaman public.

## `header.php`

Header halaman Karir.

Contoh:

```text
Logo
Home
Career
About
```

---

## `footer.php`

Footer halaman Karir.

---

## `job-card.php`

Komponen card untuk setiap lowongan.

Contoh:

```text
Frontend Developer
Technology
Full Time

[View Job]
```

---

## `application-status-card.php`

Komponen untuk menampilkan status lamaran.

Contoh:

```text
Application Status

Frontend Developer

Status:
Under Review
```

---

# 8. `public/public-loader.php`

File untuk menginisialisasi functionality public.

Contoh tanggung jawab:

```text
Public Loader
    │
    ├── register shortcode
    ├── register public hooks
    ├── enqueue public CSS
    └── enqueue public JS
```

---

# 9. Folder `admin/`

Folder ini berisi interface khusus HRD/admin.

Walaupun UI admin berada pada URL custom, misalnya:

```text
/recruitment-admin/
```

authentication dan authorization tetap harus menggunakan WordPress.

Struktur:

```text
admin/
│
├── pages/
├── components/
└── admin-loader.php
```

---

# 10. `admin/pages/`

## `login.php`

Halaman login HRD/admin jika interface custom membutuhkan halaman login sendiri.

Contoh:

```text
/recruitment-admin/
```

Login tetap menggunakan authentication WordPress.

---

## `dashboard.php`

Dashboard utama HRD.

Contoh:

```text
/recruitment-admin/dashboard/
```

Berisi:

```text
Total Vacancies
Total Applicants
New Applications
Applications in Review
```

---

## `vacancies.php`

Manajemen lowongan.

Contoh:

```text
/recruitment-admin/vacancies/
```

Fungsi yang dapat dikembangkan:

```text
Create Vacancy
Read Vacancy
Update Vacancy
Delete/Archive Vacancy
```

---

## `applicants.php`

Daftar pelamar.

Contoh:

```text
/recruitment-admin/applicants/
```

Menampilkan:

- nama
- email
- posisi
- tanggal daftar
- status

---

## `applications.php`

Manajemen application.

Contoh:

```text
/recruitment-admin/applications/
```

Fokus pada proses recruitment:

```text
Submitted
    ↓
Screening
    ↓
Interview
    ↓
Assessment
    ↓
Accepted / Rejected
```

---

## `settings.php`

Konfigurasi plugin.

Contoh:

```text
/recruitment-admin/settings/
```

Dapat berisi:

- recruitment settings
- email settings
- notification settings
- general settings

---

# 11. `admin/components/`

Berisi komponen UI khusus dashboard admin.

## `header.php`

Header dashboard HRD.

---

## `sidebar.php`

Navigation:

```text
Dashboard
Vacancies
Applicants
Applications
Settings
Logout
```

---

## `stats-card.php`

Card statistik dashboard.

---

## `application-table.php`

Reusable table untuk daftar application.

---

# 12. `admin/admin-loader.php`

File untuk menginisialisasi seluruh admin functionality.

Tanggung jawab:

```text
Admin Loader
    │
    ├── register routes
    ├── register admin hooks
    ├── check authentication
    ├── check capability
    ├── enqueue admin CSS
    └── enqueue admin JS
```

---

# 13. Folder `templates/`

Folder ini digunakan untuk template yang **tidak secara langsung menjadi halaman public atau admin**.

## `templates/emails/`

Template email.

Contoh:

```text
application-received.php
application-status.php
```

---

## `templates/errors/`

Template error.

Contoh:

```text
404.php
unauthorized.php
```

---

# 14. Folder `assets/`

Pisahkan asset public dan admin.

```text
assets/
│
├── css/
│   ├── public.css
│   └── admin.css
│
└── js/
    ├── public.js
    └── admin.js
```

Jangan menggunakan satu CSS besar untuk seluruh sistem jika tidak diperlukan.

---

# 15. URL Architecture

Gunakan URL yang mudah dimengerti.

## Public

```text
/karir/
```

Career homepage.

```text
/karir/lowongan/{job}/
```

Job detail.

```text
/karir/lamar/{job}/
```

Application form.

```text
/karir/status/
```

Application status.

---

## Admin

```text
/recruitment-admin/
```

Admin entry/login.

```text
/recruitment-admin/dashboard/
```

Dashboard.

```text
/recruitment-admin/vacancies/
```

Vacancies.

```text
/recruitment-admin/applicants/
```

Applicants.

```text
/recruitment-admin/applications/
```

Applications.

```text
/recruitment-admin/settings/
```

Settings.

URL dapat disesuaikan dengan implementasi WordPress, tetapi gunakan pola yang konsisten.

---

# 16. Naming Convention

Gunakan naming convention berikut.

## Folder

Gunakan:

```text
lowercase
```

dan pisahkan kata dengan:

```text
-
```

Contoh:

```text
public/
admin/
assets/
templates/
```

Jika nama folder terdiri dari beberapa kata:

```text
application-status/
```

---

## PHP Class

Gunakan:

```text
PascalCase
```

Contoh:

```php
class Recruitment_Plugin
class Recruitment_Router
class Recruitment_Auth
class Recruitment_Database
```

---

## PHP File Class

Gunakan:

```text
class-{name}.php
```

Contoh:

```text
class-plugin.php
class-router.php
class-auth.php
class-database.php
```

---

## Page Files

Gunakan nama berdasarkan fungsi halaman:

```text
careers.php
job-detail.php
application-form.php
application-status.php
```

Hindari:

```text
page1.php
page2.php
test.php
new.php
final.php
```

---

# 17. Separation of Responsibility

Ikuti aturan sederhana:

```text
main plugin
    ↓
initialization

includes
    ↓
core logic

public
    ↓
user-facing functionality

admin
    ↓
HRD/admin functionality

templates
    ↓
shared/non-page templates

assets
    ↓
CSS + JavaScript
```

Jangan mencampurkan:

```text
Database Query
UI HTML
Authentication
Routing
CSS
```

dalam satu file.

---

# 18. Security Rules

Plugin harus menggunakan WordPress API.

Gunakan:

```php
is_user_logged_in()
wp_get_current_user()
current_user_can()
wp_nonce_field()
wp_verify_nonce()
sanitize_text_field()
sanitize_email()
esc_html()
esc_attr()
esc_url()
```

Untuk database gunakan:

```php
$wpdb
```

dan prepared statements.

Jangan:

```php
$_POST['email']
```

langsung dimasukkan ke database.

Jangan membuat:

```text
custom password system
custom session authentication
plaintext password
hard-coded admin username
```

---

# 19. Admin Authentication

Walaupun admin menggunakan URL custom:

```text
/recruitment-admin/
```

tetap gunakan WordPress authentication.

Konsep:

```text
/recruitment-admin/
        ↓
Authentication
        ↓
WordPress User
        ↓
Capability Check
        ↓
Recruitment Dashboard
```

Jangan menganggap bahwa URL custom otomatis aman.

Setiap admin page tetap harus melakukan authorization check.

---

# 20. Public vs Admin

Pastikan kode public dan admin tidak saling tercampur.

```text
PUBLIC
public/
│
├── careers
├── job detail
├── application
└── application status


ADMIN
admin/
│
├── dashboard
├── vacancies
├── applicants
├── applications
└── settings
```

Core functionality yang digunakan bersama diletakkan di:

```text
includes/
```

---

# 21. Prinsip Arsitektur

Prioritaskan:

1. mudah dibaca
2. mudah dicari
3. mudah dikembangkan
4. mudah di-debug
5. tidak over-engineered
6. mengikuti pola WordPress
7. separation of concerns

Jangan membuat folder seperti:

```text
src/
lib/
core/
services/
repositories/
factories/
providers/
interfaces/
traits/
```

kecuali memang dibutuhkan.

Untuk plugin ini, struktur sederhana lebih diutamakan.

---

# 22. Instruksi untuk GitHub Copilot / Coding Agent

Gunakan dokumen ini sebagai **arsitektur folder**, bukan instruksi untuk membuat seluruh fitur recruitment sekaligus.

Tugas utama:

1. Buat folder plugin:

```text
recruitment-plugin/
```

2. Buat struktur folder dan file sesuai dokumen.
3. Buat placeholder PHP yang valid.
4. Buat main plugin file.
5. Buat class dasar.
6. Pastikan semua file dapat di-load tanpa fatal error.
7. Gunakan nama file dan folder yang konsisten.
8. Jangan membuat fitur database kompleks.
9. Jangan membuat fitur recruitment di luar scope.
10. Jangan mengubah WordPress core.
11. Jangan menambahkan framework.
12. Jangan menambahkan dependency eksternal tanpa alasan.

Pada tahap pertama, **prioritaskan struktur folder yang rapi dan skeleton plugin yang dapat diaktifkan oleh WordPress**.

---

# 23. Expected Result

Hasil akhirnya:

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
│   ├── components/
│   └── public-loader.php
│
├── admin/
│   ├── pages/
│   ├── components/
│   └── admin-loader.php
│
├── templates/
│   ├── emails/
│   └── errors/
│
├── assets/
│   ├── css/
│   └── js/
│
└── README.md
```

Konsep akhirnya:

```text
                    ONE PLUGIN
                        │
        ┌───────────────┴────────────────┐
        │                                │
      PUBLIC                           ADMIN
        │                                │
     /karir/                    /recruitment-admin/
        │                                │
        ├── Careers                      ├── Login
        ├── Job Detail                   ├── Dashboard
        ├── Application                  ├── Vacancies
        └── Status                       ├── Applicants
                                         ├── Applications
                                         └── Settings

                        │
                        ▼
                    includes/
                        │
             Shared Core Logic
```

**Catatan:** file OKR yang tersedia saat ini tidak memiliki konten spreadsheet yang dapat dibaca oleh sistem file, sehingga dokumen ini sengaja tidak mengklaim bahwa setiap folder di atas merupakan pemetaan satu-per-satu dari OKR. Struktur ini adalah arsitektur plugin berdasarkan requirement yang sudah kamu jelaskan: satu plugin, halaman Karir untuk user, dan dashboard HRD melalui URL khusus. fileciteturn0file0L1-L9
