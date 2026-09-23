<?php
$token = sanitize_text_field( wp_unslash( $_GET['token'] ?? '' ) );
$email = sanitize_text_field( wp_unslash( $_GET['email'] ?? '' ) );
?>
<div class="daw-recruitment daw-recruitment--tracking-lookup">
    <?php require recruitment_get_plugin_path( 'public/components/header.php' ); ?>
    <main class="daw-recruitment__tracking-lookup-main">
        <div class="daw-recruitment__tracking-lookup-inner">
            <div class="daw-recruitment__tracking-lookup-heading">
                <div class="daw-recruitment__tracking-lookup-icon" aria-hidden="true">&#10003;</div>
                <h1>Tracking Lamaran</h1>
                <p>Pantau perkembangan proses seleksi Anda menggunakan kode lamaran.</p>
            </div>
            <section class="daw-recruitment__tracking-lookup-card">
                <form method="get" action="<?= esc_url( recruitment_get_public_url( 'tracking' ) ) ?>">
                    <label for="tracking-token">Kode Lamaran <em>*</em></label>
                    <input id="tracking-token" name="token" required placeholder="Contoh: DAW-2026-001245" value="<?= esc_attr( $token ) ?>" autocomplete="off">
                    <label for="tracking-email">Email / Nomor Telepon <span>(opsional)</span></label>
                    <input id="tracking-email" name="email" type="text" placeholder="Verifikasi tambahan (opsional)" value="<?= esc_attr( $email ) ?>" autocomplete="email">
                    <button class="daw-recruitment__button" type="submit"><span aria-hidden="true">⌕</span> Cek Status</button>
                </form>
                <div class="daw-recruitment__tracking-lookup-divider"></div>
                <p>Belum memiliki kode lamaran?</p>
                <a class="daw-recruitment__tracking-jobs-link" href="<?= esc_url( recruitment_get_public_url( 'careers' ) ) ?>">Lihat Lowongan</a>
            </section>
            <div class="daw-recruitment__tracking-info"><strong>&#9432; Informasi</strong><p>Kode lamaran diterima setelah berhasil mengirim formulir lamaran. Anda juga bisa menemukan kode ini di email konfirmasi yang kami kirimkan.</p></div>
        </div>
    </main>
    <?php require recruitment_get_plugin_path( 'public/components/footer.php' ); ?>
</div>
