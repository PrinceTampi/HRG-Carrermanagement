<?php
$screen_explorer_preview = '1' === sanitize_text_field( wp_unslash( $_GET['daw_ui_preview'] ?? '' ) ) || ( function_exists( 'current_user_can' ) && current_user_can( 'manage_options' ) );
if ( ! $screen_explorer_preview ) {
    return;
}

$screen_explorer_items = [
    'Publik' => [
        [ 'label' => 'Career Landing', 'screen' => 'careers' ],
        [ 'label' => 'Detail Lowongan', 'screen' => 'job-detail', 'args' => [ 'job_id' => 5 ] ],
        [ 'label' => 'Form Lamaran', 'screen' => 'application', 'args' => [ 'job_id' => 5 ] ],
        [ 'label' => 'Lamaran Berhasil', 'screen' => 'application-confirmation', 'args' => [ 'token' => 'DAW-PREVIEW-001' ] ],
        [ 'label' => 'Tracking (Input)', 'screen' => 'tracking' ],
        [ 'label' => 'Tracking Result', 'screen' => 'tracking', 'args' => [ 'token' => 'DAW-PREVIEW-001' ] ],
        [ 'label' => 'Psikotes: Review & Kirim', 'screen' => 'psych-test', 'args' => [ 'state' => 'review' ] ],
        [ 'label' => 'Psikotes: Memproses Hasil', 'screen' => 'psych-test', 'args' => [ 'state' => 'processing' ] ],
        [ 'label' => 'Psikotes: Tes Selesai', 'screen' => 'psych-test', 'args' => [ 'state' => 'complete' ] ],
    ],
    'Admin' => [
        [ 'label' => 'Admin Login', 'screen' => 'login' ],
        [ 'label' => 'Dashboard', 'screen' => 'dashboard' ],
        [ 'label' => 'Lowongan', 'screen' => 'vacancies' ],
        [ 'label' => 'Pelamar', 'screen' => 'applicants' ],
        [ 'label' => 'Lamaran', 'screen' => 'applications' ],
        [ 'label' => 'Pengaturan', 'screen' => 'settings' ],
    ],
];
?>
<aside class="daw-screen-explorer" data-screen-explorer aria-label="Screen Explorer">
    <button class="daw-screen-explorer__toggle" type="button" data-screen-explorer-toggle aria-expanded="false" aria-controls="daw-screen-explorer-panel">
        <span aria-hidden="true">D</span> Screen Explorer
    </button>
    <section class="daw-screen-explorer__panel" id="daw-screen-explorer-panel" hidden>
        <div class="daw-screen-explorer__heading">
            <div><strong>DAW Screen Explorer</strong><small>Preview halaman</small></div>
            <button type="button" data-screen-explorer-close aria-label="Tutup Screen Explorer">&times;</button>
        </div>
        <?php foreach ( $screen_explorer_items as $group => $items ) : ?>
            <div class="daw-screen-explorer__group">
                <h2><?= esc_html( $group ) ?></h2>
                <?php foreach ( $items as $item ) : ?>
                    <?php
                    $args = array_merge( [ 'daw_ui_preview' => '1' ], $item['args'] ?? [] );
                    $url  = recruitment_get_public_url( $item['screen'], $args );
                    ?>
                    <a href="<?= esc_url( $url ) ?>" <?= in_array( $item['screen'], [ 'dashboard', 'vacancies', 'applicants', 'applications', 'settings' ], true ) ? 'target="_blank" rel="noopener"' : '' ?>>
                        <span><?= esc_html( $item['label'] ) ?></span><span aria-hidden="true">&rarr;</span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </section>
</aside>
