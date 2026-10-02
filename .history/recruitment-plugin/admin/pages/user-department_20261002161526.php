<?php
$current_user = wp_get_current_user();
$display_name = ! empty( $current_user->display_name ) ? $current_user->display_name : 'Andi Wirawan';
$department = (string) get_user_meta( $current_user->ID, 'department', true );
$department = '' !== $department ? $department : 'Sales';
$view = sanitize_key( wp_unslash( $_GET['view'] ?? 'dashboard' ) );
$views = [ 'dashboard', 'requests', 'interviews', 'interview-detail', 'applicants', 'results' ];
if ( ! in_array( $view, $views, true ) ) {
	$view = 'dashboard';
}
$page_url = admin_url( 'admin.php?page=daw-user-department' );
$view_url = static function ( string $requested_view ) use ( $page_url ): string {
	return add_query_arg( 'view', $requested_view, $page_url );
};
$view_titles = [
	'dashboard' => 'Dashboard',
	'requests' => 'Request Pembukaan Lowongan',
	'interviews' => 'Kandidat Wawancara User',
	'interview-detail' => 'Detail Kandidat',
	'applicants' => 'Data Pelamar',
	'results' => 'Hasil Wawancara User',
];
$request_filter = sanitize_text_field( wp_unslash( $_GET['status'] ?? '' ) );
$interview_filter = sanitize_text_field( wp_unslash( $_GET['interview_status'] ?? '' ) );
$search = sanitize_text_field( wp_unslash( $_GET['search'] ?? '' ) );

$requests = [
	[ 'code' => 'FPTK-2026-031', 'position' => 'Sales Executive', 'count' => 2, 'priority' => 'Tinggi', 'date' => '20 Sep 2026', 'status' => 'Disetujui' ],
	[ 'code' => 'FPTK-2026-029', 'position' => 'Sales Admin', 'count' => 1, 'priority' => 'Sedang', 'date' => '17 Sep 2026', 'status' => 'Perlu Revisi' ],
	[ 'code' => 'FPTK-2026-025', 'position' => 'Customer Service', 'count' => 3, 'priority' => 'Urgent', 'date' => '10 Sep 2026', 'status' => 'Lowongan Dibuka' ],
	[ 'code' => 'FPTK-2026-023', 'position' => 'After Sales Technician', 'count' => 2, 'priority' => 'Tinggi', 'date' => '5 Sep 2026', 'status' => 'Ready for Recruitment' ],
	[ 'code' => 'FPTK-2026-020', 'position' => 'Sales Supervisor', 'count' => 1, 'priority' => 'Sedang', 'date' => '28 Agu 2026', 'status' => 'Review HR' ],
	[ 'code' => 'FPTK-2026-018', 'position' => 'Body Repair Advisor', 'count' => 1, 'priority' => 'Rendah', 'date' => '20 Agu 2026', 'status' => 'Draft' ],
	[ 'code' => 'FPTK-2026-015', 'position' => 'Finance Staff', 'count' => 2, 'priority' => 'Sedang', 'date' => '15 Agu 2026', 'status' => 'Draft' ],
];
$interviews = [
	[ 'name' => 'Budi Santoso', 'code' => 'DW-2026-0781', 'position' => 'Sales Executive', 'dealer' => 'DAW Airmadidi', 'status' => 'Terjadwal', 'hr_result' => 'Sangat Direkomendasikan', 'date' => '24 Sep 2026 · 10:00', 'score' => '' ],
	[ 'name' => 'Aiko Hanako', 'code' => 'DW-2026-0774', 'position' => 'Sales Executive', 'dealer' => 'DAW Kotamobagu', 'status' => 'Terjadwal', 'hr_result' => 'Direkomendasikan', 'date' => '25 Sep 2026 · 14:00', 'score' => '' ],
	[ 'name' => 'Christian Vieri', 'code' => 'DW-2026-0768', 'position' => 'Sales Admin', 'dealer' => 'DAW Manado', 'status' => 'Menunggu Jadwal', 'hr_result' => 'Direkomendasikan', 'date' => '—', 'score' => '' ],
	[ 'name' => 'Sari Dewi Putri', 'code' => 'DW-2026-0761', 'position' => 'Customer Service', 'dealer' => 'DAW Airmadidi', 'status' => 'Sudah Dinilai', 'hr_result' => 'Sangat Direkomendasikan', 'date' => '20 Sep 2026 · 09:00', 'score' => '87' ],
	[ 'name' => 'Rizal Fauzy', 'code' => 'DW-2026-0755', 'position' => 'Customer Service', 'dealer' => 'DAW Manado', 'status' => 'Sudah Dinilai', 'hr_result' => 'Perlu Pertimbangan', 'date' => '19 Sep 2026 · 13:00', 'score' => '62' ],
	[ 'name' => 'Mega Lestari', 'code' => 'DW-2026-0749', 'position' => 'Sales Supervisor', 'dealer' => 'DAW Kotamobagu', 'status' => 'Sudah Dinilai', 'hr_result' => 'Direkomendasikan', 'date' => '18 Sep 2026 · 11:00', 'score' => '79' ],
];
$rating_criteria = [
	'presentation' => 'Penampilan & Kerapian',
	'communication' => 'Kemampuan Komunikasi',
	'motivation' => 'Motivasi & Antusiasme',
	'experience' => 'Relevansi Pengalaman',
	'knowledge' => 'Pengetahuan Teknis/Produk',
	'teamwork' => 'Kemampuan Kerjasama Tim',
	'problem_solving' => 'Problem Solving',
	'adaptability' => 'Kemampuan Adaptasi',
];
$stored_interview_results = get_user_meta( $current_user->ID, 'daw_user_interview_results', true );
$stored_interview_results = is_array( $stored_interview_results ) ? $stored_interview_results : [];
$posted_scores = [];
$posted_recommendation = '';
$posted_notes = '';
$interview_save_error = '';
$interview_saved = '1' === sanitize_text_field( wp_unslash( $_GET['saved'] ?? '' ) );

