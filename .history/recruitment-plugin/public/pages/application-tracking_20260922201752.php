<?php
$token = sanitize_text_field( wp_unslash( $_GET['token'] ?? '' ) );
$email = sanitize_email( wp_unslash( $_GET['email'] ?? '' ) );
$has_lookup = '' !== $token && is_email( $email );
$application_code = $token ?: 'DAW-2026-001245';
$registered_date = '5 Agustus 2026';
$psychology_method = 'offline';
?>
<div class="daw-recruitment daw-recruitment--tracking">
    <?php require recruitment_get_plugin_path( 'components/public/header.php' ); ?>
    <main class="daw-recruitment__tracking-main">
        <div class="daw-recruitment__tracking-container">
            <?php if ( ! $has_lookup ) : ?>
                <section class="daw-recruitment__panel daw-recruitment__tracking-empty">
                    <span class="daw-recruitment__eyebrow">Tracking Lamaran</span>
                    <h1>Data lamaran belum lengkap</h1>
                    <p>Gunakan kode lamaran dan email yang kamu gunakan saat mendaftar untuk melihat status.</p>
                    <a class="daw-recruitment__button" href="<?= esc_url( recruitment_get_public_url( 'tracking' ) ) ?>">Cek Status Lamaran</a>
                </section>
            <?php else : ?>
                <section class="daw-recruitment__tracking-card daw-recruitment__tracking-summary">
                    <div class="daw-recruitment__tracking-summary-top"><div><span class="daw-recruitment__tracking-label">Status Lamaran</span><h1>Pelamar DAW</h1><div class="daw-recruitment__tracking-tags"><span>Sales Consultant</span><span>DAW Bitung</span></div></div><span class="daw-recruitment__tracking-status">Tes Psikologi</span></div>
                    <div class="daw-recruitment__tracking-summary-meta"><div><span>Kode Lamaran</span><strong><?= esc_html( $application_code ) ?></strong></div><div><span>Tanggal Daftar</span><strong><?= esc_html( $registered_date ) ?></strong></div></div>
                </section>
                <section class="daw-recruitment__tracking-card daw-recruitment__tracking-current">
                    <div class="daw-recruitment__tracking-current-head"><div><span class="daw-recruitment__tracking-label">Tahap Saat Ini</span><h2><span aria-hidden="true">●</span> Tes Psikologi</h2></div><span class="daw-recruitment__tracking-running">Sedang berlangsung</span></div>
                    <?php if ( 'offline' === $psychology_method ) : ?>
                        <div class="psychology-offline">
                            <p class="psychology-offline__lead">Psikotes Anda akan dilaksanakan secara langsung (offline) di lokasi yang telah ditentukan.</p>
                            <div class="psychology-offline__card">
                                <h3 class="psychology-offline__header"><span aria-hidden="true">&#128205;</span> Psikotes Dilaksanakan Secara Offline</h3>
                                <p class="psychology-offline__description">Anda dijadwalkan untuk mengikuti psikotes secara langsung di lokasi yang telah ditentukan.</p>
                                <dl class="psychology-offline__info">
                                    <div><dt>Tanggal</dt><dd>21 September 2026</dd></div>
                                    <div><dt>Waktu</dt><dd>08:00 &ndash; 11:00 WITA</dd></div>
                                    <div><dt>Lokasi</dt><dd>PT Daya Adicipta Wisesa</dd></div>
                                    <div><dt>Alamat</dt><dd>Jl. Airmadidi, Sulawesi Utara</dd></div>
                                    <div><dt>Dress Code</dt><dd>Formal / Smart Casual</dd></div>
                                    <div><dt>Kontak</dt><dd>HR Recruitment DAW</dd></div>
                                </dl>
                                <div class="psychology-offline__requirements">
                                    <h4>Yang Perlu Dibawa:</h4>
                                    <ul>
                                        <li>KTP (wajib)</li>
                                        <li>Alat tulis (pensil, bolpoin)</li>
                                        <li>Dokumen pendukung lainnya</li>
                                    </ul>
                                </div>
                                <button class="daw-recruitment__button psychology-offline__button" type="button">Konfirmasi Kehadiran</button>
                            </div>
                        </div>
                    <?php else : ?>
                        <p>Tes psikologi Anda sudah tersedia. Silakan selesaikan sebelum batas waktu yang ditentukan.</p>
                        <span class="daw-recruitment__tracking-notice">◷ Durasi: &plusmn;60 menit. Siapkan kamera dan koneksi stabil.</span>
                        <div class="daw-recruitment__tracking-availability"><strong>Tes Psikologi Anda Sudah Tersedia</strong><span>Tes dapat dilakukan mulai: Senin, 25 Agustus 2026 &mdash; 08:00 WITA</span></div>
                        <button class="daw-recruitment__button" type="button">Mulai Tes &rarr;</button>
                    <?php endif; ?>
                </section>
                <section class="daw-recruitment__tracking-card daw-recruitment__tracking-next"><span class="daw-recruitment__tracking-next-icon" aria-hidden="true">▥</span><div><span class="daw-recruitment__tracking-label">Tahap Selanjutnya</span><h2>Wawancara HR</h2><p>Jadwal dan informasi selanjutnya akan disampaikan oleh tim HR setelah hasil tes divalidasi.</p></div></section>
                <section class="daw-recruitment__tracking-card daw-recruitment__tracking-history"><h2>Riwayat Proses</h2><div class="daw-recruitment__tracking-step is-done"><span>✓</span><div><strong>Lamaran Dikirim</strong><small>Selesai &mdash; 5 Agustus 2026</small></div><b>Selesai</b></div><div class="daw-recruitment__tracking-step is-done"><span>✓</span><div><strong>Seleksi Administrasi</strong><small>Selesai &mdash; 10 Agustus 2026</small></div><b>Selesai</b></div><div class="daw-recruitment__tracking-step is-current"><span>!</span><div><strong>Tes Psikologi</strong><small><?= 'offline' === $psychology_method ? 'Terjadwal' : 'Sedang berlangsung' ?></small></div><b><?= 'offline' === $psychology_method ? 'Terjadwal' : 'Sedang berlangsung' ?></b></div><div class="daw-recruitment__tracking-divider"></div><span class="daw-recruitment__tracking-label">Tahapan Berikutnya</span><div class="daw-recruitment__tracking-step is-pending"><span>○</span><div><strong>Wawancara HR</strong></div><b>Menunggu</b></div><div class="daw-recruitment__tracking-step is-pending"><span>○</span><div><strong>Wawancara User</strong></div><b>Menunggu</b></div><div class="daw-recruitment__tracking-step is-pending"><span>○</span><div><strong>Keputusan Akhir</strong></div><b>Menunggu</b></div></section>
                <p class="daw-recruitment__tracking-note">ⓘ Urutan tahapan proses rekrutmen dapat disesuaikan oleh tim HR sesuai kebutuhan posisi dan proses seleksi.</p>
                <div class="daw-recruitment__tracking-footer"><span>◷ Update terakhir: <strong>25 Agustus 2026</strong><small>Admin telah memperbarui status lamaran Anda.</small></span><a href="<?= esc_url( recruitment_get_public_url( 'careers' ) ) ?>">Lihat Lowongan Lain</a></div>
            <?php endif; ?>
            <a class="daw-recruitment__tracking-back" href="<?= esc_url( recruitment_get_public_url( 'careers' ) ) ?>">&larr; Kembali ke Beranda Karier</a>
        </div>
    </main>
    <?php require recruitment_get_plugin_path( 'components/public/footer.php' ); ?>
</div>
