<?php
$search = trim( (string) ( $_GET['search'] ?? '' ) );
$batch_filter = sanitize_text_field( (string) ( $_GET['batch'] ?? '' ) );
$position_filter = sanitize_text_field( (string) ( $_GET['position'] ?? '' ) );
$dealer_filter = sanitize_text_field( (string) ( $_GET['dealer'] ?? '' ) );

$candidates = [
    [ 'name' => 'Rafi Kurniawan', 'code' => 'DAW-2026-001', 'position' => 'Sales Consultant', 'dealer' => 'DAW Bandung', 'batch' => '2026-01', 'source' => 'Website Resmi DAW', 'stage' => 'Diterima', 'status' => 'Diterima', 'applied_on' => '2026-01-15', 'decision_date' => '2026-03-10' ],
    [ 'name' => 'Sari Dewi Lestari', 'code' => 'DAW-2026-002', 'position' => 'Staff Administrasi', 'dealer' => 'DAW Jakarta Selatan', 'batch' => '2026-01', 'source' => 'Instagram', 'stage' => 'Final Decision', 'status' => 'Diterima', 'applied_on' => '2026-01-21', 'decision_date' => '2026-03-05' ],
    [ 'name' => 'Budi Santoso', 'code' => 'DAW-2026-003', 'position' => 'Sales Executive', 'dealer' => 'DAW Airmadidi', 'batch' => '2026-02', 'source' => 'Job Portal', 'stage' => 'Tidak Lolos', 'status' => 'Tidak Lolos', 'applied_on' => '2026-02-03', 'decision_date' => '2026-02-28' ],
    [ 'name' => 'Anisa Putri Rahma', 'code' => 'DAW-2026-004', 'position' => 'Marketing Coordinator', 'dealer' => 'DAW Medan', 'batch' => '2026-02', 'source' => 'LinkedIn', 'stage' => 'Ditinjau Kembali', 'status' => 'Ditinjau Kembali', 'applied_on' => '2026-02-12', 'decision_date' => '' ],
    [ 'name' => 'Kevin Alexander', 'code' => 'DAW-2026-005', 'position' => 'Sales Executive', 'dealer' => 'DAW Airmadidi', 'batch' => '2026-02', 'source' => 'Rekomendasi Karyawan', 'stage' => 'Menunggu Keputusan', 'status' => 'Dalam Proses', 'applied_on' => '2026-02-18', 'decision_date' => '' ],
    [ 'name' => 'Maria Natalia', 'code' => 'DAW-2026-006', 'position' => 'Finance & Accounting Staff', 'dealer' => 'DAW Manado', 'batch' => '2026-03', 'source' => 'Website Resmi DAW', 'stage' => 'Diterima', 'status' => 'Diterima', 'applied_on' => '2026-03-02', 'decision_date' => '2026-03-18' ],
];

$positions = [];
$dealers = [];
$batch_list = [];
foreach ( $candidates as $candidate ) {
    $positions[ strtolower( $candidate['position'] ) ] = $candidate['position'];
    $dealers[ strtolower( $candidate['dealer'] ) ] = $candidate['dealer'];
    $batch_list[ strtolower( $candidate['batch'] ) ] = $candidate['batch'];
}
ksort( $positions );
ksort( $dealers );
ksort( $batch_list );

$filtered_candidates = [];
foreach ( $candidates as $candidate ) {
    $matches_search = '' === $search || false !== strpos( strtolower( $candidate['name'] . ' ' . $candidate['code'] . ' ' . $candidate['position'] . ' ' . $candidate['dealer'] ), strtolower( $search ) );
    $matches_batch = '' === $batch_filter || strtolower( $candidate['batch'] ) === strtolower( $batch_filter );
    $matches_position = '' === $position_filter || strtolower( $candidate['position'] ) === strtolower( $position_filter );
    $matches_dealer = '' === $dealer_filter || strtolower( $candidate['dealer'] ) === strtolower( $dealer_filter );
    if ( $matches_search && $matches_batch && $matches_position && $matches_dealer ) {
        $filtered_candidates[] = $candidate;
    }
}

