# MASTER TASK

## Convert Figma Make Website Implementation into a Native WordPress Plugin

Saya memiliki sebuah project website yang sebelumnya dibuat/export menggunakan Figma Make.

Project tersebut saat ini menggunakan bahasa, framework, dan/atau struktur frontend yang **tidak akan digunakan pada implementasi final**.

Target implementasi final adalah:

**Custom WordPress Plugin menggunakan PHP Native + HTML + CSS + Vanilla JavaScript bila diperlukan.**

Plugin ini akan digunakan sebagai bagian dari website WordPress yang sudah ada.

Tugas utama kamu adalah:

> Memahami implementasi existing terlebih dahulu, kemudian mengonversinya menjadi Custom WordPress Plugin dengan PHP Native tanpa mengubah desain, layout, visual identity, asset, responsive behavior, dan functionality yang sudah ada.

---

# 1. TARGET TECHNOLOGY

Gunakan teknologi berikut:

* WordPress Plugin API
* PHP Native
* HTML5
* CSS3
* Vanilla JavaScript
* WordPress hooks
* WordPress functions/API jika diperlukan

Jangan menggunakan:

* React
* Vue
* Angular
* Next.js
* Laravel
* Tailwind CSS
* Bootstrap
* Material UI
* PHP framework
* JavaScript framework
* SPA framework
* build system yang tidak diperlukan

Jika existing project menggunakan React/TypeScript/Tailwind atau framework lainnya, jangan mempertahankan framework tersebut.

Terjemahkan hasil implementasinya ke:

**WordPress + PHP + HTML + CSS + Vanilla JS**

---

# 2. PRIMARY OBJECTIVE

Tujuan utama bukan melakukan redesign.

Tujuan utama adalah:

**Existing Figma Make implementation**

↓

**Analyze**

↓

**Map**

↓

**Convert**

↓

**Custom WordPress Plugin**

dengan mempertahankan:

* visual design
* layout
* spacing
* typography
* color
* component appearance
* images
* icons
* responsive behavior
* interaction
* user flow
* functionality

Hasil akhir harus terlihat semirip mungkin dengan versi original.

---

# 3. IMPORTANT: ANALYZE BEFORE CODING

Jangan langsung mengubah code.

Pada tahap pertama, lakukan audit terhadap seluruh project.

Identifikasi:

## Existing Technology

Cari:

* framework
* programming language
* package manager
* dependencies
* component system
* routing
* styling system
* state management
* data handling
* API usage
* JavaScript interaction
* asset management

## Existing Pages

Buat daftar seluruh halaman yang ditemukan.

Contoh:

* Home
* Career
* Job Detail
* Application Form
* Application Status
* Admin Dashboard
* Login
* Interview
* dan lain-lain jika tersedia.

Jangan membuat halaman yang tidak ada pada source.

## Existing Components

Identifikasi:

* Header
* Navbar
* Sidebar
* Footer
* Button
* Card
* Form
* Input
* Select
* Table
* Modal
* Badge
* Alert
* Tabs
* Dropdown
* Pagination
* Empty state
* Loading state
* Confirmation dialog
* dan reusable component lainnya.

## Existing Assets

Identifikasi:

* logo
* images
* SVG
* icons
* fonts
* illustrations
* background
* favicon
* asset lainnya.

Jangan mengganti asset asli dengan placeholder jika asset asli tersedia.

---

# 4. VISUAL SPECIFICATION

Buat dokumentasi internal mengenai visual specification dari existing project.

Analisis:

* page width
* container
* grid
* flex
* spacing
* padding
* margin
* font family
* font size
* font weight
* line height
* color
* border
* border radius
* shadow
* icon
* image
* button
* form
* card
* table
* modal
* navigation

Perhatikan juga:

* desktop
* tablet
* mobile
* hover
* active
* focus
* disabled
* loading

Tujuannya agar desain dapat direplikasi menggunakan CSS Native.

---

# 5. WORDPRESS ARCHITECTURE

Setelah memahami existing project, tentukan arsitektur plugin WordPress.

Gunakan struktur yang modular.

Contoh struktur dasar:

```text
plugin-name/
│
├── plugin-name.php
│
├── uninstall.php
│
├── readme.txt
│
├── includes/
│   ├── class-plugin.php
│   ├── class-loader.php
│   ├── class-assets.php
│   ├── class-public.php
│   └── class-admin.php
│
├── admin/
│   ├── class-admin.php
│   ├── views/
│   ├── css/
│   └── js/
│
├── public/
│   ├── class-public.php
│   ├── views/
│   ├── css/
│   ├── js/
│   └── images/
│
├── templates/
│   ├── header.php
│   ├── footer.php
│   └── components/
│
├── assets/
│   ├── css/
│   ├── js/
│   ├── images/
│   └── icons/
│
├── ajax/
│
├── database/
│
└── languages/
```

