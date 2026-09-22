<?php
$token = sanitize_text_field( wp_unslash( $_GET['token'] ?? '' ) );
$email = sanitize_text_field( wp_unslash( $_GET['email'] ?? '' ) );
$ui_preview = '1' === sanitize_text_field( wp_unslash( $_GET['daw_ui_preview'] ?? '' ) );
$has_lookup = '' !== $token;
$application = $has_lookup ? ( new Recruitment_Database() )->find_application_by_token( $token, $email ) : null;
$application = $application ?: ( $ui_preview && $has_lookup ? [ 'token' => $token, 'created_at' => current_time( 'mysql' ), 'name' => 'Kandidat Preview', 'status' => 'submitted' ] : null );
$lookup_requested = '' !== $token;
$has_lookup = null !== $application;
$application_code = $application['token'] ?? '';
$registered_date = ! empty( $application['created_at'] ) ? gmdate( 'd F Y', strtotime( (string) $application['created_at'] ) ) : '';
$candidate_name = $application['name'] ?? 'Pelamar DAW';
$application_status = $application['status'] ?? 'submitted';
$job_title = $application['job_title'] ?? 'Sales Consultant';
$job_location = $application['job_location'] ?? 'DAW Bitung';
$current_stage = 'Tes Psikologi';
$current_stage_status = 'Sedang Berlangsung';
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
                    <div class="daw-recruitment__tracking-summary-top"><div><span class="daw-recruitment__tracking-label">Status Lamaran</span><h1><?= esc_html( $candidate_name ) ?></h1><div class="daw-recruitment__tracking-tags"><span><?= esc_html( $job_title ) ?></span><span><?= esc_html( $job_location ) ?></span></div></div><span class="daw-recruitment__tracking-status"><?= esc_html( $current_stage ) ?></span></div>
                    <div class="daw-recruitment__tracking-summary-meta"><div><span>Kode Lamaran</span><strong><?= esc_html( $application_code ) ?></strong></div><div><span>Tanggal Daftar</span><strong><?= esc_html( $registered_date ) ?></strong></div></div>
                </section>
                <section class="daw-recruitment__tracking-card daw-recruitment__tracking-current">
                    <div class="daw-recruitment__tracking-current-head"><div><span class="daw-recruitment__tracking-label">Tahap Saat Ini</span><h2><?= esc_html( $current_stage ) ?></h2></div><span class="daw-recruitment__tracking-running"><?= esc_html( $current_stage_status ) ?></span></div>
                    <p>Tes psikologi Anda sudah tersedia. Silakan selesaikan sebelum batas waktu yang ditentukan.</p>
                    <span class="daw-recruitment__tracking-notice">◷ Durasi ±60 menit. Siapkan kamera dan koneksi stabil.</span>
                    <div class="daw-recruitment__tracking-availability"><strong>Tes Psikologi Anda Sudah Tersedia</strong><span>Tes dapat dilakukan mulai <?= esc_html( $registered_date ) ?> — 08:00 WITA</span></div>
                    <a class="daw-recruitment__button" href="#">Mulai Tes &rarr;</a>
                </section>
                <section class="daw-recruitment__tracking-card daw-recruitment__tracking-next"><span class="daw-recruitment__tracking-next-icon" aria-hidden="true">▥</span><div><span class="daw-recruitment__tracking-label">Tahap Selanjutnya</span><h2>Wawancara HR</h2><p>Jadwal dan informasi selanjutnya akan disampaikan oleh tim HR setelah hasil tes divalidasi.</p></div></section>
                <section class="daw-recruitment__tracking-card daw-recruitment__tracking-history">
                    <h2>Riwayat Proses</h2>
                    <div class="daw-recruitment__tracking-step is-done"><span aria-hidden="true">✓</span><div><strong>Lamaran Dikirim</strong><small>Selesai — <?= esc_html( $registered_date ) ?></small></div><b>Selesai</b></div>
                    <div class="daw-recruitment__tracking-step is-done"><span aria-hidden="true">✓</span><div><strong>Seleksi Administrasi</strong><small>Selesai — <?= esc_html( $registered_date ) ?></small></div><b>Selesai</b></div>
                    <div class="daw-recruitment__tracking-step is-current"><span aria-hidden="true">●</span><div><strong><?= esc_html( $current_stage ) ?></strong><small><?= esc_html( $current_stage_status ) ?></small></div><b><?= esc_html( $current_stage_status ) ?></b></div>
                    <div class="daw-recruitment__tracking-divider"></div>
                    <span class="daw-recruitment__tracking-label">Tahapan Berikutnya</span>
                    <div class="daw-recruitment__tracking-step is-pending"><span aria-hidden="true">○</span><div><strong>Wawancara HR</strong></div><b>Menunggu</b></div>
                    <div class="daw-recruitment__tracking-step is-pending"><span aria-hidden="true">○</span><div><strong>Wawancara User</strong></div><b>Menunggu</b></div>
                    <div class="daw-recruitment__tracking-step is-pending"><span aria-hidden="true">○</span><div><strong>Keputusan Akhir</strong></div><b>Menunggu</b></div>
                </section>
                <p class="daw-recruitment__tracking-note">ⓘ Urutan tahapan proses rekrutmen dapat disesuaikan oleh tim HR sesuai kebutuhan posisi dan proses seleksi.</p>
                <div class="daw-recruitment__tracking-footer"><small>Update terakhir: <strong><?= esc_html( $registered_date ) ?></strong><br>Admin telah memperbarui status lamaran Anda.</small><a href="<?= esc_url( recruitment_get_public_url( 'careers' ) ) ?>">Lihat Lowongan Lain</a></div>
            <?php endif; ?>
            <a class="daw-recruitment__tracking-back" href="<?= esc_url( recruitment_get_public_url( 'careers' ) ) ?>">&larr; Kembali ke Beranda Karier</a>
        </div>
    </main>
    <?php require recruitment_get_plugin_path( 'components/public/footer.php' ); ?>
</div>
