<?php
$database    = new Recruitment_Database();
$applications = $database->get_applications();
$applicants   = $database->get_applicants();
$vacancies    = $database->get_vacancies();
$applicants_by_id = [];
$vacancies_by_id  = [];

foreach ( $applicants as $applicant ) {
    $applicant = array_change_key_case( $applicant, CASE_LOWER );
    $applicants_by_id[ (string) $applicant['id'] ] = $applicant;
}
foreach ( $vacancies as $vacancy ) {
    $vacancy = array_change_key_case( $vacancy, CASE_LOWER );
    $vacancies_by_id[ (string) $vacancy['id'] ] = $vacancy;
}

$stages = [
    'administration' => 'Administrasi',
    'psychological_test' => 'Psikotes',
    'hr_interview' => 'Wawancara HR',
    'user_interview' => 'Wawancara User',
    'final' => 'Final',
];
$status_stages = [
    'submitted' => 'administration',
    'screening' => 'administration',
    'administrasi' => 'administration',
    'psychological_test' => 'psychological_test',
    'psych_test' => 'psychological_test',
    'psikotes' => 'psychological_test',
    'interview' => 'hr_interview',
    'interview_hr' => 'hr_interview',
    'wawancara_hr' => 'hr_interview',
    'interview_user' => 'user_interview',
    'wawancara_user' => 'user_interview',
    'accepted' => 'final',
    'rejected' => 'final',
    'final' => 'final',
];
$stage_counts = array_fill_keys( array_keys( $stages ), 0 );
$candidates   = [];
$dealer_names = [];
$vacancy_names = [];
$active_count = 0;

foreach ( $applications as $application ) {
    $application = array_change_key_case( $application, CASE_LOWER );
    $applicant_id = (string) ( $application['applicant_id'] ?? '' );
    $vacancy_id   = (string) ( $application['vacancy_id'] ?? '' );
    $applicant    = $applicants_by_id[ $applicant_id ] ?? [];
    $vacancy      = $vacancies_by_id[ $vacancy_id ] ?? [];
    $status       = strtolower( str_replace( [ ' ', '-' ], '_', (string) ( $application['status'] ?? 'submitted' ) ) );
    $stage        = $status_stages[ $status ] ?? 'other';
    $name         = (string) ( $applicant['name'] ?? 'Pelamar #' . ( $applicant_id ?: $application['id'] ) );
    $vacancy_name = (string) ( $vacancy['title'] ?? ( $vacancy_id ? 'Lowongan #' . $vacancy_id : 'Lowongan tidak tersedia' ) );
    $dealer       = (string) ( $vacancy['dealer'] ?? '' );
    $date_value   = (string) ( $application['created_at'] ?? $applicant['created_at'] ?? '' );
    $timestamp    = '' !== $date_value ? strtotime( $date_value ) : false;
    $date_label   = false !== $timestamp ? date_i18n( 'j F Y', $timestamp ) : 'Tanggal tidak tersedia';
    $name_parts   = preg_split( '/\s+/', trim( $name ) ) ?: [];
    $initials     = strtoupper( substr( $name_parts[0] ?? 'P', 0, 1 ) . substr( $name_parts[1] ?? '', 0, 1 ) );
    $stage_label  = $stages[ $stage ] ?? ucwords( str_replace( '_', ' ', $status ) );
    $state_label  = [ 'submitted' => 'Menunggu', 'accepted' => 'Diterima', 'rejected' => 'Tidak Lolos' ][ $status ] ?? 'Berlangsung';

    if ( isset( $stage_counts[ $stage ] ) ) {
        $stage_counts[ $stage ]++;
    }
    if ( ! in_array( $status, [ 'accepted', 'rejected' ], true ) ) {
        $active_count++;
    }
    if ( '' !== $dealer ) {
        $dealer_names[ $dealer ] = $dealer;
    }
    $vacancy_names[ $vacancy_name ] = $vacancy_name;

    $candidates[] = [
        'name' => $name,
        'initials' => $initials,
        'token' => (string) ( $application['application_token'] ?? 'Lamaran #' . ( $application['id'] ?? '' ) ),
        'date' => $date_label,
        'vacancy' => $vacancy_name,
        'dealer' => $dealer,
        'location' => (string) ( $vacancy['location'] ?? '' ),
        'stage' => $stage,
        'stage_label' => $stage_label,
        'state_label' => $state_label,
        'search' => strtolower( $name . ' ' . $vacancy_name . ' ' . $dealer . ' ' . (string) ( $applicant['email'] ?? '' ) ),
    ];
}

