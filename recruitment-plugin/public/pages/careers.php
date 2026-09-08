<?php $logged_in = is_user_logged_in(); ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Halaman karir — temukan lowongan dan bergabunglah bersama kami.">
    <title>Karir | Recruitment Plugin</title>
    <link rel="stylesheet" href="assets/css/public.css">
</head>
<body>
<?php require __DIR__ . '/../components/header.php'; ?>
<main>
    <section class="hero">
        <div class="hero-copy">
            <p class="eyebrow">Halaman Karir / Lowongan</p>
            <h1>Bergabung bersama tim kami.</h1>
            <p class="lede">Temukan lowongan yang sesuai dengan keahlian dan passion kamu. Kami selalu mencari talenta terbaik.</p>
            <a class="button" href="<?= $logged_in ? '?page=dashboard' : '?page=application-form' ?>">
                <?= $logged_in ? 'Open dashboard' : 'Lamar Sekarang' ?> <span aria-hidden="true">-&gt;</span>
            </a>
        </div>
        <div class="hero-panel" aria-label="Ringkasan fitur">
            <span class="panel-kicker">KARIR / 01</span>
            <strong>Open Positions</strong>
            <strong>Growth Culture</strong>
            <strong>Competitive Benefits</strong>
            <span class="panel-line"></span>
            <small>Recruitment powered by Recruitment Plugin.</small>
        </div>
    </section>
    <section class="about" id="lowongan">
        <p class="eyebrow">Lowongan tersedia</p>
        <h2>Posisi yang sedang dibuka.</h2>
        <div class="feature-grid">
            <?php
            // TODO: Ganti dengan data dari Recruitment_Database::get_vacancies()
            $dummy_jobs = [
                [ 'title' => 'Frontend Developer', 'dept' => 'Technology',  'type' => 'Full Time' ],
                [ 'title' => 'HR Specialist',       'dept' => 'Human Resources', 'type' => 'Full Time' ],
                [ 'title' => 'Marketing Analyst',   'dept' => 'Marketing',   'type' => 'Contract' ],
            ];
            foreach ( $dummy_jobs as $i => $job ) :
                require __DIR__ . '/../components/job-card.php';
            endforeach;
            ?>
        </div>
    </section>
</main>
<?php require __DIR__ . '/../components/footer.php'; ?>
<script src="assets/js/public.js"></script>
</body>
</html>
