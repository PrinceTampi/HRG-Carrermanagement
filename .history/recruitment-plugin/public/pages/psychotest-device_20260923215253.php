<?php
$back_url = recruitment_get_public_url( 'psychotest-preparation' );
$home_url = recruitment_get_public_url( 'psychotest' );
?>
<div class="daw-recruitment daw-recruitment--psychotest-device">
    <?php require recruitment_get_plugin_path( 'public/components/header.php' ); ?>
    <main class="daw-recruitment__psychotest-main">
        <div class="daw-recruitment__psychotest-shell daw-recruitment__psychotest-shell--narrow">
            <a class="daw-recruitment__back-link" href="<?= esc_url( $back_url ) ?>">&larr; Kembali</a>

            <div class="daw-recruitment__psychotest-header">
                <span class="daw-recruitment__eyebrow daw-recruitment__eyebrow--red">PERIKSA PERANGKAT</span>
                <h1>Pastikan perangkat siap digunakan</h1>
                <p>Saat ini sistem akan mengecek akses kamera, mikrofon, dan audio di perangkat Anda.</p>
            </div>

            <section class="daw-recruitment__psychotest-card daw-recruitment__device-check-panel">
                <div class="daw-recruitment__device-check-box is-success">
                    <div class="daw-recruitment__device-status-icon" aria-hidden="true">✓</div>
                    <div>
                        <strong>Kamera aktif</strong>
                        <small>Izin akses kamera telah diberikan.</small>
                    </div>
                </div>
                <div class="daw-recruitment__device-check-box is-success">
                    <div class="daw-recruitment__device-status-icon" aria-hidden="true">✓</div>
                    <div>
                        <strong>Mikrofon aktif</strong>
                        <small>Izin mikrofon telah diberikan.</small>
                    </div>
                </div>
                <div class="daw-recruitment__device-check-box is-success">
                    <div class="daw-recruitment__device-status-icon" aria-hidden="true">✓</div>
                    <div>
                        <strong>Audio siap</strong>
                        <small>Volume sudah terdeteksi dengan baik.</small>
                    </div>
                </div>
                <div class="daw-recruitment__device-check-box is-warning">
                    <div class="daw-recruitment__device-status-icon" aria-hidden="true">!</div>
                    <div>
                        <strong>Jaringan terdeteksi</strong>
                        <small>Silakan tetap menjaga koneksi stabil selama tes.</small>
                    </div>
                </div>
            </section>

            <div class="daw-recruitment__psychotest-footer dawn-recruitment__psychotest-footer--split">
                <a class="daw-recruitment__button daw-recruitment__button--ghost" href="<?= esc_url( $back_url ) ?>">Cek Ulang</a>
                <a class="daw-recruitment__button daw-recruitment__button--full" href="<?= esc_url( $home_url ) ?>">Mulai Tes</a>
            </div>
        </div>
    </main>
    <?php require recruitment_get_plugin_path( 'public/components/footer.php' ); ?>
</div>
