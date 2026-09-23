<?php
$back_url = recruitment_get_public_url( 'psychotest' );
$device_url = recruitment_get_public_url( 'psychotest-device' );
?>
<div class="daw-recruitment daw-recruitment--psychotest-preparation">
    <?php require recruitment_get_plugin_path( 'public/components/header.php' ); ?>
    <main class="daw-recruitment__psychotest-main">
        <div class="daw-recruitment__psychotest-shell daw-recruitment__psychotest-shell--narrow">
            <a class="daw-recruitment__back-link" href="<?= esc_url( $back_url ) ?>">&larr; Kembali</a>

            <div class="daw-recruitment__psychotest-header">
                <span class="daw-recruitment__eyebrow daw-recruitment__eyebrow--red">PERSIAPAN ASSESSMENT</span>
                <h1>Pastikan Anda siap sebelum memulai</h1>
                <p>Silakan baca instruksi berikut agar proses tes psikologi berjalan lancar dan terhindar dari gangguan teknis.</p>
            </div>

            <section class="daw-recruitment__psychotest-card">
                <h2 class="daw-recruitment__mini-title">Aturan umum</h2>
                <ul class="daw-recruitment__check-list">
                    <li>Kerjakan tes di ruangan yang tenang dan tidak bising.</li>
                    <li>Gunakan laptop atau komputer dengan kamera dan mikrofon yang berfungsi.</li>
                    <li>Pastikan koneksi internet stabil dan tidak terputus selama tes berlangsung.</li>
                    <li>Jangan membuka tab lain atau aplikasi lain selama assessment.</li>
                    <li>Isi semua jawaban dengan jujur dan sesuai kondisi Anda saat ini.</li>
                </ul>
            </section>

            <section class="daw-recruitment__psychotest-card">
                <h2 class="daw-recruitment__mini-title">Persyaratan perangkat</h2>
                <div class="daw-recruitment__device-checklist">
                    <div class="daw-recruitment__device-item">
                        <span>📹</span>
                        <div>
                            <strong>Kamera</strong>
                            <p>Harus aktif dan dapat melihat wajah Anda selama tes.</p>
                        </div>
                    </div>
                    <div class="daw-recruitment__device-item">
                        <span>🎙️</span>
                        <div>
                            <strong>Mikrofon</strong>
                            <p>Harus bekerja agar instruksi audio terdengar dengan jelas.</p>
                        </div>
                    </div>
                    <div class="daw-recruitment__device-item">
                        <span>🔊</span>
                        <div>
                            <strong>Speaker</strong>
                            <p>Pastikan audio keluar dengan volume yang cukup.</p>
                        </div>
                    </div>
                    <div class="daw-recruitment__device-item">
                        <span>🌐</span>
                        <div>
                            <strong>Koneksi Internet</strong>
                            <p>Gunakan jaringan yang stabil dan cepat.</p>
                        </div>
                    </div>
                </div>
            </section>

            <div class="daw-recruitment__psychotest-footer dawn-recruitment__psychotest-footer--split">
                <a class="daw-recruitment__button daw-recruitment__button--ghost" href="<?= esc_url( $back_url ) ?>">Sebelumnya</a>
                <a class="daw-recruitment__button daw-recruitment__button--full" href="<?= esc_url( $device_url ) ?>">Lanjut ke Pemeriksaan Perangkat</a>
            </div>
        </div>
    </main>
    <?php require recruitment_get_plugin_path( 'public/components/footer.php' ); ?>
</div>
