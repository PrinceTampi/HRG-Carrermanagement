<?php
/**
 * admin/pages/applications.php
 *
 * Manajemen application / proses rekrutmen.
 *
 * URL: /recruitment-admin/applications/
 *
 * Alur status:
 *   Submitted → Screening → Interview → Assessment → Accepted / Rejected
 *
 * TODO: Implementasi tampilan dan manajemen status menggunakan Recruitment_Database.
 */
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Applications | Recruitment Plugin</title>
    <link rel="stylesheet" href="assets/css/public.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php require recruitment_get_plugin_path( 'admin/components/header.php' ); ?>
<div class="admin-layout">
    <?php require recruitment_get_plugin_path( 'admin/components/sidebar.php' ); ?>
    <main class="content-shell">
        <p class="eyebrow">Proses Rekrutmen</p>
        <h1>Applications.</h1>
        <p class="lede">Kelola proses rekrutmen dari Submitted hingga keputusan akhir.</p>
        <?php require recruitment_get_plugin_path( 'admin/components/application-table.php' ); ?>
    </main>
</div>
<footer><span>Recruitment Plugin</span><span>Applications</span></footer>
<script src="assets/js/admin.js"></script>
</body>
</html>