$database_ready = $database->is_configured() && class_exists( 'PDO' ) && in_array( 'oci', PDO::getAvailableDrivers(), true );
?>
<div class="wrap recruitment-admin recruitment-dashboard" data-recruitment-dashboard>
    <div class="recruitment-dashboard__banner">
        <div>
            <p class="recruitment-dashboard__breadcrumb">DAW Admin <span>/</span> Recruitment — Kandidat Aktif</p>
            <h1>Rekrutmen Aktif</h1>
            <p class="recruitment-dashboard__subtitle">Kelola kandidat di semua tahap seleksi yang sedang berjalan</p>
        </div>
        <div class="recruitment-dashboard__active-count"><span class="dashicons dashicons-groups" aria-hidden="true"></span><strong><?= esc_html( (string) $active_count ) ?></strong><span>Aktif</span></div>
    </div>

    <section class="recruitment-dashboard__pipeline" aria-label="Pipeline kandidat">
        <p class="recruitment-dashboard__section-label">Filter tahap</p>
        <div class="recruitment-dashboard__stage-filters" role="group" aria-label="Pilih tahap kandidat">
            <button class="is-active" type="button" data-stage-filter="all" aria-pressed="true">Semua <span><?= esc_html( (string) count( $candidates ) ) ?></span></button>
            <?php foreach ( $stages as $stage_key => $stage_name ) : ?>
                <button type="button" data-stage-filter="<?= esc_attr( $stage_key ) ?>" aria-pressed="false"><?= esc_html( $stage_name ) ?> <span><?= esc_html( (string) $stage_counts[ $stage_key ] ) ?></span></button>
            <?php endforeach; ?>
        </div>

        <div class="recruitment-dashboard__filters">
            <label class="recruitment-dashboard__search"><span class="dashicons dashicons-search" aria-hidden="true"></span><span class="screen-reader-text">Cari kandidat</span><input type="search" placeholder="Cari nama, kode, atau posisi..." data-candidate-search></label>
            <label><span class="screen-reader-text">Filter dealer</span><select data-candidate-dealer><option value="">Semua Dealer</option><?php foreach ( $dealer_names as $dealer_name ) : ?><option value="<?= esc_attr( $dealer_name ) ?>"><?= esc_html( $dealer_name ) ?></option><?php endforeach; ?></select></label>
            <label><span class="screen-reader-text">Filter posisi</span><select data-candidate-vacancy><option value="">Semua Posisi</option><?php foreach ( $vacancy_names as $vacancy_name ) : ?><option value="<?= esc_attr( $vacancy_name ) ?>"><?= esc_html( $vacancy_name ) ?></option><?php endforeach; ?></select></label>
            <button class="button" type="button" data-candidate-reset>Reset</button>
        </div>

        <p class="recruitment-dashboard__result-count" aria-live="polite"><span data-candidate-count><?= esc_html( (string) count( $candidates ) ) ?></span> kandidat ditampilkan</p>
        <div class="recruitment-dashboard__candidate-list" data-candidate-list>
            <?php foreach ( $candidates as $candidate ) : ?>
                <article class="recruitment-candidate" data-candidate-card data-stage="<?= esc_attr( $candidate['stage'] ) ?>" data-dealer="<?= esc_attr( $candidate['dealer'] ) ?>" data-vacancy="<?= esc_attr( $candidate['vacancy'] ) ?>" data-search="<?= esc_attr( $candidate['search'] ) ?>">
                    <span class="recruitment-candidate__avatar" aria-hidden="true"><?= esc_html( $candidate['initials'] ) ?></span>
                    <div class="recruitment-candidate__identity">
                        <h2><?= esc_html( $candidate['name'] ) ?></h2>
                        <p><?= esc_html( $candidate['token'] ) ?> <span>·</span> Daftar <?= esc_html( $candidate['date'] ) ?></p>
                        <p class="recruitment-candidate__position"><?= esc_html( $candidate['vacancy'] ) ?><?php if ( '' !== $candidate['dealer'] || '' !== $candidate['location'] ) : ?> <span>·</span> <?= esc_html( implode( ' · ', array_filter( [ $candidate['dealer'], $candidate['location'] ] ) ) ) ?><?php endif; ?></p>
                    </div>
                    <div class="recruitment-candidate__status">
                        <span class="recruitment-candidate__stage recruitment-candidate__stage--<?= esc_attr( $candidate['stage'] ) ?>"><?= esc_html( $candidate['stage_label'] ) ?></span>
                        <span class="recruitment-candidate__state"><?= esc_html( $candidate['state_label'] ) ?></span>
                    </div>
                </article>
            <?php endforeach; ?>
            <div class="recruitment-dashboard__empty" data-candidate-empty <?= $candidates ? 'hidden' : '' ?>>
                <?php if ( ! $database_ready ) : ?>
                    Koneksi Oracle belum siap. Periksa konfigurasi DSN, user, password, dan driver PDO OCI.
                <?php else : ?>
                    Belum ada kandidat yang sesuai dengan filter ini.
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