if ( 'POST' === strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) ) && 'save_user_interview' === sanitize_key( wp_unslash( $_POST['daw_ud_action'] ?? '' ) ) ) {
	$nonce = sanitize_text_field( wp_unslash( $_POST['daw_user_interview_nonce'] ?? '' ) );
	if ( ! wp_verify_nonce( $nonce, 'daw_save_user_interview' ) ) {
		wp_die( esc_html__( 'Sesi penilaian tidak valid. Muat ulang halaman dan coba lagi.', 'recruitment-plugin' ) );
	}
	$posted_code = sanitize_text_field( wp_unslash( $_POST['candidate_code'] ?? '' ) );
	$posted_scores = isset( $_POST['scores'] ) && is_array( $_POST['scores'] ) ? wp_unslash( $_POST['scores'] ) : [];
	foreach ( $rating_criteria as $key => $label ) {
		$posted_scores[ $key ] = isset( $posted_scores[ $key ] ) ? min( 5, max( 1, absint( $posted_scores[ $key ] ) ) ) : 0;
	}
	$posted_recommendation = sanitize_text_field( wp_unslash( $_POST['recommendation'] ?? '' ) );
	$posted_notes = sanitize_textarea_field( wp_unslash( $_POST['interview_notes'] ?? '' ) );
	$valid_recommendations = [ 'Sangat Baik', 'Baik', 'Dipertimbangkan', 'Tidak Dilanjutkan' ];
	$valid_candidate = false;
	foreach ( $interviews as $interview ) {
		if ( $interview['code'] === $posted_code && in_array( $interview['status'], [ 'Terjadwal', 'Sudah Dinilai' ], true ) ) {
			$valid_candidate = true;
			break;
		}
	}
	if ( ! $valid_candidate ) {
		$interview_save_error = 'Kandidat ini tidak sedang dalam status wawancara terjadwal.';
	} elseif ( in_array( 0, $posted_scores, true ) || ! in_array( $posted_recommendation, $valid_recommendations, true ) ) {
		$interview_save_error = 'Lengkapi seluruh penilaian dan pilih rekomendasi sebelum menyimpan.';
	} else {
		$score_total = (int) floor( array_sum( $posted_scores ) / ( count( $rating_criteria ) * 5 ) * 100 );
		$stored_interview_results[ $posted_code ] = [
			'scores' => $posted_scores,
			'total' => $score_total,
			'recommendation' => $posted_recommendation,
			'notes' => $posted_notes,
			'interviewer' => $display_name,
			'saved_at' => current_time( 'mysql' ),
		];
		update_user_meta( $current_user->ID, 'daw_user_interview_results', $stored_interview_results );
		wp_safe_redirect( add_query_arg( [ 'view' => 'interview-detail', 'candidate_code' => $posted_code, 'saved' => '1' ], $page_url ) );
		exit;
	}
}

foreach ( $interviews as &$interview ) {
	if ( isset( $stored_interview_results[ $interview['code'] ] ) ) {
		$saved_result = $stored_interview_results[ $interview['code'] ];
		$interview['status'] = 'Sudah Dinilai';
		$interview['score'] = (string) ( $saved_result['total'] ?? 0 );
		$interview['recommendation'] = (string) ( $saved_result['recommendation'] ?? '' );
		$interview['notes'] = (string) ( $saved_result['notes'] ?? '' );
		$interview['saved_at'] = (string) ( $saved_result['saved_at'] ?? '' );
		$interview['scores'] = is_array( $saved_result['scores'] ?? null ) ? $saved_result['scores'] : [];
	}
}
unset( $interview );

$candidate_profiles = [
	'DW-2026-0781' => [
		'gender' => 'Laki-laki', 'birth' => 'Manado, 15 Mar 1995', 'email' => 'budi.santoso@example.test', 'phone' => '0812-3456-7890', 'city' => 'Manado, Sulawesi Utara',
		'education' => [ 'S1 Manajemen Bisnis', 'Universitas Sam Ratulangi Manado', 'Lulus 2018 · IPK 3.42' ],
		'experience' => [ [ 'Sales Representative', 'PT Auto Motor Manado', '2019 – 2023 · 4 tahun', 'Penjualan kendaraan roda dua dan pencapaian target.' ], [ 'Sales Supervisor', 'CV Mitra Niaga', '2023 – 2026 · 3 tahun', 'Memimpin tim penjualan dan menjaga kepuasan pelanggan.' ] ],
	],
];
$selected_interview = null;
$selected_interview_code = sanitize_text_field( wp_unslash( $_GET['candidate_code'] ?? $_POST['candidate_code'] ?? '' ) );
foreach ( $interviews as $interview ) {
	if ( $interview['code'] === $selected_interview_code ) {
		$selected_interview = $interview;
		break;
	}
}
$applicants = [
	[ 'name' => 'Sari Dewi Putri', 'code' => 'DW-2026-0761', 'position' => 'Customer Service', 'dealer' => 'DAW Airmadidi', 'date' => '15 Sep 2026', 'hr_result' => 'Sangat Baik', 'user_result' => 'Sangat Baik', 'status' => 'Diterima' ],
	[ 'name' => 'Rizal Fauzy', 'code' => 'DW-2026-0755', 'position' => 'Customer Service', 'dealer' => 'DAW Manado', 'date' => '14 Sep 2026', 'hr_result' => 'Baik', 'user_result' => 'Dipertimbangkan', 'status' => 'Ditolak' ],
	[ 'name' => 'Mega Lestari', 'code' => 'DW-2026-0749', 'position' => 'Sales Supervisor', 'dealer' => 'DAW Kotamobagu', 'date' => '12 Sep 2026', 'hr_result' => 'Baik', 'user_result' => 'Baik', 'status' => 'Pending' ],
	[ 'name' => 'Doni Prasetyo', 'code' => 'DW-2026-0731', 'position' => 'Sales Executive', 'dealer' => 'DAW Airmadidi', 'date' => '8 Sep 2026', 'hr_result' => 'Sangat Baik', 'user_result' => 'Dipilih', 'status' => 'Diterima' ],
	[ 'name' => 'Budi Santoso', 'code' => 'DW-2026-0781', 'position' => 'Sales Executive', 'dealer' => 'DAW Manado', 'date' => '22 Sep 2026', 'hr_result' => 'Baik', 'user_result' => 'Belum dinilai', 'status' => 'Proses' ],
	[ 'name' => 'Aiko Hanako', 'code' => 'DW-2026-0774', 'position' => 'Sales Executive', 'dealer' => 'DAW Kotamobagu', 'date' => '22 Sep 2026', 'hr_result' => 'Sangat Baik', 'user_result' => 'Belum dinilai', 'status' => 'Proses' ],
];
$results = [
	[ 'name' => 'Sari Dewi Putri', 'code' => 'DW-2026-0761', 'position' => 'Customer Service', 'date' => '20 Sep 2026', 'score' => 87, 'recommendation' => 'Terima', 'decision' => 'Diterima' ],
	[ 'name' => 'Rizal Fauzy', 'code' => 'DW-2026-0755', 'position' => 'Customer Service', 'date' => '19 Sep 2026', 'score' => 62, 'recommendation' => 'Pertimbangkan', 'decision' => 'Ditolak' ],
	[ 'name' => 'Mega Lestari', 'code' => 'DW-2026-0749', 'position' => 'Sales Supervisor', 'date' => '18 Sep 2026', 'score' => 79, 'recommendation' => 'Terima', 'decision' => 'Pending' ],
	[ 'name' => 'Doni Prasetyo', 'code' => 'DW-2026-0731', 'position' => 'Sales Executive', 'date' => '12 Sep 2026', 'score' => 91, 'recommendation' => 'Terima', 'decision' => 'Diterima' ],
	[ 'name' => 'Fitri Handayani', 'code' => 'DW-2026-0720', 'position' => 'Sales Executive', 'date' => '8 Sep 2026', 'score' => 55, 'recommendation' => 'Tolak', 'decision' => 'Ditolak' ],
];
foreach ( $interviews as $interview ) {
	if ( 'Sudah Dinilai' !== $interview['status'] || ! isset( $stored_interview_results[ $interview['code'] ] ) ) {
		continue;
	}
	$has_result = false;
	foreach ( $results as &$result ) {
		if ( $result['code'] === $interview['code'] ) {
			$result['score'] = (int) $interview['score'];
			$result['recommendation'] = $interview['recommendation'] ?? 'Pertimbangkan';
			$result['decision'] = 'Pending';
			$has_result = true;
			break;
		}
	}
	unset( $result );
	if ( ! $has_result ) {
		$results[] = [
			'name' => $interview['name'],
			'code' => $interview['code'],
			'position' => $interview['position'],
			'date' => current_time( 'd M Y' ),
			'score' => (int) $interview['score'],
			'recommendation' => $interview['recommendation'],
			'decision' => 'Pending',
		];
	}
}

