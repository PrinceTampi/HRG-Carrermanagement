<?php
$token = sanitize_text_field( wp_unslash( $_GET['token'] ?? '' ) );
$email = sanitize_email( wp_unslash( $_GET['email'] ?? '' ) );
$tracking_url = recruitment_get_public_url( 'tracking-detail' );
?>
<div class="daw-recruitment"><div class="daw-recruitment__section"><div class="daw-recruitment__container daw-recruitment__form-container">
    <?php require recruitment_get_plugin_path( 'components/public/header.php' ); ?>
    <section class="daw-recruitment__panel"><span class="daw-recruitment__eyebrow">Cek Status Lamaran</span><h1>Status Lamaran Kamu</h1><p class="daw-recruitment__form-lede">Masukkan kode lamaran untuk melihat perkembangan proses recruitment.</p><form class="daw-recruitment__application-form" method="get" action="<?= esc_url( $tracking_url ) ?>"><label>Kode Lamaran<input name="token" required placeholder="Contoh: REC-20260908-001" value="<?= esc_attr( $token ) ?>"></label><label>Email<input name="email" type="email" required placeholder="email@example.com" value="<?= esc_attr( $email ) ?>"></label><button class="daw-recruitment__button" type="submit">Cek Status</button></form></section>
</div></div><?php require recruitment_get_plugin_path( 'components/public/footer.php' ); ?></div>
