<?php
$user = function_exists( 'wp_get_current_user' ) ? wp_get_current_user() : null;
$display_name = $user && ! empty( $user->display_name ) ? $user->display_name : 'Admin HR';
$search = trim( (string) ( $_GET['search'] ?? '' ) );
$position_filter = sanitize_text_field( (string) ( $_GET['position'] ?? '' ) );
$dealer_filter = sanitize_text_field( (string) ( $_GET['dealer'] ?? '' ) );
$status_filter = sanitize_text_field( (string) ( $_GET['status'] ?? '' ) );
$selected_candidate_id = absint( $_GET['candidate_id'] ?? 0 );

$candidates = [
    [
        'id' => 101,
        'name' => 'Rafi Kurniawan',
        'code' => 'DAW-2026-001',
        'position' => 'Sales Consultant',
        'department' => 'Sales',
        'dealer' => 'DAW Bandung',
        'applied_on' => '2026-01-15',
        'psychotes' => 'Lulus',
        'hr_interview' => 'Direkomendasikan',
        'user_interview' => 'Direkomendasikan',
        'last_interview' => '2026-02-18',
        'status' => 'Menunggu Keputusan',
    ],
    [
        'id' => 102,
        'name' => 'Sari Dewi Lestari',
        'code' => 'DAW-2026-002',
        'position' => 'Staff Administrasi',
        'department' => 'Admin',
        'dealer' => 'DAW Jakarta Selatan',
        'applied_on' => '2026-01-21',
        'psychotes' => 'Lulus',
        'hr_interview' => 'Cukup',
        'user_interview' => 'Direkomendasikan',
        'last_interview' => '2026-02-03',
        'status' => 'Diterima',
    ],
    [
        'id' => 103,
        'name' => 'Budi Santoso',
        'code' => 'DAW-2026-003',
        'position' => 'Sales Executive',
        'department' => 'Sales',
        'dealer' => 'DAW Airmadidi',
        'applied_on' => '2026-02-03',
        'psychotes' => 'Tidak Lulus',
        'hr_interview' => 'Tidak Direkomendasikan',
        'user_interview' => 'Tidak Direkomendasikan',
        'last_interview' => '2026-02-11',
        'status' => 'Tidak Lolos',
    ],
    [
        'id' => 104,
        'name' => 'Anisa Putri Rahma',
        'code' => 'DAW-2026-004',
        'position' => 'Marketing Coordinator',
        'department' => 'Marketing',
        'dealer' => 'DAW Medan',
        'applied_on' => '2026-02-12',
        'psychotes' => 'Lulus',
        'hr_interview' => 'Direkomendasikan',
        'user_interview' => 'Menunggu Review',
        'last_interview' => '2026-02-19',
        'status' => 'Ditinjau Kembali',
    ],
    [
        'id' => 105,
        'name' => 'Kevin Alexander',
        'code' => 'DAW-2026-005',
        'position' => 'Sales Executive',
        'department' => 'Sales',
        'dealer' => 'DAW Airmadidi',
        'applied_on' => '2026-02-18',
        'psychotes' => 'Lulus',
        'hr_interview' => 'Direkomendasikan',
        'user_interview' => 'Direkomendasikan',
        'last_interview' => '2026-03-01',
        'status' => 'Menunggu Keputusan',
    ],
    [
        'id' => 106,
        'name' => 'Maria Natalia',
        'code' => 'DAW-2026-006',
        'position' => 'Finance & Accounting Staff',
        'department' => 'Finance',
        'dealer' => 'DAW Manado',
        'applied_on' => '2026-03-02',
        'psychotes' => 'Lulus',
        'hr_interview' => 'Direkomendasikan',
        'user_interview' => 'Direkomendasikan',
        'last_interview' => '2026-03-11',
        'status' => 'Diterima',
    ],
];

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
    $search_text = strtolower( $candidate['name'] . ' ' . $candidate['code'] . ' ' . $candidate['position'] . ' ' . $candidate['dealer'] );
    $matches_search = '' === $search || false !== strpos( $search_text, strtolower( $search ) );
    $matches_position = '' === $position_filter || strtolower( $candidate['position'] ) === strtolower( $position_filter );
    $matches_dealer = '' === $dealer_filter || strtolower( $candidate['dealer'] ) === strtolower( $dealer_filter );
    $matches_status = '' === $status_filter || $status_filter === strtolower( str_replace( ' ', '_', $candidate['status'] ) );

    if ( $matches_search && $matches_position && $matches_dealer && $matches_status ) {
        $filtered_candidates[] = $candidate;
    }
}

