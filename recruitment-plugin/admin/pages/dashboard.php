<?php $user = wp_get_current_user(); ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard HRD | Recruitment Plugin</title>
    <link rel="stylesheet" href="assets/css/public.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php require __DIR__ . '/../components/header.php'; ?>
<div class="admin-layout">
    <?php require __DIR__ . '/../components/sidebar.php'; ?>
    <main class="content-shell">
        <p class="eyebrow">Authenticated area</p>
        <h1>Good to see you, <?= htmlspecialchars( $user['display_name'], ENT_QUOTES, 'UTF-8' ) ?>.</h1>
        <p class="lede">Ringkasan aktivitas rekrutmen.</p>
        <section class="stats-row">
            <?php
            // TODO: Ambil data dari Recruitment_Database
            $stats = [
                [ 'label' => 'Total Vacancies',       'value' => '—' ],
                [ 'label' => 'Total Applicants',       'value' => '—' ],
                [ 'label' => 'New Applications',       'value' => '—' ],
                [ 'label' => 'Applications in Review', 'value' => '—' ],
            ];
            foreach ( $stats as $stat ) :
                require __DIR__ . '/../components/stats-card.php';
            endforeach;
            ?>
        </section>
    </main>
</div>
<footer><span>Recruitment Plugin</span><span>HRD Dashboard</span></footer>
<script src="assets/js/admin.js"></script>
</body>
</html>
