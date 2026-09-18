<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Applicants | Recruitment Plugin</title>
    <link rel="stylesheet" href="assets/css/public.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php require recruitment_get_plugin_path( 'components/admin/header.php' ); ?>
<div class="admin-layout">
    <?php require recruitment_get_plugin_path( 'components/admin/sidebar.php' ); ?>
    <main class="content-shell">
        <p class="eyebrow">Plugin overview</p>
        <h1>Applicants.</h1>
        <p class="lede">Daftar semua pelamar yang telah mendaftar.</p>
        <section class="status-table">
            <?php
            // Tampilkan plugin info sementara.
            // TODO: Ganti dengan daftar applicants dari Recruitment_Database::get_applicants()
            if ( isset( $plugin_info ) ) :
                foreach ( $plugin_info as $label => $value ) : ?>
                    <div>
                        <dt><?= htmlspecialchars( ucwords( str_replace( '_', ' ', $label ) ), ENT_QUOTES, 'UTF-8' ) ?></dt>
                        <dd><?= htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ) ?></dd>
                    </div>
            <?php endforeach; endif; ?>
        </section>
        <?php require recruitment_get_plugin_path( 'components/admin/application-table.php' ); ?>
    </main>
</div>
<footer><span>Recruitment Plugin</span><span>Applicants</span></footer>
<script src="assets/js/admin.js"></script>
</body>
</html>
