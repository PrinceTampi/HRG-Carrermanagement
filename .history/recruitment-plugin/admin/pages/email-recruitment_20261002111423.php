<?php
$search = trim( (string) ( $_GET['search'] ?? '' ) );
$stage_filter = sanitize_text_field( (string) ( $_GET['stage'] ?? '' ) );
$status_filter = sanitize_text_field( (string) ( $_GET['status'] ?? '' ) );
$position_filter = sanitize_text_field( (string) ( $_GET['position'] ?? '' ) );
$dealer_filter = sanitize_text_field( (string) ( $_GET['dealer'] ?? '' ) );

$candidates = [
    [ 'id' => 1, 'name' => 'Rafi Kurniawan', 'code' => 'DAW-2026-001', 'position' => 'Sales Consultant', 'dealer' => 'DAW Bandung', 'stage' => 'Wawancara User', 'status' => 'Dalam Proses', 'email_status' => 'Belum Dikirim' ],
    [ 'id' => 2, 'name' => 'Sari Dewi Lestari', 'code' => 'DAW-2026-002', 'position' => 'Staff Administrasi', 'dealer' => 'DAW Jakarta Selatan', 'stage' => 'Final Decision', 'status' => 'Diterima', 'email_status' => 'Terkirim' ],
    [ 'id' => 3, 'name' => 'Budi Santoso', 'code' => 'DAW-2026-003', 'position' => 'Sales Executive', 'dealer' => 'DAW Airmadidi', 'stage' => 'Psikotes', 'status' => 'Dalam Proses', 'email_status' => 'Belum Dikirim' ],
    [ 'id' => 4, 'name' => 'Anisa Putri Rahma', 'code' => 'DAW-2026-004', 'position' => 'Marketing Coordinator', 'dealer' => 'DAW Medan', 'stage' => 'Wawancara HR', 'status' => 'Dalam Proses', 'email_status' => 'Belum Dibaca' ],
    [ 'id' => 5, 'name' => 'Kevin Alexander', 'code' => 'DAW-2026-005', 'position' => 'Sales Executive', 'dealer' => 'DAW Airmadidi', 'stage' => 'Final Decision', 'status' => 'Menunggu Keputusan', 'email_status' => 'Belum Dikirim' ],
    [ 'id' => 6, 'name' => 'Maria Natalia', 'code' => 'DAW-2026-006', 'position' => 'Finance & Accounting Staff', 'dealer' => 'DAW Manado', 'stage' => 'Final Decision', 'status' => 'Diterima', 'email_status' => 'Terkirim' ],
];

$stages = [ 'Lamaran Diterima', 'Seleksi Administrasi', 'Psikotes', 'Wawancara HR', 'Wawancara User', 'Final Decision' ];
$positions = [];
$dealers = [];
foreach ( $candidates as $candidate ) {
    $positions[ strtolower( $candidate['position'] ) ] = $candidate['position'];
    $dealers[ strtolower( $candidate['dealer'] ) ] = $candidate['dealer'];
}
ksort( $positions );
ksort( $dealers );

$filtered_candidates = [];
foreach ( $candidates as $candidate ) {
    $matches_search = '' === $search || false !== strpos( strtolower( $candidate['name'] . ' ' . $candidate['code'] . ' ' . $candidate['position'] ), strtolower( $search ) );
    $matches_stage = '' === $stage_filter || strtolower( $candidate['stage'] ) === strtolower( $stage_filter );
    $matches_status = '' === $status_filter || strtolower( $candidate['status'] ) === strtolower( $status_filter );
    $matches_position = '' === $position_filter || strtolower( $candidate['position'] ) === strtolower( $position_filter );
    $matches_dealer = '' === $dealer_filter || strtolower( $candidate['dealer'] ) === strtolower( $dealer_filter );

    if ( $matches_search && $matches_stage && $matches_status && $matches_position && $matches_dealer ) {
        $filtered_candidates[] = $candidate;
    }
}

