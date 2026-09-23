<?php
$token = sanitize_text_field( wp_unslash( $_GET['token'] ?? '' ) );
$ui_preview = '1' === sanitize_text_field( wp_unslash( $_GET['daw_ui_preview'] ?? '' ) );
$application = '' !== $token ? ( new Recruitment_Database() )->find_application_by_token( $token ) : null;
if ( ! $application && $ui_preview ) {
    $application = [ 'name' => 'Kandidat Preview' ];
}
if ( ! $application ) {
    wp_safe_redirect( recruitment_get_public_url( 'application-status' ) );
    exit;
}
$name = (string) ( $application['name'] ?? 'Kandidat' );
?>
<div class="daw-recruitment daw-recruitment--application daw-recruitment--confirmation">
    <?php require recruitment_get_plugin_path( 'public/components/header.php' ); ?>
    <main class="daw-recruitment__section daw-recruitment__application-main">
        <div class="daw-recruitment__container daw-recruitment__form-container">
            <section class="daw-recruitment__panel daw-recruitment__success">
                <div class="daw-recruitment__success-icon" aria-hidden="true">&#10003;</div>
                <h1>Lamaran Berhasil Dikirim</h1>
                <p>Terima kasih, <?= esc_html( $name ) ?>. Tim recruitment akan meninjau data kamu.</p>
                <div class="daw-recruitment__application-code"><small>KODE LAMARAN ANDA</small><span class="daw-recruitment__application-token" data-application-token><?= esc_html( $token ) ?></span><button class="daw-recruitment__copy-token" type="button" data-copy-token aria-label="Salin kode lamaran" title="Salin kode lamaran">&#10697;</button><span class="daw-recruitment__copy-feedback" role="status" aria-live="polite"></span></div>
                <div class="daw-recruitment__success-notice"><strong>Langkah Selanjutnya:</strong><ol><li>Tim HR akan meninjau dokumen Anda</li><li>Notifikasi akan dikirim ke email Anda</li><li>Gunakan kode lamaran untuk tracking status</li></ol></div>
                <div class="daw-recruitment__form-actions"><a class="daw-recruitment__button" href="<?= esc_url( recruitment_get_public_url( 'tracking', [ 'token' => $token, 'daw_ui_preview' => $ui_preview ? '1' : '0' ] ) ) ?>">Tracking Lamaran</a><a class="daw-recruitment__button daw-recruitment__button--muted" href="<?= esc_url( recruitment_get_public_url( 'careers', [ 'daw_ui_preview' => $ui_preview ? '1' : '0' ] ) ) ?>">Kembali ke Career</a></div>
            </section>
        </div>
    </main>
    <?php require recruitment_get_plugin_path( 'public/components/footer.php' ); ?>
</div>