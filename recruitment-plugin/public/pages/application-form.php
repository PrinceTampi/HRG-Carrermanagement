<?php
/**
 * public/pages/application-form.php
 *
 * Halaman form pendaftaran pelamar.
 *
 * URL: /karir/lamar/{job}/
 *
 * Berisi:
 * - data pribadi
 * - kontak
 * - pendidikan
 * - pengalaman
 * - dokumen
 * - posisi yang dilamar
 *
 * Gunakan WordPress security practices:
 * - wp_nonce_field() / wp_verify_nonce()
 * - sanitize_text_field() / sanitize_email()
 * - esc_html() / esc_attr()
 *
 * TODO: Implementasi form submission dan penyimpanan ke database.
 */
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form Lamaran | Recruitment Plugin</title>
    <link rel="stylesheet" href="assets/css/public.css">
</head>
<body>
<?php require __DIR__ . '/../components/header.php'; ?>
<main class="content-shell">
    <p class="eyebrow">Form Pendaftaran</p>
    <h1>Lamar Posisi.</h1>
    <p class="lede">Isi form berikut untuk mengirim lamaran kamu.</p>
    <form method="post" action="?page=application-form" enctype="multipart/form-data">
        <!-- TODO: wp_nonce_field( 'recruitment_apply' ) -->
        <label for="full_name">Nama Lengkap</label>
        <input id="full_name" name="full_name" type="text" required>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required>
        <label for="phone">Nomor Telepon</label>
        <input id="phone" name="phone" type="tel">
        <label for="position">Posisi yang Dilamar</label>
        <input id="position" name="position" type="text" required>
        <button class="button" type="submit">Kirim Lamaran <span aria-hidden="true">-&gt;</span></button>
    </form>
</main>
<?php require __DIR__ . '/../components/footer.php'; ?>
<script src="assets/js/public.js"></script>
</body>
</html>
