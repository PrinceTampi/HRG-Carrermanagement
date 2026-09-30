<main class="admin-login">
    <section class="admin-login__intro" aria-label="Tentang DAW Recruitment">
        <div class="admin-login__brand">
            <span class="admin-login__logo">DAW</span>
            <span><strong>DAW Recruitment</strong><small>PT. Daya Adicipta Wisesa</small></span>
        </div>
        <div class="admin-login__message">
            <h1>Sistem Manajemen<br>Rekrutmen</h1>
            <p>Platform internal HR untuk mengelola proses rekrutmen secara efisien — dari permintaan SDM hingga keputusan akhir.</p>
        </div>
        <dl class="admin-login__stats" aria-label="Cakupan rekrutmen">
            <div><dt>Wilayah</dt><dd>3</dd></div>
            <div><dt>Dealer</dt><dd>12+</dd></div>
            <div><dt>Posisi</dt><dd>20+</dd></div>
        </dl>
    </section>

    <section class="admin-login__panel" aria-labelledby="admin-login-title">
        <div class="admin-login__form-wrap">
            <h2 id="admin-login-title">Masuk ke Portal Admin</h2>
            <p class="admin-login__subtitle">Gunakan akun HR yang telah didaftarkan.</p>

            <?php if ( ! empty( $error ) ) : ?>
                <div class="admin-login__error" role="alert"><?= esc_html( $error ) ?></div>
            <?php endif; ?>

            <form class="admin-login__form" method="post" action="?page=login">
                <label for="username">Email</label>
                <input id="username" name="username" type="email" autocomplete="username" placeholder="nama@perusahaan.id" required>

                <label for="password">Password</label>
                <div class="admin-login__password-field">
                    <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Masukkan password" required>
                    <button class="admin-login__password-toggle" type="button" aria-label="Tampilkan password" aria-pressed="false">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.3-6 9.5-6 9.5 6 9.5 6-3.3 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                    </button>
                </div>

                <button class="admin-login__submit" type="submit">Masuk</button>
            </form>
            <p class="admin-login__notice">Akses terbatas. Hanya untuk administrator HR DAW yang berwenang.</p>
        </div>
    </section>
</main>
<?php require recruitment_get_plugin_path( 'public/components/screen-explorer.php' ); ?>
<script>
document.querySelectorAll('.admin-login__password-toggle').forEach(function (toggle) {
    toggle.addEventListener('click', function () {
        var password = document.getElementById('password');
        var isVisible = password.type === 'text';
        password.type = isVisible ? 'password' : 'text';
        toggle.setAttribute('aria-pressed', String(!isVisible));
        toggle.setAttribute('aria-label', isVisible ? 'Tampilkan password' : 'Sembunyikan password');
    });
});
</script>
