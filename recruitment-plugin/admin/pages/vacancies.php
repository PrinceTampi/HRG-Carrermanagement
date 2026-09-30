<?php
$user = function_exists( 'wp_get_current_user' ) ? wp_get_current_user() : null;
$display_name = $user && ! empty( $user->display_name ) ? $user->display_name : 'Admin HR';
$user_email = $user && ! empty( $user->user_email ) ? $user->user_email : 'hr@daw.co.id';
$logout_url = function_exists( 'wp_logout_url' ) ? wp_logout_url( home_url( '/' ) ) : '?page=login';
$vacancies = [
    [ 'title' => 'Sales Consultant', 'dealer' => 'DAW Bitung', 'region' => 'Sulawesi Utara', 'type' => 'Full Time', 'deadline' => '30 September 2026', 'posted' => '1 Agustus 2026', 'status' => 'closed' ],
    [ 'title' => 'Service Advisor', 'dealer' => 'DAW Main Dealer Maumbi', 'region' => 'Sulawesi Utara', 'type' => 'Full Time', 'deadline' => '15 September 2026', 'posted' => '5 Agustus 2026', 'status' => 'published' ],
    [ 'title' => 'Staff Administrasi', 'dealer' => 'DAW Bitung', 'region' => 'Sulawesi Utara', 'type' => 'Full Time', 'deadline' => '20 September 2026', 'posted' => '10 Agustus 2026', 'status' => 'closed' ],
    [ 'title' => 'Marketing Coordinator', 'dealer' => 'DAW Main Dealer Maumbi', 'region' => 'Sulawesi Utara', 'type' => 'Full Time', 'deadline' => '25 September 2026', 'posted' => '8 Agustus 2026', 'status' => 'published' ],
    [ 'title' => 'Finance & Accounting Staff', 'dealer' => 'DAW Bitung', 'region' => 'Sulawesi Utara', 'type' => 'Full Time', 'deadline' => '30 September 2026', 'posted' => '12 Agustus 2026', 'status' => 'published' ],
    [ 'title' => 'Mekanik / Teknisi Motor', 'dealer' => 'DAW Main Dealer Maumbi', 'region' => 'Sulawesi Utara', 'type' => 'Full Time', 'deadline' => '10 Oktober 2026', 'posted' => '15 Agustus 2026', 'status' => 'published' ],
];
$status_labels = [ 'published' => 'Dipublikasikan', 'draft' => 'Draft', 'closed' => 'Ditutup' ];
?>
<div class="daw-vacancies">
    <aside class="daw-vacancies__sidebar">
        <a class="daw-vacancies__brand" href="<?= esc_url( recruitment_get_admin_url( 'vacancies' ) ) ?>"><span>DAW</span><strong>Admin</strong></a>
        <nav aria-label="Navigasi admin">
            <span class="daw-vacancies__nav-item">Dashboard</span>
            <span class="daw-vacancies__nav-heading">Recruitment</span>
            <span class="daw-vacancies__nav-item">Semua Kandidat Aktif <b>12</b></span>
            <span class="daw-vacancies__nav-item">Seleksi Administrasi</span>
            <span class="daw-vacancies__nav-item">Psikotes</span>
            <span class="daw-vacancies__nav-item">Wawancara HR</span>
            <span class="daw-vacancies__nav-item">Wawancara User</span>
            <span class="daw-vacancies__nav-item">Final Decision</span>
            <span class="daw-vacancies__nav-item">Database Pelamar</span>
            <span class="daw-vacancies__nav-item is-active">Lowongan</span>
            <span class="daw-vacancies__nav-item">Form Lamaran</span>
            <span class="daw-vacancies__nav-item">Psikotes</span>
            <span class="daw-vacancies__nav-item">Jadwal Wawancara</span>
            <span class="daw-vacancies__nav-item">Email Recruitment</span>
            <span class="daw-vacancies__nav-item">Approval Pengajuan</span>
            <span class="daw-vacancies__nav-item">Akun User Dept</span>
        </nav>
        <span class="daw-vacancies__collapse">‹‹ Ciutkan</span>
    </aside>

    <div class="daw-vacancies__workspace">
        <header class="daw-vacancies__header">
            <nav aria-label="Breadcrumb"><span>DAW Admin</span><i aria-hidden="true">/</i><strong>Lowongan</strong></nav>
            <div class="daw-vacancies__account">
                <span class="daw-vacancies__avatar" aria-hidden="true">HR</span>
                <span><strong><?= esc_html( $display_name ) ?></strong><small><?= esc_html( $user_email ) ?></small></span>
                <a href="<?= esc_url( $logout_url ) ?>">Keluar</a>
            </div>
        </header>

        <main class="daw-vacancies__main">
            <div class="daw-vacancies__overview">
                <dl class="daw-vacancies__stat"><div><dd data-vacancy-count="total">6</dd><dt>Total Lowongan</dt></div></dl>
                <dl class="daw-vacancies__stat"><div><dd data-vacancy-count="published">4</dd><dt>Dipublikasikan</dt></div></dl>
                <dl class="daw-vacancies__stat"><div><dd data-vacancy-count="draft">1</dd><dt>Draft</dt></div></dl>
                <dl class="daw-vacancies__stat"><div><dd data-vacancy-count="closed">2</dd><dt>Ditutup</dt></div></dl>
                <button class="daw-vacancies__create" type="button" data-open-vacancy-dialog><span aria-hidden="true">+</span> Buat Lowongan</button>
            </div>
            <p class="daw-vacancies__feedback" role="status" aria-live="polite" data-vacancy-feedback hidden></p>
            <div class="daw-vacancies__table-wrap">
                <table class="daw-vacancies__table">
                    <caption class="screen-reader-text">Daftar lowongan</caption>
                    <thead><tr><th scope="col">Judul Jabatan</th><th scope="col">Dealer</th><th scope="col">Wilayah</th><th scope="col">Tipe</th><th scope="col">Batas Lamaran</th><th scope="col">Status</th><th scope="col">Aksi</th></tr></thead>
                    <tbody data-vacancy-list>
                        <?php foreach ( $vacancies as $index => $vacancy ) : ?>
                            <tr data-vacancy-row data-vacancy-id="<?= esc_attr( (string) ( $index + 1 ) ) ?>" data-status="<?= esc_attr( $vacancy['status'] ) ?>">
                                <td><strong data-field="title"><?= esc_html( $vacancy['title'] ) ?></strong><small>Diposting: <?= esc_html( $vacancy['posted'] ) ?></small></td>
                                <td data-field="dealer"><?= esc_html( $vacancy['dealer'] ) ?></td><td data-field="region"><?= esc_html( $vacancy['region'] ) ?></td><td data-field="type"><?= esc_html( $vacancy['type'] ) ?></td><td data-field="deadline"><?= esc_html( $vacancy['deadline'] ) ?></td>
                                <td><span class="daw-vacancies__badge daw-vacancies__badge--<?= esc_attr( $vacancy['status'] ) ?>" data-vacancy-status><?= esc_html( $status_labels[ $vacancy['status'] ] ) ?></span></td>
                                <td class="daw-vacancies__actions"><button type="button" data-vacancy-edit>Edit</button><button type="button" data-vacancy-toggle><?= 'published' === $vacancy['status'] ? 'Tutup' : 'Publish' ?></button><button type="button" data-vacancy-archive>Arsip</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <dialog class="daw-vacancies__dialog" data-vacancy-dialog aria-labelledby="vacancy-dialog-title">
        <form data-vacancy-form>
            <div class="daw-vacancies__dialog-heading"><h2 id="vacancy-dialog-title">Buat Lowongan</h2><button type="button" aria-label="Tutup" data-close-vacancy-dialog>&times;</button></div>
            <label>Judul Jabatan<input name="title" required></label>
            <div class="daw-vacancies__dialog-row"><label>Dealer<input name="dealer" required></label><label>Wilayah<input name="region" value="Sulawesi Utara" required></label></div>
            <div class="daw-vacancies__dialog-row"><label>Tipe Pekerjaan<select name="type"><option>Full Time</option><option>Contract</option><option>Internship</option></select></label><label>Batas Lamaran<input name="deadline" type="date" required></label></div>
            <div class="daw-vacancies__dialog-actions"><button type="button" data-close-vacancy-dialog>Batal</button><button type="submit">Simpan sebagai Draft</button></div>
        </form>
    </dialog>
</div>
<?php if ( defined( 'RECRUITMENT_SANDBOX' ) ) : ?>
    <?php require recruitment_get_plugin_path( 'public/components/screen-explorer.php' ); ?>
<?php endif; ?>
