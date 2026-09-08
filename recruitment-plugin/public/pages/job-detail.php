<?php
/**
 * public/pages/job-detail.php
 *
 * Halaman detail lowongan.
 *
 * URL: /karir/lowongan/{job}/
 *
 * Berisi:
 * - job title
 * - department
 * - location
 * - description
 * - requirements
 * - responsibilities
 * - CTA Apply
 *
 * TODO: Ambil data job dari Recruitment_Database berdasarkan slug/ID.
 */
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Lowongan | Recruitment Plugin</title>
    <link rel="stylesheet" href="assets/css/public.css">
</head>
<body>
<?php require __DIR__ . '/../components/header.php'; ?>
<main class="content-shell">
    <p class="eyebrow">Detail Lowongan</p>
    <h1>Frontend Developer</h1>
    <p class="lede">Technology · Full Time · Jakarta</p>
    <p>TODO: Tampilkan deskripsi, requirements, dan responsibilities dari database.</p>
    <a class="button" href="?page=application-form">Lamar Posisi Ini <span aria-hidden="true">-&gt;</span></a>
</main>
<?php require __DIR__ . '/../components/footer.php'; ?>
<script src="assets/js/public.js"></script>
</body>
</html>
