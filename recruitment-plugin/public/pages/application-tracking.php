<?php
$token = sanitize_text_field( wp_unslash( $_GET['token'] ?? '' ) );
$email = sanitize_email( wp_unslash( $_GET['email'] ?? '' ) );
$has_lookup = '' !== $token && is_email( $email );
$application = $has_lookup ? ( new Recruitment_Database() )->find_application_by_token( $token, $email ) : null;
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
            <?php if ( ! $has_lookup ) : ?>
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
                <section class="daw-recruitment__tracking-card daw-recruitment__tracking-current">
                    <div class="daw-recruitment__tracking-current-head"><div><span class="daw-recruitment__tracking-label">Tahap Saat Ini</span><h2><span aria-hidden="true">●</span> Tes Psikologi</h2></div><span class="daw-recruitment__tracking-running">Sedang berlangsung</span></div>
                    <p>Tes psikologi Anda sudah tersedia. Silakan selesaikan sebelum batas waktu yang ditentukan.</p>
                    <span class="daw-recruitment__tracking-notice">◷ Durasi: &plusmn;60 menit. Siapkan kamera dan koneksi stabil.</span>
                    <div class="daw-recruitment__tracking-availability"><strong>Tes Psikologi Anda Sudah Tersedia</strong><span>Tes dapat dilakukan mulai: Senin, 25 Agustus 2026 &mdash; 08:00 WITA</span></div>
                    <button class="daw-recruitment__button" type="button">Mulai Tes &rarr;</button>
                </section>
                <section class="daw-recruitment__tracking-card daw-recruitment__tracking-next"><span class="daw-recruitment__tracking-next-icon" aria-hidden="true">▥</span><div><span class="daw-recruitment__tracking-label">Tahap Selanjutnya</span><h2>Wawancara HR</h2><p>Jadwal dan informasi selanjutnya akan disampaikan oleh tim HR setelah hasil tes divalidasi.</p></div></section>
                <section class="daw-recruitment__tracking-card daw-recruitment__tracking-history"><h2>Riwayat Proses</h2><div class="daw-recruitment__tracking-step is-done"><span>✓</span><div><strong>Lamaran Dikirim</strong><small>Selesai &mdash; 5 Agustus 2026</small></div><b>Selesai</b></div><div class="daw-recruitment__tracking-step is-done"><span>✓</span><div><strong>Seleksi Administrasi</strong><small>Selesai &mdash; 10 Agustus 2026</small></div><b>Selesai</b></div><div class="daw-recruitment__tracking-step is-current"><span>!</span><div><strong>Tes Psikologi</strong><small>Sedang berlangsung</small></div><b>Sedang berlangsung</b></div><div class="daw-recruitment__tracking-divider"></div><span class="daw-recruitment__tracking-label">Tahapan Berikutnya</span><div class="daw-recruitment__tracking-step is-pending"><span>○</span><div><strong>Wawancara HR</strong></div><b>Menunggu</b></div><div class="daw-recruitment__tracking-step is-pending"><span>○</span><div><strong>Wawancara User</strong></div><b>Menunggu</b></div><div class="daw-recruitment__tracking-step is-pending"><span>○</span><div><strong>Keputusan Akhir</strong></div><b>Menunggu</b></div></section>
                <p class="daw-recruitment__tracking-note">ⓘ Urutan tahapan proses rekrutmen dapat disesuaikan oleh tim HR sesuai kebutuhan posisi dan proses seleksi.</p>
                <div class="daw-recruitment__tracking-footer"><span>◷ Update terakhir: <strong>25 Agustus 2026</strong><small>Admin telah memperbarui status lamaran Anda.</small></span><a href="<?= esc_url( recruitment_get_public_url( 'careers' ) ) ?>">Lihat Lowongan Lain</a></div>
            <?php endif; ?>
            <a class="daw-recruitment__tracking-back" href="<?= esc_url( recruitment_get_public_url( 'careers' ) ) ?>">&larr; Kembali ke Beranda Karier</a>
        </div>
    </main>
    <?php require recruitment_get_plugin_path( 'components/public/footer.php' ); ?>
</div>
