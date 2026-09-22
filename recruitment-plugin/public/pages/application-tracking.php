<?php
$token = sanitize_text_field( wp_unslash( $_GET['token'] ?? '' ) );
$email = sanitize_text_field( wp_unslash( $_GET['email'] ?? '' ) );
$has_lookup = '' !== $token;
$application = $has_lookup ? ( new Recruitment_Database() )->find_application_by_token( $token, $email ) : null;
$lookup_requested = '' !== $token;
$has_lookup = null !== $application;
$application_code = $application['token'] ?? '';
$registered_date = ! empty( $application['created_at'] ) ? gmdate( 'd F Y', strtotime( (string) $application['created_at'] ) ) : '';
$candidate_name = $application['name'] ?? 'Pelamar DAW';
$application_status = $application['status'] ?? 'submitted';
?>
<div class="daw-recruitment daw-recruitment--tracking">
    <?php require recruitment_get_plugin_path( 'components/public/header.php' ); ?>
    <main class="daw-recruitment__tracking-main">
        <div class="daw-recruitment__tracking-container">
            <?php if ( $lookup_requested && ! $has_lookup ) : ?>
                <section class="daw-recruitment__panel daw-recruitment__tracking-empty daw-recruitment__tracking-error">
                    <span class="daw-recruitment__tracking-error-icon" aria-hidden="true">!</span>
                    <span class="daw-recruitment__eyebrow">Tracking Lamaran</span>
                    <h1>Lamaran tidak ditemukan</h1>
                    <p>Periksa kembali kode lamaran Anda dan coba lagi.</p>
                    <a class="daw-recruitment__button" href="<?= esc_url( recruitment_get_public_url( 'tracking' ) ) ?>">Coba Lagi</a>
                </section>
            <?php elseif ( ! $has_lookup ) : ?>
                <section class="daw-recruitment__panel daw-recruitment__tracking-empty">
                    <span class="daw-recruitment__eyebrow">Tracking Lamaran</span>
                    <h1>Data lamaran belum lengkap</h1>
                    <p>Gunakan kode lamaran dan email yang kamu gunakan saat mendaftar untuk melihat status.</p>
                    <a class="daw-recruitment__button" href="<?= esc_url( recruitment_get_public_url( 'tracking' ) ) ?>">Cek Status Lamaran</a>
                </section>
            <?php else : ?>
                <section class="daw-recruitment__tracking-card daw-recruitment__tracking-summary">
                    <div class="daw-recruitment__tracking-summary-top"><div><span class="daw-recruitment__tracking-label">Status Lamaran</span><h1><?= esc_html( $candidate_name ) ?></h1><div class="daw-recruitment__tracking-tags"><span>Lamaran diterima</span></div></div><span class="daw-recruitment__tracking-status"><?= esc_html( ucfirst( $application_status ) ) ?></span></div>
                    <div class="daw-recruitment__tracking-summary-meta"><div><span>Kode Lamaran</span><strong><?= esc_html( $application_code ) ?></strong></div><div><span>Tanggal Daftar</span><strong><?= esc_html( $registered_date ) ?></strong></div></div>
                </section>
                <section class="daw-recruitment__tracking-card daw-recruitment__tracking-real-result"><span class="daw-recruitment__tracking-label">Data Lamaran</span><h2><?= esc_html( $candidate_name ) ?></h2><dl><div><dt>Kode Lamaran</dt><dd><?= esc_html( $application_code ) ?></dd></div><div><dt>Status Lamaran</dt><dd><?= esc_html( ucfirst( $application_status ) ) ?></dd></div><div><dt>Tanggal Dikirim</dt><dd><?= esc_html( $registered_date ) ?></dd></div></dl></section>
            <?php endif; ?>
            <a class="daw-recruitment__tracking-back" href="<?= esc_url( recruitment_get_public_url( 'careers' ) ) ?>">&larr; Kembali ke Beranda Karier</a>
        </div>
    </main>
    <?php require recruitment_get_plugin_path( 'components/public/footer.php' ); ?>
</div>