$stats = [
    'total' => count( $filtered_candidates ),
    'waiting' => 0,
    'accepted' => 0,
    'rejected' => 0,
    'review' => 0,
];
foreach ( $filtered_candidates as $candidate ) {
    switch ( strtolower( $candidate['status'] ) ) {
        case 'menunggu keputusan':
            $stats['waiting']++;
            break;
        case 'diterima':
            $stats['accepted']++;
            break;
        case 'tidak lolos':
            $stats['rejected']++;
            break;
        case 'ditinjau kembali':
            $stats['review']++;
            break;
    }
}

$selected_candidate = null;
if ( $selected_candidate_id > 0 ) {
    foreach ( $filtered_candidates as $candidate ) {
        if ( (int) $candidate['id'] === $selected_candidate_id ) {
            $selected_candidate = $candidate;
            break;
        }
    }
}

if ( null === $selected_candidate && $selected_candidate_id > 0 ) {
    foreach ( $candidates as $candidate ) {
        if ( (int) $candidate['id'] === $selected_candidate_id ) {
            $selected_candidate = $candidate;
            break;
        }
    }
}
?>
<div class="wrap recruitment-admin daw-final-decision-page">
    <header class="daw-feature-header">
        <div>
            <p class="daw-feature-header__breadcrumb">DAW Admin / Final Decision</p>
            <h1>Final Decision</h1>
        </div>
        <div class="daw-feature-header__actions">
            <button type="button" class="button button-secondary">Export</button>
            <button type="button" class="button button-primary">Kirim Email Hasil</button>
        </div>
    </header>

    <form method="get" class="daw-filter-bar" action="<?= esc_url( admin_url( 'admin.php' ) ) ?>">
        <input type="hidden" name="page" value="recruitment-final-decision" />
        <div class="daw-filter-bar__controls">
            <label class="daw-filter-bar__search">
                <span class="dashicons dashicons-search" aria-hidden="true"></span>
                <input type="search" name="search" value="<?= esc_attr( $search ) ?>" placeholder="Cari nama atau kode..." />
            </label>
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
                <option value="menunggu_keputusan" <?= selected( $status_filter, 'menunggu_keputusan', false ) ?>>Menunggu Keputusan</option>
                <option value="diterima" <?= selected( $status_filter, 'diterima', false ) ?>>Diterima</option>
                <option value="tidak_lolos" <?= selected( $status_filter, 'tidak_lolos', false ) ?>>Tidak Lolos</option>
                <option value="ditinjau_kembali" <?= selected( $status_filter, 'ditinjau_kembali', false ) ?>>Ditinjau Kembali</option>
            </select>
            <button type="submit" class="button button-primary">Terapkan</button>
            <a href="<?= esc_url( admin_url( 'admin.php?page=recruitment-final-decision' ) ) ?>" class="button button-secondary">Reset</a>
        </div>
    </form>

    <section class="daw-feature-stats" aria-label="Statistik final decision">
        <article class="daw-feature-stat daw-feature-stat--dark">
            <span>Total Kandidat</span>
            <strong><?= esc_html( number_format_i18n( $stats['total'] ) ) ?></strong>
        </article>
        <article class="daw-feature-stat daw-feature-stat--amber">
            <span>Menunggu Keputusan</span>
            <strong><?= esc_html( number_format_i18n( $stats['waiting'] ) ) ?></strong>
        </article>
        <article class="daw-feature-stat daw-feature-stat--green">
            <span>Diterima</span>
            <strong><?= esc_html( number_format_i18n( $stats['accepted'] ) ) ?></strong>
        </article>
        <article class="daw-feature-stat daw-feature-stat--red">
            <span>Tidak Lolos</span>
            <strong><?= esc_html( number_format_i18n( $stats['rejected'] ) ) ?></strong>
        </article>
    </section>

    <?php if ( $selected_candidate ) : ?>
        <section class="daw-final-decision__detail">
            <div class="daw-final-decision__summary">
                <h2><?= esc_html( $selected_candidate['name'] ) ?></h2>
                <p><?= esc_html( $selected_candidate['code'] ) ?> · <?= esc_html( $selected_candidate['position'] ) ?> · <?= esc_html( $selected_candidate['dealer'] ) ?></p>
                <div class="daw-tag-row">
                    <span class="daw-status-badge daw-status-badge--waiting"><?= esc_html( $selected_candidate['status'] ) ?></span>
                    <span class="daw-stage-badge daw-stage-badge--final">Final Decision</span>
                </div>
            </div>

            <div class="daw-final-decision__grid">
                <div class="daw-final-decision__card">
                    <h3>Riwayat Seleksi</h3>
                    <dl>
                        <div><dt>Psikotes</dt><dd><?= esc_html( $selected_candidate['psychotes'] ) ?></dd></div>
                        <div><dt>Wawancara HR</dt><dd><?= esc_html( $selected_candidate['hr_interview'] ) ?></dd></div>
                        <div><dt>Wawancara User</dt><dd><?= esc_html( $selected_candidate['user_interview'] ) ?></dd></div>
                        <div><dt>Terakhir Wawancara</dt><dd><?= esc_html( date_i18n( 'd F Y', strtotime( $selected_candidate['last_interview'] ) ) ) ?></dd></div>
                    </dl>
                </div>
                <div class="daw-final-decision__card">
                    <h3>Keputusan Akhir</h3>
                    <form method="post" action="<?= esc_url( admin_url( 'admin.php?page=recruitment-final-decision' ) ) ?>">
                        <input type="hidden" name="candidate_id" value="<?= esc_attr( (string) $selected_candidate['id'] ) ?>" />
                        <label>
                            Pilihan Keputusan
                            <select name="decision_status">
                                <option value="Diterima">Diterima</option>
                                <option value="Tidak Lolos">Tidak Lolos</option>
                                <option value="Ditinjau Kembali">Ditinjau Kembali</option>
                            </select>
                        </label>
                        <label>
                            Catatan Keputusan
                            <textarea rows="4" name="decision_note" placeholder="Tuliskan catatan keputusan akhir HR..."></textarea>
                        </label>
                        <button type="submit" class="button button-primary">Simpan Keputusan</button>
                        <button type="button" class="button button-secondary">Konfirmasi</button>
                    </form>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="daw-table-card">
        <div class="daw-table-card__top">
            <strong><?= esc_html( number_format_i18n( count( $filtered_candidates ) ) ) ?> kandidat</strong>
        </div>

        <?php if ( empty( $filtered_candidates ) ) : ?>
            <div class="daw-empty-state">Kandidat tidak ditemukan. Silakan ubah kata kunci atau filter pencarian.</div>
        <?php else : ?>
            <div class="daw-table-wrap">
                <table class="daw-data-table">
                    <thead>
                        <tr>
                            <th>Nama / Kode</th>
                            <th>Posisi</th>
                            <th>Dealer</th>
                            <th>Tanggal Melamar</th>
                            <th>Hasil Psikotes</th>
                            <th>Status Final</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $filtered_candidates as $candidate ) : ?>
                            <tr>
                                <td>
                                    <strong><?= esc_html( $candidate['name'] ) ?></strong><br />
                                    <small><?= esc_html( $candidate['code'] ) ?></small>
                                </td>
                                <td><?= esc_html( $candidate['position'] ) ?></td>
                                <td><?= esc_html( $candidate['dealer'] ) ?></td>
                                <td><?= esc_html( date_i18n( 'd F Y', strtotime( $candidate['applied_on'] ) ) ) ?></td>
                                <td><?= esc_html( $candidate['psychotes'] ) ?></td>
                                <td><span class="daw-status-badge daw-status-badge--<?= strtolower( str_replace( ' ', '-', $candidate['status'] ) ) ?>"><?= esc_html( $candidate['status'] ) ?></span></td>
                                <td>
                                    <a href="<?= esc_url( add_query_arg( [ 'page' => 'recruitment-final-decision', 'candidate_id' => $candidate['id'] ], admin_url( 'admin.php' ) ) ) ?>" class="button button-small">Detail</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</div>