Namun struktur di atas hanya contoh.

**Jangan membuat file/folder yang tidak diperlukan.**

Sesuaikan struktur dengan hasil audit project.

---

# 6. WORDPRESS PLUGIN RESPONSIBILITY

Pisahkan responsibility dengan jelas.

## Root Plugin File

File utama plugin bertanggung jawab untuk:

* plugin metadata
* initialization
* loading dependencies
* plugin bootstrap

Jangan menaruh seluruh logic plugin di file utama.

## Includes

Digunakan untuk:

* core classes
* hooks
* initialization
* shared logic
* helper
* asset registration

## Admin

Digunakan khusus untuk:

* WordPress Admin
* dashboard
* management interface
* admin forms
* admin tables
* admin settings
* admin AJAX

## Public

Digunakan untuk:

* frontend website
* public pages
* applicant interface
* public forms
* public components
* frontend JavaScript
* frontend CSS

## Templates

Digunakan untuk:

* reusable PHP templates
* page templates
* component templates
* shared UI

## Assets

Pisahkan:

```text
assets/
├── css/
├── js/
├── images/
└── icons/
```

Jangan mencampurkan admin assets dengan public assets jika tidak diperlukan.

---

# 7. WORDPRESS INTEGRATION

Jangan membuat sistem routing PHP sendiri jika WordPress dapat menangani kebutuhan tersebut.

Gunakan WordPress mechanism yang sesuai seperti:

* hooks
* actions
* filters
* shortcodes
* custom post types jika diperlukan
* admin menus
* AJAX
* REST API jika memang diperlukan
* WordPress database API

Jika sebuah halaman frontend harus ditampilkan melalui plugin, tentukan mekanisme WordPress yang paling sesuai.

Contoh kemungkinan:

```text
Shortcode
Template
Rewrite Rule
Page Integration
```

Pilih berdasarkan kebutuhan actual project.

Jangan membuat keputusan arsitektur tanpa terlebih dahulu memahami kebutuhan halaman.

---

# 8. SECURITY REQUIREMENTS

Karena plugin akan berjalan di WordPress, seluruh implementasi harus mengikuti praktik keamanan WordPress.

Gunakan jika relevan:

* `current_user_can()`
* `wp_nonce_field()`
* `check_admin_referer()`
* `check_ajax_referer()`
* `sanitize_text_field()`
* `sanitize_email()`
* `sanitize_textarea_field()`
* `absint()`
* `esc_html()`
* `esc_attr()`
* `esc_url()`
* `wp_kses()`

Jangan langsung memasukkan user input ke HTML atau database tanpa sanitization/validation yang sesuai.

Untuk database gunakan `$wpdb` dan prepared statements bila diperlukan.

Jangan menyimpan credential secara hardcoded.

---

# 9. ADMIN VS PUBLIC

Bedakan dengan jelas antara:

### Public

Halaman yang dapat diakses visitor/applicant.

Contoh:

```text
Career
Job Detail
Application Form
Application Status
Interview Form
```

### Admin

Halaman yang hanya dapat digunakan administrator/HR.

Contoh:

```text
Dashboard
Vacancy Management
Applicant Management
Interview Management
Evaluation
Reports
Settings
```

Jangan mencampurkan public logic dengan admin logic jika tidak diperlukan.

---

# 10. CONVERSION MAPPING

Setelah audit, buat mapping dari source project ke WordPress.

Contoh:

```text
Existing React Component
        ↓
PHP Template / Component

Existing CSS / Tailwind
        ↓
Native CSS

Existing JavaScript
        ↓
Vanilla JavaScript

Existing Route
        ↓
WordPress Page / Shortcode / Rewrite

Existing State
        ↓
PHP / WordPress / Vanilla JS sesuai kebutuhan

Existing API
        ↓
WordPress HTTP API / AJAX / REST API

Existing Asset
        ↓
WordPress Plugin Asset
```

Buat mapping aktual berdasarkan project.

Jangan hanya menggunakan contoh di atas.

---

# 11. COMPONENT CONVERSION

Jika existing project memiliki reusable components, jangan menggabungkannya menjadi satu file besar.

Contoh:

Existing:

```text
components/
├── Header
├── Navbar
├── Button
├── Card
├── Modal
└── Form
```

Dapat dikonversi menjadi:

```text
templates/
└── components/
    ├── header.php
    ├── navbar.php
    ├── button.php
    ├── card.php
    ├── modal.php
    └── form.php
```

