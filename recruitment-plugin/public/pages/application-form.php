<?php
$submitted = false;
$errors = [];
if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
    if ( ! isset( $_POST['recruitment_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['recruitment_nonce'] ) ), 'recruitment_apply' ) ) {
        $errors[] = 'Sesi form tidak valid. Silakan coba lagi.';
    }
    $name = sanitize_text_field( wp_unslash( $_POST['full_name'] ?? '' ) );
    $email = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
    if ( '' === $name || ! is_email( $email ) ) {
        $errors[] = 'Nama dan email wajib diisi dengan benar.';
    }
    $submitted = ! $errors;
}
$job_id = absint( $_GET['job_id'] ?? $_POST['job_id'] ?? 0 );
?>
<div class="daw-recruitment"><div class="daw-recruitment__section"><div class="daw-recruitment__container daw-recruitment__form-container">
    <?php require recruitment_get_plugin_path( 'components/public/header.php' ); ?>
    <?php if ( $submitted ) : ?><section class="daw-recruitment__panel daw-recruitment__success"><h1>Lamaran Berhasil Dikirim</h1><p>Terima kasih, <?= esc_html( $name ) ?>. Tim recruitment akan meninjau data kamu.</p><div class="daw-recruitment__application-code">REC-<?= esc_html( gmdate( 'Ymd' ) ) ?>-<?= esc_html( str_pad( (string) wp_rand( 1, 999 ), 3, '0', STR_PAD_LEFT ) ) ?></div><a class="daw-recruitment__button" href="<?= esc_url( recruitment_get_public_url( 'tracking' ) ) ?>">Tracking Lamaran</a></section>
    <?php else : ?><a class="daw-recruitment__back" href="<?= esc_url( recruitment_get_public_url( 'job-detail', [ 'job_id' => $job_id ] ) ) ?>">&larr; Kembali ke Detail Lowongan</a><section class="daw-recruitment__panel"><span class="daw-recruitment__eyebrow">Form Pendaftaran</span><h1>Lamar Posisi</h1><p class="daw-recruitment__form-lede">Isi data berikut dengan lengkap dan benar.</p><?php foreach ( $errors as $error ) : ?><div class="daw-recruitment__alert"><?= esc_html( $error ) ?></div><?php endforeach; ?><form class="daw-recruitment__application-form" method="post"><input type="hidden" name="job_id" value="<?= esc_attr( $job_id ) ?>"><?php wp_nonce_field( 'recruitment_apply', 'recruitment_nonce' ); ?><label>Nama Lengkap<input name="full_name" required value="<?= esc_attr( $_POST['full_name'] ?? '' ) ?>"></label><label>Email<input name="email" type="email" required value="<?= esc_attr( $_POST['email'] ?? '' ) ?>"></label><label>Nomor Telepon<input name="phone" type="tel" value="<?= esc_attr( $_POST['phone'] ?? '' ) ?>"></label><label>Pendidikan Terakhir<select name="education"><option value="">Pilih pendidikan</option><option>SMA / SMK</option><option>D3</option><option>S1</option><option>S2</option></select></label><label>Alamat<textarea name="address" rows="3"></textarea></label><label class="daw-recruitment__agreement"><input type="checkbox" name="agreed" required> Saya menyatakan data yang diberikan benar dan dapat digunakan untuk keperluan rekrutmen.</label><button class="daw-recruitment__button" type="submit">Kirim Lamaran</button></form></section><?php endif; ?>
</div></div><?php require recruitment_get_plugin_path( 'components/public/footer.php' ); ?></div>
