<?php
$application_code = 'DAW-PREVIEW-001';
$registered_date = '22 Agustus 2026';
$stage_label = 'Keputusan Akhir';
$stage_status = 'Tidak Lolos';
$next_stage = 'Coba Lagi';
$next_description = 'Saat ini Anda belum lolos pada proses seleksi kali ini. Tetap semangat, terus belajar, dan jangan menyerah. Masih ada kesempatan lain yang menunggu.';
$timeline = [
    [ 'label' => 'Lamaran Dikirim', 'date' => '22 Agustus 2026', 'state' => 'done', 'state_label' => 'Selesai' ],
    [ 'label' => 'Seleksi Administrasi', 'date' => '23 Agustus 2026', 'state' => 'done', 'state_label' => 'Selesai' ],
    [ 'label' => 'Tes Psikologi', 'date' => '25 Agustus 2026', 'state' => 'done', 'state_label' => 'Selesai' ],
    [ 'label' => 'Wawancara HR', 'date' => '27 Agustus 2026', 'state' => 'done', 'state_label' => 'Selesai' ],
    [ 'label' => 'Wawancara User', 'date' => '29 Agustus 2026', 'state' => 'done', 'state_label' => 'Selesai' ],
    [ 'label' => 'Keputusan Akhir', 'date' => '30 Agustus 2026', 'state' => 'current', 'state_label' => 'Tidak Lolos' ],
];
?>
<div class="daw-recruitment daw-recruitment--tracking">
    <?php require recruitment_get_plugin_path( 'public/components/header.php' ); ?>
    <main class="daw-recruitment__tracking-main">
        <div class="daw-recruitment__tracking-container">
            <section class="daw-recruitment__tracking-card daw-recruitment__tracking-summary">
                <div class="daw-recruitment__tracking-summary-top">
                    <div>
                        <span class="daw-recruitment__tracking-label">Status Lamaran</span>
                        <h1>Pelamar DAW</h1>
                        <div class="daw-recruitment__tracking-tags"><span>Sales Consultant</span><span>DAW Bitung</span></div>
                    </div>
                    <span class="daw-recruitment__tracking-status"><?= esc_html( $stage_label ) ?></span>
                </div>
                <div class="daw-recruitment__tracking-summary-meta">
                    <div><span>Kode Lamaran</span><strong><?= esc_html( $application_code ) ?></strong></div>
                    <div><span>Tanggal Daftar</span><strong><?= esc_html( $registered_date ) ?></strong></div>
                </div>
            </section>

            <section class="daw-recruitment__tracking-card daw-recruitment__tracking-current">
                <div class="daw-recruitment__tracking-current-head" style="background: #f5f5f7; border: 1px solid #e0e0e3; color: #222; margin: 0 0 13px;">
                    <div>
                        <span class="daw-recruitment__tracking-label" style="color: #6e6e73;">Tahap Saat Ini</span>
                        <h2><span aria-hidden="true">●</span> <?= esc_html( $stage_label ) ?></h2>
                    </div>
                    <span class="daw-recruitment__tracking-running" style="background: #fff0f2; border: 1px solid #f6c8d0; color: var(--daw-red);"><?= esc_html( $stage_status ) ?></span>
                </div>
                <p style="font-size: 12px; line-height: 1.6; color: #444;">Maaf, Anda belum lolos pada seleksi kali ini. Namun, setiap proses adalah pengalaman berharga. Tetap semangat dan jangan menyerah.</p>
                <span class="daw-recruitment__tracking-notice" style="background: #fff7d6; color: #8a5b00; border: 1px solid #f1d589;">💪 Masih ada kesempatan lain, tetap semangat!</span>
                <div class="daw-recruitment__tracking-availability" style="border-color: #f3d9a8; background: #fffaf0;">
                    <strong>Pesan Motivasi</strong>
                    <span>Kegagalan hari ini bukan akhir dari perjalanan Anda. Terus berusaha, tingkatkan kemampuan, dan coba lagi di kesempatan berikutnya.</span>
                </div>
                <a class="daw-recruitment__button" href="<?= esc_url( recruitment_get_public_url( 'careers' ) ) ?>">Lihat Lowongan Lainnya</a>
            </section>

            <section class="daw-recruitment__tracking-card daw-recruitment__tracking-next">
                <span class="daw-recruitment__tracking-next-icon" aria-hidden="true" style="background: #fff4d7; color: #b7791f;">▥</span>
                <div>
                    <span class="daw-recruitment__tracking-label">Tahap Selanjutnya</span>
                    <h2><?= esc_html( $next_stage ) ?></h2>
                    <p><?= esc_html( $next_description ) ?></p>
                </div>
            </section>

            <section class="daw-recruitment__tracking-card daw-recruitment__tracking-history">
                <h2>Riwayat Proses</h2>
                <?php foreach ( $timeline as $item ) : ?>
                    <div class="daw-recruitment__tracking-step is-<?= esc_attr( $item['state'] ) ?>">
                        <span aria-hidden="true"><?= 'done' === $item['state'] ? '✓' : ( 'current' === $item['state'] ? '●' : '○' ); ?></span>
                        <div>
                            <strong><?= esc_html( $item['label'] ) ?></strong>
                            <?php if ( '' !== $item['date'] ) : ?><small><?= esc_html( $item['state_label'] ) ?> — <?= esc_html( $item['date'] ) ?></small><?php endif; ?>
                        </div>
                        <b><?= esc_html( $item['state_label'] ) ?></b>
                    </div>
                <?php endforeach; ?>
            </section>

            <p class="daw-recruitment__tracking-note">ⓘ Urutan tahapan proses rekrutmen dapat disesuaikan oleh tim HR sesuai kebutuhan posisi dan proses seleksi.</p>
            <div class="daw-recruitment__tracking-footer">
                <small>Update terakhir: <strong><?= esc_html( $registered_date ) ?></strong><br>Admin telah memperbarui status lamaran Anda.</small>
                <a href="<?= esc_url( recruitment_get_public_url( 'careers' ) ) ?>">Lihat Lowongan Lain</a>
            </div>
            <a class="daw-recruitment__tracking-back" href="<?= esc_url( recruitment_get_public_url( 'careers' ) ) ?>">&larr; Kembali ke Beranda Karier</a>
        </div>
    </main>
    <?php require recruitment_get_plugin_path( 'public/components/footer.php' ); ?>
</div>