Gunakan PHP variables/arrays untuk component yang membutuhkan data dinamis.

Contoh konsep:

```php
<?php
$title = $args['title'] ?? '';
$url = $args['url'] ?? '#';
?>
```

Tetapi jangan membuat abstraction yang terlalu kompleks.

Prioritaskan readability.

---

# 12. CSS CONVERSION

Jika existing project menggunakan Tailwind atau utility CSS, jangan membawa Tailwind ke plugin.

Terjemahkan visual result menjadi CSS Native.

Contoh:

Existing:

```text
flex
items-center
justify-between
gap-4
rounded-lg
shadow-md
```

Konversikan menjadi CSS yang menghasilkan visual yang sama.

Contoh:

```css
.component {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    border-radius: 0.5rem;
    box-shadow: ...;
}
```

Yang dipertahankan adalah **visual output**, bukan nama class dari framework lama.

---

# 13. CSS NAMESPACE

Karena plugin akan berjalan di website WordPress yang mungkin sudah memiliki CSS dari theme atau plugin lain, hindari generic global selectors jika memungkinkan.

Jangan sembarangan menggunakan:

```css
button {}
input {}
.card {}
.container {}
.title {}
```

Gunakan namespace/plugin prefix.

Contoh:

```css
.career-plugin {}
.career-plugin__header {}
.career-plugin__card {}
.career-plugin__button {}
```

atau prefix yang sesuai dengan nama plugin.

Tujuannya mencegah CSS plugin bertabrakan dengan theme WordPress.

---

# 14. JAVASCRIPT

Gunakan Vanilla JavaScript.

JavaScript hanya digunakan jika diperlukan untuk:

* modal
* dropdown
* tabs
* toggle
* validation
* dynamic interaction
* AJAX
* UI state
* form interaction

Jangan membawa React state atau framework state management ke plugin.

Pastikan JavaScript tidak bergantung pada framework lama.

---

# 15. WORDPRESS ASSET LOADING

Jangan hardcode path asset seperti:

```html
/assets/css/style.css
```

Gunakan WordPress enqueue mechanism.

Contoh konsep:

```php
wp_enqueue_style(
    'plugin-public',
    plugin_dir_url(__FILE__) . 'assets/css/public.css',
    array(),
    PLUGIN_VERSION
);
```

Sesuaikan implementasi dengan lokasi file sebenarnya.

CSS dan JS harus hanya dimuat pada halaman yang membutuhkannya jika memungkinkan.

---

# 16. DATABASE

Jika existing project membutuhkan database:

Jangan langsung membuat database schema baru.

Pertama identifikasi:

* data apa yang disimpan
* relationship
* CRUD
* validation
* status
* user role
* existing WordPress data
* kebutuhan custom table

Kemudian tentukan apakah data lebih cocok menggunakan:

* WordPress database
* Custom Post Type
* Post Meta
* User Meta
* Options API
* Custom Database Table

Pilih berdasarkan kebutuhan sistem.

Jangan membuat custom table jika WordPress native storage sudah cukup.

Jika custom table memang diperlukan, buat struktur database yang jelas dan gunakan `$wpdb`.

---

# 17. NO UNNECESSARY REBUILD

Jangan melakukan perubahan arsitektur yang tidak diperlukan.

Tujuan conversion adalah:

**preserve existing design + functionality while adapting implementation to WordPress PHP Native.**

Bukan:

**rebuild the website based on your own preferred design or architecture.**

Jika ada dua pendekatan yang memungkinkan, pilih pendekatan yang:

1. paling dekat dengan behavior existing
2. paling aman untuk WordPress
3. paling mudah dipelihara
4. paling sederhana

---

# 18. DO NOT MODIFY DESIGN

Ini merupakan requirement utama.

Jangan melakukan:

* redesign
* perubahan warna
* perubahan font
* perubahan layout
* perubahan spacing
* perubahan ukuran
* perubahan hierarchy
* perubahan icon
* perubahan image
* perubahan responsive behavior

kecuali memang diperlukan karena perbedaan environment WordPress.

Jika ada masalah teknis yang berpotensi mengubah desain, jelaskan masalahnya sebelum mengambil keputusan besar.

---

# 19. PRESERVE RESPONSIVENESS

Hasil akhir harus mempertahankan responsive behavior.

Validasi:

```text
Desktop
Tablet
Mobile
```

Perhatikan terutama:

* navbar
* sidebar
* cards
* forms
* tables
* buttons
* modal
* images
* typography
* spacing
* overflow

Jangan hanya memastikan halaman terlihat benar pada desktop.