$stats = [
    'total' => count( $filtered_candidates ),
    'accepted' => 0,
    'rejected' => 0,
    'waiting' => 0,
    'administrasi' => 0,
    'psikotes' => 0,
    'hr' => 0,
    'user' => 0,
];
foreach ( $filtered_candidates as $candidate ) {
    switch ( strtolower( $candidate['status'] ) ) {
        case 'diterima':
            $stats['accepted']++;
            break;
        case 'tidak lolos':
            $stats['rejected']++;
            break;
        case 'dalam proses':
        case 'menunggu keputusan':
        case 'ditinjau kembali':
            $stats['waiting']++;
            break;
    }
    switch ( strtolower( $candidate['stage'] ) ) {
        case 'seleksi administrasi':
            $stats['administrasi']++;
            break;
        case 'psikotes':
            $stats['psikotes']++;
            break;
        case 'wawancara hr':
            $stats['hr']++;
            break;
        case 'wawancara user':
            $stats['user']++;
            break;
    }
}
$pipeline = [
    [ 'label' => 'Lamaran Diterima', 'value' => $stats['total'] ],
    [ 'label' => 'Seleksi Administrasi', 'value' => max( 0, $stats['administrasi'] ) ],
    [ 'label' => 'Psikotes', 'value' => max( 0, $stats['psikotes'] ) ],
    [ 'label' => 'Wawancara HR', 'value' => max( 0, $stats['hr'] ) ],
    [ 'label' => 'Wawancara User', 'value' => max( 0, $stats['user'] ) ],
    [ 'label' => 'Diterima', 'value' => $stats['accepted'] ],
];
?>
<div class="wrap recruitment-admin daw-analytics-page">
    <header class="daw-feature-header">
        <div>
            <p class="daw-feature-header__breadcrumb">DAW Admin / Recruitment Analytics</p>
            <h1>Recruitment Analytics</h1>
        </div>
        <div class="daw-feature-header__actions">
            <button type="button" class="button button-secondary">Export CSV</button>
        </div>
    </header>

    <form method="get" class="daw-filter-bar" action="<?= esc_url( admin_url( 'admin.php' ) ) ?>">
        <input type="hidden" name="page" value="recruitment-analytics" />
        <div class="daw-filter-bar__controls">
            <label class="daw-filter-bar__search">
                <span class="dashicons dashicons-search" aria-hidden="true"></span>
                <input type="search" name="search" value="<?= esc_attr( $search ) ?>" placeholder="Cari kandidat atau kode..." />
            </label>
            <select name="batch">
                <option value="">Semua Batch</option>
                <?php foreach ( $batch_list as $value ) : ?>
                    <option value="<?= esc_attr( $value ) ?>" <?= selected( $batch_filter, $value, false ) ?>><?= esc_html( $value ) ?></option>
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
            <button type="submit" class="button button-primary">Terapkan</button>
            <a href="<?= esc_url( admin_url( 'admin.php?page=recruitment-analytics' ) ) ?>" class="button button-secondary">Reset</a>
        </div>
    </form>

    <section class="daw-feature-stats" aria-label="Ringkasan analitik recruitment">
        <article class="daw-feature-stat daw-feature-stat--dark">
            <span>Total Pelamar</span>
            <strong><?= esc_html( number_format_i18n( $stats['total'] ) ) ?></strong>
        </article>
        <article class="daw-feature-stat daw-feature-stat--blue">
            <span>Lolos Administrasi</span>
            <strong><?= esc_html( number_format_i18n( $stats['administrasi'] ) ) ?></strong>
        </article>
        <article class="daw-feature-stat daw-feature-stat--green">
            <span>Lolos Psikotes</span>
            <strong><?= esc_html( number_format_i18n( $stats['psikotes'] ) ) ?></strong>
        </article>
        <article class="daw-feature-stat daw-feature-stat--orange">
            <span>Wawancara HR</span>
            <strong><?= esc_html( number_format_i18n( $stats['hr'] ) ) ?></strong>
        </article>
    </section>

    <section class="daw-analytics__grid">
        <div class="daw-table-card">
            <h2>Recruitment Pipeline</h2>
            <div class="daw-pipeline">
                <?php foreach ( $pipeline as $step ) : ?>
                    <?php $width = $stats['total'] > 0 ? min( 100, ( $step['value'] / max( 1, $stats['total'] ) ) * 100 ) : 0; ?>
                    <div class="daw-pipeline__row">
                        <div class="daw-pipeline__label"><span><?= esc_html( $step['label'] ) ?></span><strong><?= esc_html( number_format_i18n( $step['value'] ) ) ?></strong></div>
                        <div class="daw-pipeline__track"><span style="width: <?= esc_attr( (string) round( $width ) ) ?>%"></span></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="daw-table-card">
            <h2>Sumber Informasi Lowongan</h2>
            <ul class="daw-analytics__list">
                <li><span>Website Resmi DAW</span><strong>2</strong></li>
                <li><span>Instagram</span><strong>1</strong></li>
                <li><span>LinkedIn</span><strong>1</strong></li>
                <li><span>Job Portal</span><strong>1</strong></li>
                <li><span>Rekomendasi Karyawan</span><strong>1</strong></li>
            </ul>
        </div>
    </section>

    <section class="daw-table-card">
        <div class="daw-table-card__top">
            <strong><?= esc_html( number_format_i18n( count( $filtered_candidates ) ) ) ?> data analitik</strong>
        </div>
        <div class="daw-table-wrap">
            <table class="daw-data-table">
                <thead>
                    <tr>
                        <th>Kode Lamaran</th>
                        <th>Nama</th>
                        <th>Posisi</th>
                        <th>Dealer</th>
                        <th>Sumber</th>
                        <th>Tahap</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $filtered_candidates as $candidate ) : ?>
                        <tr>
                            <td><?= esc_html( $candidate['code'] ) ?></td>
                            <td><?= esc_html( $candidate['name'] ) ?></td>
                            <td><?= esc_html( $candidate['position'] ) ?></td>
                            <td><?= esc_html( $candidate['dealer'] ) ?></td>
                            <td><?= esc_html( $candidate['source'] ) ?></td>
                            <td><?= esc_html( $candidate['stage'] ) ?></td>
                            <td><span class="daw-status-badge daw-status-badge--<?= strtolower( str_replace( ' ', '-', $candidate['status'] ) ) ?>"><?= esc_html( $candidate['status'] ) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