$request_statuses = [ 'Draft' => 2, 'Menunggu Review HR' => 3, 'Perlu Revisi' => 1, 'Disetujui' => 5, 'Lowongan Dibuka' => 2 ];
$filtered_requests = [];
foreach ( $requests as $request ) {
	$matches_status = '' === $request_filter || $request['status'] === $request_filter;
	$matches_search = '' === $search || false !== strpos( strtolower( $request['code'] . ' ' . $request['position'] ), strtolower( $search ) );
	if ( $matches_status && $matches_search ) {
		$filtered_requests[] = $request;
	}
}
$filtered_interviews = [];
foreach ( $interviews as $interview ) {
	$matches_search = '' === $search || false !== strpos( strtolower( $interview['name'] . ' ' . $interview['code'] . ' ' . $interview['position'] ), strtolower( $search ) );
	$matches_status = '' === $interview_filter || $interview['status'] === $interview_filter;
	if ( $matches_search && $matches_status ) {
		$filtered_interviews[] = $interview;
	}
}
$filtered_applicants = [];
foreach ( $applicants as $applicant ) {
	$matches_search = '' === $search || false !== strpos( strtolower( $applicant['name'] . ' ' . $applicant['code'] . ' ' . $applicant['position'] ), strtolower( $search ) );
	if ( $matches_search ) {
		$filtered_applicants[] = $applicant;
	}
}
$interview_stats = [ 'waiting' => 0, 'scheduled' => 0, 'done' => 0 ];
foreach ( $interviews as $interview ) {
	$key = [ 'Menunggu Jadwal' => 'waiting', 'Terjadwal' => 'scheduled', 'Sudah Dinilai' => 'done' ][ $interview['status'] ];
	++$interview_stats[ $key ];
}
$applicant_stats = [ 'accepted' => 0, 'rejected' => 0, 'in_progress' => 0 ];
foreach ( $applicants as $applicant ) {
	if ( 'Diterima' === $applicant['status'] ) {
		++$applicant_stats['accepted'];
	} elseif ( 'Ditolak' === $applicant['status'] ) {
		++$applicant_stats['rejected'];
	} else {
		++$applicant_stats['in_progress'];
	}
}
$result_stats = [ 'recommended' => 0, 'accepted' => 0, 'pending' => 0 ];
foreach ( $results as $result ) {
	if ( 'Terima' === $result['recommendation'] ) {
		++$result_stats['recommended'];
	}
	if ( 'Diterima' === $result['decision'] ) {
		++$result_stats['accepted'];
	} elseif ( 'Pending' === $result['decision'] ) {
		++$result_stats['pending'];
	}
}
$greeting_name = trim( (string) preg_replace( '/\s+.*/', '', $display_name ) );
?>
<div class="daw-user-department">
	<aside class="daw-ud-sidebar" aria-label="Navigasi User Department">
		<a class="daw-ud-brand" href="<?= esc_url( $view_url( 'dashboard' ) ) ?>"><span class="daw-ud-brand__mark">D</span><span><strong>Daya Adicipta Wisesa</strong><small>User Department</small></span></a>
		<div class="daw-ud-identity"><span>User Department</span><strong><?= esc_html( $department ) ?> — <?= esc_html( $display_name ) ?></strong></div>
		<nav class="daw-ud-nav" aria-label="Menu User Department">
			<a class="<?= 'dashboard' === $view ? 'is-active' : '' ?>" href="<?= esc_url( $view_url( 'dashboard' ) ) ?>"><span class="dashicons dashicons-dashboard" aria-hidden="true"></span>Dashboard</a>
			<a class="<?= 'requests' === $view ? 'is-active' : '' ?>" href="<?= esc_url( $view_url( 'requests' ) ) ?>"><span class="dashicons dashicons-clipboard" aria-hidden="true"></span>Request Lowongan</a>
			<a class="<?= 'interviews' === $view ? 'is-active' : '' ?>" href="<?= esc_url( $view_url( 'interviews' ) ) ?>"><span class="dashicons dashicons-groups" aria-hidden="true"></span><span>Kandidat Wawancara User</span><b><?= esc_html( (string) ( $interview_stats['waiting'] + $interview_stats['scheduled'] ) ) ?></b></a>
			<a class="<?= 'applicants' === $view ? 'is-active' : '' ?>" href="<?= esc_url( $view_url( 'applicants' ) ) ?>"><span class="dashicons dashicons-database" aria-hidden="true"></span>Data Pelamar</a>
			<a class="<?= 'results' === $view ? 'is-active' : '' ?>" href="<?= esc_url( $view_url( 'results' ) ) ?>"><span class="dashicons dashicons-list-view" aria-hidden="true"></span>Hasil Wawancara User</a>
		</nav>
		<div class="daw-ud-sidebar__footer"><span class="dashicons dashicons-building" aria-hidden="true"></span><?= esc_html( $department ) ?> Department</div>
	</aside>

	<div class="daw-ud-workspace">
		<header class="daw-ud-topbar"><h1><?= esc_html( $view_titles[ $view ] ) ?></h1><div><span class="daw-ud-user-avatar"><?= esc_html( strtoupper( substr( $greeting_name, 0, 1 ) ) ) ?></span><span><strong><?= esc_html( $display_name ) ?></strong><small><?= esc_html( $department ) ?> Department</small></span></div></header>
		<main class="daw-ud-main">
			<?php if ( 'dashboard' === $view ) : ?>
				<section class="daw-ud-welcome"><p>Selamat datang, <strong><?= esc_html( $display_name ) ?></strong></p><span>Departemen <?= esc_html( $department ) ?> — <?= esc_html( date_i18n( 'l, j F Y' ) ) ?></span></section>
				<section class="daw-ud-section"><div class="daw-ud-section__heading"><h2>Permintaan Lowongan</h2><a href="<?= esc_url( $view_url( 'requests' ) ) ?>">Lihat Semua</a></div><div class="daw-ud-stats daw-ud-stats--four"><article><span>Draft</span><strong><?= esc_html( (string) $request_statuses['Draft'] ) ?></strong></article><article><span>Menunggu Review HR</span><strong class="is-orange"><?= esc_html( (string) $request_statuses['Menunggu Review HR'] ) ?></strong></article><article><span>Perlu Revisi</span><strong class="is-red"><?= esc_html( (string) $request_statuses['Perlu Revisi'] ) ?></strong><small>Perlu tindakan</small></article><article><span>Disetujui</span><strong class="is-green"><?= esc_html( (string) $request_statuses['Disetujui'] ) ?></strong></article></div></section>
				<section class="daw-ud-section"><div class="daw-ud-section__heading"><h2>Kandidat User Interview</h2><a href="<?= esc_url( $view_url( 'interviews' ) ) ?>">Lihat Semua</a></div><div class="daw-ud-stats daw-ud-stats--three"><article><span>Menunggu Jadwal</span><strong class="is-orange"><?= esc_html( (string) $interview_stats['waiting'] ) ?></strong></article><article><span>Terjadwal</span><strong><?= esc_html( (string) $interview_stats['scheduled'] ) ?></strong></article><article><span>Sudah Dinilai</span><strong class="is-green"><?= esc_html( (string) $interview_stats['done'] ) ?></strong></article></div></section>
				<section class="daw-ud-section daw-ud-activity-section"><h2>Aktivitas Terkini</h2><div class="daw-ud-activity"><div><i class="is-green"></i><span>Request FPTK-2026-031 disetujui HR<small>22 Sep 2026</small></span></div><div><i class="is-blue"></i><span>Kandidat Budi Santoso terjadwal wawancara user — 24 Sep 2026<small>20 Sep 2026</small></span></div><div><i class="is-orange"></i><span>Request FPTK-2026-029 perlu revisi — lihat catatan HR<small>18 Sep 2026</small></span></div><div><i class="is-green"></i><span>Hasil wawancara Aiko Hanako berhasil dikirim<small>15 Sep 2026</small></span></div></div></section>

			<?php elseif ( 'requests' === $view ) : ?>
				<div class="daw-ud-page-heading"><p>Total <?= esc_html( (string) count( $requests ) ) ?> permintaan</p><button type="button" class="daw-ud-primary-button"><span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span>Buat Request Baru</button></div>
				<nav class="daw-ud-pills" aria-label="Filter status request"><?php foreach ( [ 'Semua' => '', 'Draft' => 'Draft', 'Perlu Revisi' => 'Perlu Revisi', 'Review HR' => 'Review HR', 'Disetujui' => 'Disetujui', 'Lowongan Dibuka' => 'Lowongan Dibuka' ] as $label => $value ) : ?><a class="<?= $request_filter === $value ? 'is-active' : '' ?>" href="<?= esc_url( add_query_arg( [ 'status' => $value ], $view_url( 'requests' ) ) ) ?>"><?= esc_html( $label ) ?></a><?php endforeach; ?></nav>
				<?php if ( '' === $request_filter || 'Perlu Revisi' === $request_filter ) : ?><div class="daw-ud-alert"><span class="dashicons dashicons-warning" aria-hidden="true"></span><span><strong>Ada permintaan yang perlu direvisi</strong><small>FPTK-2026-029: Mohon lengkapi profil kandidat minimum pendidikan.</small></span></div><?php endif; ?>
				<form class="daw-ud-search" method="get" action="<?= esc_url( admin_url( 'admin.php' ) ) ?>"><input type="hidden" name="page" value="daw-user-department" /><input type="hidden" name="view" value="requests" /><input type="hidden" name="status" value="<?= esc_attr( $request_filter ) ?>" /><label><span class="screen-reader-text">Cari request</span><input type="search" name="search" value="<?= esc_attr( $search ) ?>" placeholder="Cari nomor FPTK atau posisi..." /></label><button class="button" type="submit">Cari</button></form>
				<div class="daw-ud-table-wrap"><table class="daw-ud-table"><thead><tr><th>No. FPTK</th><th>Posisi</th><th>Jml</th><th>Prioritas</th><th>Tgl Pengajuan</th><th>Status</th><th>Aksi</th></tr></thead><tbody><?php if ( empty( $filtered_requests ) ) : ?><tr><td colspan="7" class="daw-ud-empty">Tidak ada request yang cocok.</td></tr><?php endif; ?><?php foreach ( $filtered_requests as $request ) : ?><tr><td><strong><?= esc_html( $request['code'] ) ?></strong></td><td><?= esc_html( $request['position'] ) ?></td><td><?= esc_html( (string) $request['count'] ) ?></td><td class="priority-<?= esc_attr( strtolower( $request['priority'] ) ) ?>"><?= esc_html( $request['priority'] ) ?></td><td><?= esc_html( $request['date'] ) ?></td><td><span class="daw-ud-status <?= esc_attr( sanitize_title( $request['status'] ) ) ?>"><?= esc_html( $request['status'] ) ?></span></td><td><a class="daw-ud-link" href="#request-<?= esc_attr( $request['code'] ) ?>">Lihat</a></td></tr><?php endforeach; ?></tbody></table></div>

			<?php elseif ( 'interviews' === $view ) : ?>
				<section class="daw-ud-intro"><h2>Kandidat Wawancara User</h2><p>Kandidat yang telah melewati HR Interview dan diarahkan HR ke departemen Anda.</p></section>
				<nav class="daw-ud-pills" aria-label="Filter status wawancara"><a class="<?= '' === $interview_filter ? 'is-active' : '' ?>" href="<?= esc_url( add_query_arg( [ 'interview_status' => '', 'search' => $search ], $view_url( 'interviews' ) ) ) ?>">Semua <span><?= esc_html( (string) count( $interviews ) ) ?></span></a><a class="<?= 'Menunggu Jadwal' === $interview_filter ? 'is-active' : '' ?>" href="<?= esc_url( add_query_arg( [ 'interview_status' => 'Menunggu Jadwal', 'search' => $search ], $view_url( 'interviews' ) ) ) ?>">Menunggu Jadwal <span><?= esc_html( (string) $interview_stats['waiting'] ) ?></span></a><a class="<?= 'Terjadwal' === $interview_filter ? 'is-active' : '' ?>" href="<?= esc_url( add_query_arg( [ 'interview_status' => 'Terjadwal', 'search' => $search ], $view_url( 'interviews' ) ) ) ?>">Terjadwal <span><?= esc_html( (string) $interview_stats['scheduled'] ) ?></span></a><a class="<?= 'Sudah Dinilai' === $interview_filter ? 'is-active' : '' ?>" href="<?= esc_url( add_query_arg( [ 'interview_status' => 'Sudah Dinilai', 'search' => $search ], $view_url( 'interviews' ) ) ) ?>">Sudah Dinilai <span><?= esc_html( (string) $interview_stats['done'] ) ?></span></a></nav>
				<form class="daw-ud-search" method="get" action="<?= esc_url( admin_url( 'admin.php' ) ) ?>"><input type="hidden" name="page" value="daw-user-department" /><input type="hidden" name="view" value="interviews" /><input type="hidden" name="interview_status" value="<?= esc_attr( $interview_filter ) ?>" /><label><span class="screen-reader-text">Cari kandidat wawancara</span><input type="search" name="search" value="<?= esc_attr( $search ) ?>" placeholder="Cari nama, kode, atau posisi..." /></label><button class="button" type="submit">Cari</button></form>
				<div class="daw-ud-interview-list"><?php foreach ( $filtered_interviews as $interview ) : ?><article class="daw-ud-interview-card"><span class="daw-ud-candidate-avatar"><?= esc_html( strtoupper( substr( preg_replace( '/[^A-Za-z]/', '', $interview['name'] ), 0, 2 ) ) ) ?></span><div class="daw-ud-interview-card__candidate"><strong><?= esc_html( $interview['name'] ) ?></strong><span class="daw-ud-status <?= esc_attr( sanitize_title( $interview['status'] ) ) ?>"><?= esc_html( $interview['status'] ) ?></span><small><?= esc_html( $interview['code'] ) ?> · <?= esc_html( $interview['position'] ) ?> · <?= esc_html( $interview['dealer'] ) ?></small></div><div class="daw-ud-interview-card__meta"><span>Hasil HR<strong><?= esc_html( $interview['hr_result'] ) ?></strong></span><?php if ( '' !== $interview['score'] ) : ?><span>Nilai<strong class="is-green"><?= esc_html( $interview['score'] ) ?></strong></span><?php endif; ?></div><div class="daw-ud-interview-card__date"><small>Jadwal</small><strong><?= esc_html( $interview['date'] ) ?></strong></div><?php if ( 'Terjadwal' === $interview['status'] ) : ?><a class="daw-ud-primary-button" href="<?= esc_url( add_query_arg( [ 'view' => 'interview-detail', 'candidate_code' => $interview['code'] ], $page_url ) ) ?>">Mulai Wawancara</a><?php elseif ( 'Sudah Dinilai' === $interview['status'] ) : ?><a class="button" href="<?= esc_url( add_query_arg( [ 'view' => 'interview-detail', 'candidate_code' => $interview['code'] ], $page_url ) ) ?>">Lihat Hasil</a><?php else : ?><a class="button" href="<?= esc_url( add_query_arg( [ 'view' => 'interview-detail', 'candidate_code' => $interview['code'] ], $page_url ) ) ?>">Lihat Detail</a><?php endif; ?></article><?php endforeach; ?><?php if ( empty( $filtered_interviews ) ) : ?><p class="daw-ud-empty">Tidak ada kandidat yang cocok.</p><?php endif; ?></div>

			<?php elseif ( 'interview-detail' === $view ) : ?>
				<?php if ( ! $selected_interview ) : ?>
					<a class="daw-ud-back-link" href="<?= esc_url( $view_url( 'interviews' ) ) ?>">&larr; Kembali</a><p class="daw-ud-empty">Kandidat wawancara tidak ditemukan.</p>
				<?php else : ?>
					<?php
					$profile = $candidate_profiles[ $selected_interview['code'] ] ?? [
						'gender' => 'Tidak dicantumkan', 'birth' => 'Data belum tersedia', 'email' => 'kandidat@example.test', 'phone' => '—', 'city' => $selected_interview['dealer'],
						'education' => [ 'Informasi pendidikan', 'Belum tersedia', '—' ],
						'experience' => [ [ 'Pengalaman kerja', 'Belum tersedia', '—', 'Informasi pengalaman kerja belum tersedia.' ] ],
					];
					$is_saved = 'Sudah Dinilai' === $selected_interview['status'];
					$is_editing_result = $is_saved && '1' === sanitize_text_field( wp_unslash( $_GET['edit'] ?? '' ) );
					$show_rating_form = 'Terjadwal' === $selected_interview['status'] || $is_editing_result;
					$detail_scores = $posted_scores ?: ( $selected_interview['scores'] ?? [] );
					$detail_score_total = ! empty( $detail_scores ) ? (int) round( array_sum( array_map( 'absint', $detail_scores ) ) / ( count( $rating_criteria ) * 5 ) * 100 ) : 0;
					?>
					<a class="daw-ud-back-link" href="<?= esc_url( $view_url( 'interviews' ) ) ?>">&larr; Kembali</a>
					<section class="daw-ud-detail-heading"><div><strong><?= esc_html( $selected_interview['name'] ) ?></strong><span><?= esc_html( $selected_interview['position'] ) ?> · <?= esc_html( $selected_interview['dealer'] ) ?> · <?= esc_html( $selected_interview['code'] ) ?></span></div><nav aria-label="Tahap wawancara"><span class="<?= 'Menunggu Jadwal' === $selected_interview['status'] ? 'is-current' : '' ?>">Menunggu</span><span class="<?= 'Terjadwal' === $selected_interview['status'] ? 'is-current' : '' ?>">Terjadwal</span><span class="<?= $is_saved ? 'is-current' : '' ?>">Dinilai</span><span class="daw-ud-status <?= esc_attr( sanitize_title( $selected_interview['status'] ) ) ?>"><?= esc_html( $selected_interview['status'] ) ?></span></nav></section>
					<?php if ( $interview_saved ) : ?><p class="daw-ud-success" role="status">Hasil wawancara berhasil disimpan.</p><?php endif; ?>
					<?php if ( '' !== $interview_save_error ) : ?><p class="daw-ud-alert" role="alert"><?= esc_html( $interview_save_error ) ?></p><?php endif; ?>
					<div class="daw-ud-detail-layout">
						<section class="daw-ud-detail-main">
							<div class="daw-ud-interview-summary"><div><small>Jadwal</small><strong><?= esc_html( $selected_interview['date'] ) ?></strong></div><div><small>Metode</small><strong>Tatap Muka</strong></div><div><small>Lokasi</small><strong>Ruang Meeting 2, Lt. 3</strong></div><div><small>Hasil HR</small><strong><?= esc_html( $selected_interview['hr_result'] ) ?></strong></div></div>
							<?php if ( 'Menunggu Jadwal' === $selected_interview['status'] ) : ?>
								<div class="daw-ud-detail-panel"><div class="daw-ud-alert"><span class="dashicons dashicons-clock" aria-hidden="true"></span><span><strong>Menunggu Jadwal Wawancara</strong><small>Jadwal wawancara user belum ditentukan. Hubungi HR untuk konfirmasi jadwal.</small></span></div><div class="daw-ud-summary-grid"><div><small>Hasil HR Interview</small><strong><?= esc_html( $selected_interview['hr_result'] ) ?></strong></div><div><small>Skor Psikotes</small><strong>82/100</strong></div><div><small>Rekomendasi HR</small><strong><?= esc_html( $department ) ?> — <?= esc_html( $display_name ) ?></strong></div><div><small>Tgl Masuk Dept</small><strong>22 Sep 2026</strong></div></div><p class="daw-ud-muted-panel">Form penilaian wawancara akan tersedia setelah jadwal wawancara user dikonfirmasi.</p></div>
							<?php elseif ( $show_rating_form ) : ?>
								<form class="daw-ud-detail-panel daw-ud-rating-form" method="post" action="<?= esc_url( add_query_arg( [ 'page' => 'daw-user-department', 'view' => 'interview-detail', 'candidate_code' => $selected_interview['code'], 'edit' => $is_editing_result ? '1' : '' ], admin_url( 'admin.php' ) ) ) ?>" data-interview-rating>
									<div class="daw-ud-panel-heading"><div><h2>Penilaian Wawancara User</h2><p>Nilai 1–5 untuk setiap aspek. 5 = Sangat Baik.</p></div><?php if ( $is_editing_result ) : ?><span class="daw-ud-status terjadwal">Edit Hasil</span><?php endif; ?></div>
									<?php wp_nonce_field( 'daw_save_user_interview', 'daw_user_interview_nonce' ); ?><input type="hidden" name="daw_ud_action" value="save_user_interview" /><input type="hidden" name="candidate_code" value="<?= esc_attr( $selected_interview['code'] ) ?>" />
									<div class="daw-ud-rating-list">
										<?php foreach ( $rating_criteria as $key => $criterion ) : $selected_score = (int) ( $detail_scores[ $key ] ?? 0 ); ?>
											<fieldset class="daw-ud-rating-row"><legend><?= esc_html( $criterion ) ?></legend><div class="daw-ud-score-options" role="radiogroup" aria-label="<?= esc_attr( $criterion ) ?>"><?php for ( $score = 1; $score <= 5; $score++ ) : ?><label class="<?= $score === $selected_score ? 'is-selected' : '' ?>"><input type="radio" name="scores[<?= esc_attr( $key ) ?>]" value="<?= esc_attr( (string) $score ) ?>" <?= checked( $selected_score, $score, false ) ?> required /><span><?= esc_html( (string) $score ) ?></span></label><?php endfor; ?></div></fieldset>
										<?php endforeach; ?>
									</div>
									<div class="daw-ud-score-total"><span>Skor Total</span><strong><span data-score-total><?= esc_html( (string) $detail_score_total ) ?></span><small>/100</small></strong></div>
									<fieldset class="daw-ud-recommendation"><legend>Rekomendasi <b>*</b></legend><div><?php foreach ( [ 'Sangat Baik', 'Baik', 'Dipertimbangkan', 'Tidak Dilanjutkan' ] as $recommendation ) : ?><label class="<?= $posted_recommendation === $recommendation || ( '' === $posted_recommendation && ( $selected_interview['recommendation'] ?? '' ) === $recommendation ) ? 'is-selected' : '' ?>"><input type="radio" name="recommendation" value="<?= esc_attr( $recommendation ) ?>" <?= checked( $posted_recommendation ?: ( $selected_interview['recommendation'] ?? '' ), $recommendation, false ) ?> required /><span><?= esc_html( $recommendation ) ?></span></label><?php endforeach; ?></div></fieldset>
									<label class="daw-ud-notes-field"><span>Catatan Wawancara</span><textarea name="interview_notes" rows="4" placeholder="Catat observasi dan kesan umum dari proses wawancara..."><?= esc_textarea( $posted_notes ?: ( $selected_interview['notes'] ?? '' ) ) ?></textarea></label>
									<div class="daw-ud-form-actions"><a class="button" href="<?= esc_url( $view_url( 'interviews' ) ) ?>">Batal</a><button class="daw-ud-primary-button" type="submit">Simpan Hasil Wawancara</button></div>
								</form>
							<?php else : ?>
								<section class="daw-ud-detail-panel daw-ud-result-panel"><div class="daw-ud-panel-heading"><div><h2>Hasil Wawancara User</h2><p>Penilaian telah disimpan · Hanya baca</p></div><a class="daw-ud-link" href="<?= esc_url( add_query_arg( [ 'view' => 'interview-detail', 'candidate_code' => $selected_interview['code'], 'edit' => '1' ], $page_url ) ) ?>">Edit Hasil</a></div><div class="daw-ud-saved-result"><div><small>Skor Total</small><strong><?= esc_html( $selected_interview['score'] ) ?><small>/100</small></strong></div><div class="daw-ud-result-meta"><div><small>Rekomendasi</small><strong><?= esc_html( $selected_interview['recommendation'] ?? '' ) ?></strong></div><div><small>Interviewer</small><strong><?= esc_html( $stored_interview_results[ $selected_interview['code'] ]['interviewer'] ?? $display_name ) ?></strong></div><div><small>Tgl Wawancara</small><strong><?= esc_html( $selected_interview['date'] ) ?></strong></div><div><small>Disimpan</small><strong><?= esc_html( $selected_interview['saved_at'] ?? '' ) ?></strong></div></div></div><h3>Penilaian per Aspek</h3><div class="daw-ud-readonly-ratings"><?php foreach ( $rating_criteria as $key => $criterion ) : $score = (int) ( $selected_interview['scores'][ $key ] ?? 0 ); ?><div><span><?= esc_html( $criterion ) ?></span><strong><?= esc_html( (string) $score ) ?>/5</strong></div><?php endforeach; ?></div><section class="daw-ud-saved-notes"><h3>Catatan Wawancara</h3><p><?= '' !== ( $selected_interview['notes'] ?? '' ) ? nl2br( esc_html( $selected_interview['notes'] ) ) : 'Tidak ada catatan.' ?></p></section></section>
							<?php endif; ?>
						</section>
						<aside class="daw-ud-profile-panel"><div class="daw-ud-profile-title"><span class="daw-ud-candidate-avatar"><?= esc_html( strtoupper( substr( preg_replace( '/[^A-Za-z]/', '', $selected_interview['name'] ), 0, 2 ) ) ) ?></span><div><strong><?= esc_html( $selected_interview['name'] ) ?></strong><small><?= esc_html( $selected_interview['code'] ) ?><br><?= esc_html( $selected_interview['position'] ) ?> · <?= esc_html( $selected_interview['dealer'] ) ?></small></div></div>
								<h3>Data Diri</h3><dl class="daw-ud-profile-facts"><div><dt>Jenis Kelamin</dt><dd><?= esc_html( $profile['gender'] ) ?></dd></div><div><dt>Tempat/Tgl Lahir</dt><dd><?= esc_html( $profile['birth'] ) ?></dd></div><div><dt>Email</dt><dd><?= esc_html( $profile['email'] ) ?></dd></div><div><dt>Telepon</dt><dd><?= esc_html( $profile['phone'] ) ?></dd></div><div><dt>Kota</dt><dd><?= esc_html( $profile['city'] ) ?></dd></div></dl>
								<h3>Pendidikan</h3><div class="daw-ud-profile-box"><strong><?= esc_html( $profile['education'][0] ) ?></strong><span><?= esc_html( $profile['education'][1] ) ?></span><small><?= esc_html( $profile['education'][2] ) ?></small></div>
								<h3>Pengalaman Kerja</h3><?php foreach ( $profile['experience'] as $experience ) : ?><div class="daw-ud-profile-box"><strong><?= esc_html( $experience[0] ) ?></strong><span><?= esc_html( $experience[1] ) ?></span><small><?= esc_html( $experience[2] ) ?></small><p><?= esc_html( $experience[3] ) ?></p></div><?php endforeach; ?>
								<h3>Dokumen</h3><div class="daw-ud-doc-list"><?php foreach ( [ 'CV / Resume', 'KTP', 'Ijazah S1' ] as $document ) : ?><a href="#document-<?= esc_attr( sanitize_title( $document ) ) ?>"><span class="dashicons dashicons-media-document" aria-hidden="true"></span><?= esc_html( $document ) ?><b>Buka</b></a><?php endforeach; ?></div>
								<h3>Riwayat Seleksi</h3><ul class="daw-ud-selection-history"><li><span>Administrasi</span><strong>Lolos</strong></li><li><span>Psikotes</span><strong>Lolos · 82/100</strong></li><li><span>Wawancara HR</span><strong><?= esc_html( $selected_interview['hr_result'] ) ?></strong></li><li><span>Wawancara User</span><strong class="<?= $is_saved ? 'is-green' : 'is-orange' ?>"><?= esc_html( $selected_interview['status'] ) ?></strong></li></ul><div class="daw-ud-profile-callout">Direkomendasikan HR ke Departemen: <?= esc_html( $department ) ?></div><a class="daw-ud-profile-action" href="<?= esc_url( add_query_arg( [ 'view' => 'applicants', 'search' => $selected_interview['code'] ], $page_url ) ) ?>">Lihat Profil Lengkap</a><a class="daw-ud-profile-action" href="#download-profile">Download Profil Pelamar</a>
							</aside>
					</div>
				<?php endif; ?>

			<?php elseif ( 'applicants' === $view ) : ?>
				<section class="daw-ud-intro"><h2>Data Pelamar</h2><p>Data kandidat yang telah diarahkan HR ke departemen Anda. Hanya kandidat yang secara eksplisit diarahkan oleh HR yang tampil di sini.</p></section>
				<section class="daw-ud-stats daw-ud-stats--four daw-ud-applicant-stats"><article><span>Total Diarahkan</span><strong><?= esc_html( (string) count( $applicants ) ) ?></strong></article><article><span>Diterima</span><strong class="is-green"><?= esc_html( (string) $applicant_stats['accepted'] ) ?></strong></article><article><span>Ditolak</span><strong class="is-red"><?= esc_html( (string) $applicant_stats['rejected'] ) ?></strong></article><article><span>Sedang Proses</span><strong class="is-orange"><?= esc_html( (string) $applicant_stats['in_progress'] ) ?></strong></article></section>
				<form class="daw-ud-search" method="get" action="<?= esc_url( admin_url( 'admin.php' ) ) ?>"><input type="hidden" name="page" value="daw-user-department" /><input type="hidden" name="view" value="applicants" /><label><span class="screen-reader-text">Cari pelamar</span><input type="search" name="search" value="<?= esc_attr( $search ) ?>" placeholder="Cari nama, kode, atau posisi..." /></label><button class="button" type="submit">Cari</button></form>
				<div class="daw-ud-table-wrap"><table class="daw-ud-table"><thead><tr><th>Nama Pelamar</th><th>Posisi</th><th>Dealer</th><th>Tgl Masuk</th><th>Hasil HR</th><th>Hasil User</th><th>Status Final</th><th>Aksi</th></tr></thead><tbody><?php foreach ( $filtered_applicants as $applicant ) : ?><tr><td><strong><?= esc_html( $applicant['name'] ) ?></strong><small><?= esc_html( $applicant['code'] ) ?></small></td><td><?= esc_html( $applicant['position'] ) ?></td><td><?= esc_html( $applicant['dealer'] ) ?></td><td><?= esc_html( $applicant['date'] ) ?></td><td><?= esc_html( $applicant['hr_result'] ) ?></td><td><?= esc_html( $applicant['user_result'] ) ?></td><td><span class="daw-ud-status <?= esc_attr( sanitize_title( $applicant['status'] ) ) ?>"><?= esc_html( $applicant['status'] ) ?></span></td><td><a class="daw-ud-link" href="<?= esc_url( $view_url( 'interviews' ) ) ?>">Profil</a> | <a href="#download-<?= esc_attr( $applicant['code'] ) ?>">Download</a></td></tr><?php endforeach; ?><?php if ( empty( $filtered_applicants ) ) : ?><tr><td colspan="8" class="daw-ud-empty">Tidak ada pelamar yang cocok.</td></tr><?php endif; ?></tbody></table></div>
				<p class="daw-ud-info"><span class="dashicons dashicons-info-outline" aria-hidden="true"></span>Data pelamar hanya mencakup kandidat yang secara eksplisit diarahkan oleh HR. Catatan internal HR dan detail psikotes rahasia tidak ditampilkan.</p>

			<?php else : ?>
				<section class="daw-ud-intro"><h2>Hasil Wawancara User</h2><p>Rekap penilaian user interview yang telah Anda kirimkan.</p></section>
				<section class="daw-ud-stats daw-ud-stats--four daw-ud-result-stats"><article><span>Total Dinilai</span><strong><?= esc_html( (string) count( $results ) ) ?></strong></article><article><span>Direkomendasikan Terima</span><strong class="is-green"><?= esc_html( (string) $result_stats['recommended'] ) ?></strong></article><article><span>Diterima HR</span><strong class="is-green"><?= esc_html( (string) $result_stats['accepted'] ) ?></strong></article><article><span>Menunggu Keputusan</span><strong class="is-orange"><?= esc_html( (string) $result_stats['pending'] ) ?></strong></article></section>
				<div class="daw-ud-table-wrap"><table class="daw-ud-table"><thead><tr><th>Kandidat</th><th>Posisi</th><th>Tgl Wawancara</th><th>Skor</th><th>Rekomendasi Anda</th><th>Keputusan Final HR</th></tr></thead><tbody><?php foreach ( $results as $result ) : ?><tr><td><strong><?= esc_html( $result['name'] ) ?></strong><small><?= esc_html( $result['code'] ) ?></small></td><td><?= esc_html( $result['position'] ) ?></td><td><?= esc_html( $result['date'] ) ?></td><td class="score-<?= (int) $result['score'] >= 75 ? 'good' : 'low' ?>"><?= esc_html( (string) $result['score'] ) ?></td><td><span class="daw-ud-status <?= esc_attr( sanitize_title( $result['recommendation'] ) ) ?>"><?= esc_html( $result['recommendation'] ) ?></span></td><td class="decision-<?= esc_attr( sanitize_title( $result['decision'] ) ) ?>"><?= esc_html( $result['decision'] ) ?></td></tr><?php endforeach; ?></tbody></table></div>
			<?php endif; ?>
			<p class="daw-ud-demo-note">Data pada halaman ini merupakan prototipe tampilan dan belum terhubung ke data produksi.</p>
		</main>
	</div>
</div>