---

# 20. WORDPRESS CODING STANDARDS

Gunakan naming convention yang konsisten.

Gunakan prefix plugin untuk menghindari collision.

Contoh:

```php
myplugin_
MyPlugin_
myplugin-
myplugin__
```

Pilih satu convention dan gunakan secara konsisten.

Jangan membuat global functions dengan nama generik.

Jika menggunakan class, gunakan struktur class yang jelas.

---

# 21. PHASED DEVELOPMENT

Kerjakan conversion secara bertahap.

### PHASE 1 — AUDIT

Jangan mengubah code.

Output:

```text
1. Existing stack
2. Existing structure
3. Pages
4. Components
5. Assets
6. Styling
7. JavaScript
8. Functionality
9. Routing
10. Dependencies
```

### PHASE 2 — ARCHITECTURE

Tentukan:

```text
1. Plugin structure
2. Page mapping
3. Component mapping
4. Asset mapping
5. WordPress integration strategy
6. Admin/Public separation
7. Database strategy
```

### PHASE 3 — FOUNDATION

Buat:

```text
plugin-name.php
includes/
admin/
public/
templates/
assets/
```

sesuai kebutuhan actual project.

Pastikan plugin dapat diaktifkan WordPress tanpa fatal error.

### PHASE 4 — UI CONVERSION

Konversikan:

```text
Layout
↓
Components
↓
Pages
↓
CSS
↓
Responsive
```

### PHASE 5 — FUNCTIONALITY

Konversikan:

```text
Interaction
Forms
Validation
AJAX
Navigation
Dynamic data
```

### PHASE 6 — WORDPRESS INTEGRATION

Implementasikan:

```text
Hooks
Shortcodes
Admin pages
Permissions
Nonce
Sanitization
Database
WordPress APIs
```

sesuai kebutuhan.

### PHASE 7 — VALIDATION

Periksa:

```text
Visual
Responsive
Functionality
Security
WordPress compatibility
Code quality
Asset loading
CSS conflicts
JavaScript errors
PHP errors
```

---

# 22. REQUIRED FIRST RESPONSE

Saat task ini pertama kali dijalankan, **JANGAN langsung melakukan conversion.**

Jawaban pertama harus berupa audit.

Gunakan format:

```text
PROJECT ANALYSIS

1. Existing Technology
2. Existing Folder Structure
3. Pages
4. Components
5. Assets
6. Styling System
7. JavaScript / Interactions
8. Routing
9. Data Handling
10. Dependencies

CONVERSION PLAN

1. Old → New Mapping
2. Proposed WordPress Plugin Structure
3. Public Architecture
4. Admin Architecture
5. Asset Strategy
6. Database Strategy
7. Security Considerations
8. Potential Conversion Risks

RECOMMENDED IMPLEMENTATION ORDER

1.
2.
3.
4.
...
```

**Jangan membuat perubahan file pada tahap audit.**

Setelah audit dan conversion plan selesai, baru mulai implementasi.

---

# 23. FINAL SUCCESS CRITERIA

Conversion dianggap berhasil jika:

### Architecture

* valid WordPress plugin
* modular
* maintainable
* clear separation of concerns
* public/admin separated
* no unnecessary framework

### Technology

* PHP Native
* HTML
* CSS
* Vanilla JS
* WordPress APIs

### Visual

* desain sama/se-mirip mungkin
* spacing preserved
* typography preserved
* colors preserved
* components preserved
* assets preserved
* responsive behavior preserved

### Functionality

* existing interactions preserved
* existing user flow preserved
* forms preserved
* navigation preserved

### Security

* input sanitized
* output escaped
* nonce implemented where necessary
* capability checks implemented
* database queries secured

### Compatibility

* plugin can be activated
* no PHP fatal error
* no unnecessary dependency on original framework
* CSS does not unnecessarily conflict with WordPress theme
* JavaScript works without original frontend framework

---

# FINAL PRINCIPLE

Selalu ingat:

> DO NOT RECREATE THE DESIGN FROM YOUR MEMORY.

> DO NOT REDESIGN THE WEBSITE.

> DO NOT SIMPLIFY THE UI JUST BECAUSE PHP IS DIFFERENT.

> FIRST UNDERSTAND THE EXISTING IMPLEMENTATION.

> THEN REPRODUCE ITS VISUAL AND FUNCTIONAL RESULT USING WORDPRESS + PHP NATIVE + CSS + VANILLA JS.

Source project adalah reference utama untuk desain dan functionality.

WordPress Plugin architecture adalah target implementation.

The final result should preserve the original website experience while replacing the underlying technology stack.
