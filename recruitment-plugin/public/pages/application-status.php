<?php
/**
 * public/pages/application-status.php
 *
 * Halaman pengecekan status lamaran.
 *
 * URL: /karir/status/
 *
 * User tidak perlu memiliki akun WordPress.
 * Gunakan token lamaran untuk lookup status.
 *
 * Alur:
 *   Application Token → Status Lookup → Application Status
 *
 * TODO: Implementasi token lookup dari database.
 */
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Status Lamaran | Recruitment Plugin</title>
    <link rel="stylesheet" href="assets/css/public.css">
</head>
<body>
<?php require __DIR__ . '/../components/header.php'; ?>
<main class="content-shell">
    <p class="eyebrow">Cek Status Lamaran</p>
    <h1>Status Lamaran Kamu.</h1>
    <p class="lede">Masukkan kode token yang dikirimkan ke email kamu untuk melihat status lamaran.</p>
    <form method="get" action="">
        <input type="hidden" name="page" value="application-status">
        <label for="token">Kode Token Lamaran</label>
        <input id="token" name="token" type="text" placeholder="Contoh: REC-20260908-001" required>
        <button class="button" type="submit">Cek Status <span aria-hidden="true">-&gt;</span></button>
    </form>
    <?php
    // TODO: Tampilkan application-status-card jika token ditemukan.
    // require __DIR__ . '/../components/application-status-card.php';
    ?>
</main>
<?php require __DIR__ . '/../components/footer.php'; ?>
<script src="assets/js/public.js"></script>
</body>
</html>
