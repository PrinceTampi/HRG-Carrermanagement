<?php
/**
 * admin/pages/settings.php
 *
 * Konfigurasi plugin.
 *
 * URL: /recruitment-admin/settings/
 *
 * Dapat berisi:
 * - recruitment settings
 * - email settings
 * - notification settings
 * - general settings
 *
 * TODO: Implementasi settings form dengan WordPress Options API.
 */
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Settings | Recruitment Plugin</title>
    <link rel="stylesheet" href="assets/css/public.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php require __DIR__ . '/../components/header.php'; ?>
<div class="admin-layout">
    <?php require __DIR__ . '/../components/sidebar.php'; ?>
    <main class="content-shell">
        <p class="eyebrow">Konfigurasi Plugin</p>
        <h1>Settings.</h1>
        <p class="lede">Pengaturan plugin recruitment.</p>
        <p>TODO: Implementasi settings menggunakan WordPress Options API (get_option / update_option).</p>
    </main>
</div>
<footer><span>Recruitment Plugin</span><span>Settings</span></footer>
<script src="assets/js/admin.js"></script>
</body>
</html>
