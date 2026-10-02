<?php
$target = 'user_department' === ( $target ?? '' ) ? 'user_department' : 'hr';
$portal_url = recruitment_get_public_url( 'portal' );
$public_url = recruitment_get_public_url( 'careers' );
$login_action = recruitment_get_public_url( 'portal', [ 'recruitment_page' => 'login', 'target' => $target ] );
$lost_password_url = function_exists( 'wp_lostpassword_url' ) ? wp_lostpassword_url( $login_action ) : wp_lostpassword_url();
$portal_title = 'user_department' === $target ? 'Admin User Department' : 'Admin HR';
?>
<main class="daw-admin-login">
    <a class="daw-admin-login__brand" href="<?= esc_url( $portal_url ) ?>" aria-label="DAW Recruitment Admin">
        <span aria-hidden="true">D</span>
    </a>
    <h1>DAW Recruitment Admin</h1>
    <p class="daw-admin-login__subtitle">Sistem Manajemen Rekrutmen</p>

    <section class="daw-admin-login__card" aria-labelledby="daw-admin-login-title">
        <h2 id="daw-admin-login-title"><?= esc_html( $portal_title ) ?></h2>
        <?php if ( ! empty( $error ) ) : ?>
            <p class="daw-admin-login__error" role="alert"><?= esc_html( $error ) ?></p>
        <?php endif; ?>
        <form method="post" action="<?= esc_url( $login_action ) ?>">
            <?php wp_nonce_field( 'daw_admin_portal_login' ); ?>
            <input type="hidden" name="target" value="<?= esc_attr( $target ) ?>" />
            <label for="daw-admin-username">Username / Email</label>
            <input id="daw-admin-username" name="username" type="text" autocomplete="username" placeholder="admin@daw.co.id" required />
            <label for="daw-admin-password">Password</label>
            <input id="daw-admin-password" name="password" type="password" autocomplete="current-password" placeholder="Masukkan password" required />
            <button type="submit">Login</button>
        </form>
        <a class="daw-admin-login__lost-password" href="<?= esc_url( $lost_password_url ) ?>">Lupa Password?</a>
    </section>

    <a class="daw-admin-login__back" href="<?= esc_url( $public_url ) ?>">&larr; Kembali ke Halaman Publik</a>
    <p class="daw-admin-login__copyright">&copy; <?= esc_html( gmdate( 'Y' ) ) ?> PT. Daya Adicipta Wisesa. Akses terbatas untuk staf yang berwenang.</p>
</main>
