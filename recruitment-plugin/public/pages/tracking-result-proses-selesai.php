<?php
$application_code = 'DAW-PREVIEW-001';
$registered_date = '22 Agustus 2026';
$stage_label = 'Proses Selesai';
$stage_status = 'Proses Selesai';
$next_stage = 'Selesai';
$next_description = 'Semua tahapan rekrutmen telah selesai dan proses penempatan kerja akan dilanjutkan sesuai jadwal internal perusahaan.';
$timeline = [
    [ 'label' => 'Lamaran Dikirim', 'date' => '22 Agustus 2026', 'state' => 'done', 'state_label' => 'Selesai' ],
    [ 'label' => 'Seleksi Administrasi', 'date' => '23 Agustus 2026', 'state' => 'done', 'state_label' => 'Selesai' ],
    [ 'label' => 'Tes Psikologi', 'date' => '25 Agustus 2026', 'state' => 'done', 'state_label' => 'Selesai' ],
    [ 'label' => 'Wawancara HR', 'date' => '27 Agustus 2026', 'state' => 'done', 'state_label' => 'Selesai' ],
    [ 'label' => 'Wawancara User', 'date' => '29 Agustus 2026', 'state' => 'done', 'state_label' => 'Selesai' ],
    [ 'label' => 'Keputusan Akhir', 'date' => '30 Agustus 2026', 'state' => 'done', 'state_label' => 'Selesai' ],
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
                <div class="daw-recruitment__tracking-current-head">
                    <div>
                        <span class="daw-recruitment__tracking-label">Tahap Saat Ini</span>
                        <h2><span aria-hidden="true">●</span> <?= esc_html( $stage_label ) ?></h2>
                    </div>
                    <span class="daw-recruitment__tracking-running"><?= esc_html( $stage_status ) ?></span>
                </div>
                <p>Proses rekrutmen telah selesai dan semua tahapan seleksi telah dilalui sesuai prosedur yang berlaku.</p>
                <span class="daw-recruitment__tracking-notice">◷ Terima kasih atas partisipasi Anda dalam proses rekrutmen DAW.</span>
                <div class="daw-recruitment__tracking-availability"><strong>Catatan</strong><span>Semua tahapan telah selesai dan tidak ada status lanjutan yang perlu ditunggu.</span></div>
            </section>
            <section class="daw-recruitment__tracking-card daw-recruitment__tracking-next">
                <span class="daw-recruitment__tracking-next-icon" aria-hidden="true">▥</span>
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