$templates = [
    [ 'name' => 'Undangan Wawancara User', 'category' => 'Wawancara User', 'subject' => 'Undangan Wawancara User - {{nama_pelamar}}' ],
    [ 'name' => 'Hasil Akhir Diterima', 'category' => 'Final Decision', 'subject' => 'Hasil Akhir Rekrutmen - {{nama_pelamar}}' ],
    [ 'name' => 'Hasil Akhir Tidak Lolos', 'category' => 'Final Decision', 'subject' => 'Status Rekrutmen - {{nama_pelamar}}' ],
];
?>
<div class="wrap recruitment-admin daw-email-page">
    <header class="daw-feature-header">
        <div>
            <p class="daw-feature-header__breadcrumb">DAW Admin / Email Recruitment</p>
            <h1>Email Recruitment</h1>
        </div>
        <div class="daw-feature-header__actions">
            <button type="button" class="button button-secondary">Preview</button>
            <button type="button" class="button button-primary">Kirim Email</button>
        </div>
    </header>

    <form method="get" class="daw-filter-bar" action="<?= esc_url( admin_url( 'admin.php' ) ) ?>">
        <input type="hidden" name="page" value="recruitment-email-recruitment" />
        <div class="daw-filter-bar__controls">
            <label class="daw-filter-bar__search">
                <span class="dashicons dashicons-search" aria-hidden="true"></span>
                <input type="search" name="search" value="<?= esc_attr( $search ) ?>" placeholder="Cari nama atau kode..." />
            </label>
            <select name="stage">
                <option value="">Semua Tahap</option>
                <?php foreach ( $stages as $stage ) : ?>
                    <option value="<?= esc_attr( strtolower( $stage ) ) ?>" <?= selected( strtolower( $stage_filter ), strtolower( $stage ), false ) ?>><?= esc_html( $stage ) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="position">
                <option value="">Semua Posisi</option>
                <?php foreach ( $positions as $value ) : ?>
                    <option value="<?= esc_attr( $value ) ?>" <?= selected( $position_filter, $value, false ) ?>><?= esc_html( $value ) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="dealer">
                <option value="">Semua Dealer</option>
                <?php foreach ( $dealers as $value ) : ?>
                    <option value="<?= esc_attr( $value ) ?>" <?= selected( $dealer_filter, $value, false ) ?>><?= esc_html( $value ) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="status">
                <option value="">Semua Status</option>
                <option value="dalam proses" <?= selected( strtolower( $status_filter ), 'dalam proses', false ) ?>>Dalam Proses</option>
                <option value="diterima" <?= selected( strtolower( $status_filter ), 'diterima', false ) ?>>Diterima</option>
                <option value="menunggu keputusan" <?= selected( strtolower( $status_filter ), 'menunggu keputusan', false ) ?>>Menunggu Keputusan</option>
            </select>
            <button type="submit" class="button button-primary">Terapkan</button>
            <a href="<?= esc_url( admin_url( 'admin.php?page=recruitment-email-recruitment' ) ) ?>" class="button button-secondary">Reset</a>
        </div>
    </form>

    <section class="daw-email-layout">
        <div class="daw-email-layout__list">
            <div class="daw-table-card">
                <div class="daw-table-card__top">
                    <strong><?= esc_html( number_format_i18n( count( $filtered_candidates ) ) ) ?> penerima</strong>
                    <label><input type="checkbox" /> Pilih Semua</label>
                </div>
                <div class="daw-table-wrap">
                    <table class="daw-data-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Nama</th>
                                <th>Posisi</th>
                                <th>Dealer</th>
                                <th>Tahap</th>
                                <th>Status Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $filtered_candidates as $candidate ) : ?>
                                <tr>
                                    <td><input type="checkbox" checked /></td>
                                    <td><strong><?= esc_html( $candidate['name'] ) ?></strong><br /><small><?= esc_html( $candidate['code'] ) ?></small></td>
                                    <td><?= esc_html( $candidate['position'] ) ?></td>
                                    <td><?= esc_html( $candidate['dealer'] ) ?></td>
                                    <td><?= esc_html( $candidate['stage'] ) ?></td>
                                    <td><span class="daw-status-badge daw-status-badge--mail"><?= esc_html( $candidate['email_status'] ) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="daw-email-layout__composer">
            <div class="daw-email-template-list">
                <?php foreach ( $templates as $template ) : ?>
                    <button type="button" class="daw-email-template-item">
                        <strong><?= esc_html( $template['name'] ) ?></strong>
                        <small><?= esc_html( $template['category'] ) ?></small>
                    </button>
                <?php endforeach; ?>
            </div>

            <form class="daw-email-form" method="post">
                <div class="daw-email-form__row">
                    <label>
                        Kepada
                        <input type="text" value="Rafi Kurniawan &lt;rafi.kurniawan@example.com&gt;" />
                    </label>
                    <label>
                        CC
                        <input type="text" value="hr@daw.co.id" />
                    </label>
                </div>
                <label>
                    Subjek
                    <input type="text" value="Undangan Wawancara User - {{nama_pelamar}}" />
                </label>
                <label>
                    Isi Pesan
                    <textarea rows="10">Halo {{nama_pelamar}},\n\nTerima kasih atas minat Anda terhadap posisi {{posisi}} di DAW.\nKami ingin mengundang Anda untuk mengikuti tahap wawancara user pada {{tanggal}} pukul {{waktu}}.\n\nSalam hangat,\nTim Rekrutmen DAW</textarea>
                </label>
                <div class="daw-email-form__actions">
                    <button type="button" class="button button-secondary">Preview</button>
                    <button type="button" class="button button-secondary">Simpan Draft</button>
                    <button type="submit" class="button button-primary">Kirim Email</button>
                </div>
            </form>
        </div>
    </section>
</div>
