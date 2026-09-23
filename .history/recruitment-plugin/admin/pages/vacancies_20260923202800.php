<?php
/**
 * admin/pages/vacancies.php
 *
 * Manajemen lowongan kerja.
 *
 * URL: /recruitment-admin/vacancies/
 *
 * Fungsi yang dapat dikembangkan:
 * - Create Vacancy
 * - Read Vacancy
 * - Update Vacancy
 * - Delete/Archive Vacancy
 *
 * TODO: Implementasi CRUD menggunakan Recruitment_Database.
 */
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vacancies | Recruitment Plugin</title>
    <link rel="stylesheet" href="assets/css/public.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php require recruitment_get_plugin_path( 'admin/components/header.php' ); ?>
<div class="admin-layout">
    <?php require recruitment_get_plugin_path( 'admin/components/sidebar.php' ); ?>
    <main class="content-shell">
        <p class="eyebrow">Manajemen Lowongan</p>
        <h1>Vacancies.</h1>
        <p class="lede">Kelola semua lowongan kerja yang tersedia.</p>
        <p>TODO: Tampilkan daftar vacancies dan form Create/Edit menggunakan Recruitment_Database.</p>
    </main>
</div>
<footer><span>Recruitment Plugin</span><span>Vacancies</span></footer>
<script src="assets/js/admin.js"></script>
</body>
</html>
