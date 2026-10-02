<?php
$current_user = function_exists( 'wp_get_current_user' ) ? wp_get_current_user() : null;
$display_name = $current_user && ! empty( $current_user->display_name ) ? $current_user->display_name : 'Andi Wirawan';
$department = $current_user && ! empty( $current_user->ID ) && function_exists( 'get_user_meta' )
	? (string) get_user_meta( $current_user->ID, 'department', true )
	: '';
$department = '' !== $department ? $department : 'Sales';
$view = sanitize_key( wp_unslash( $_GET['view'] ?? 'dashboard' ) );
$views = [ 'dashboard', 'requests', 'interviews', 'applicants', 'results' ];
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
	'applicants' => 'Data Pelamar',
	'results' => 'Hasil Wawancara User',
];
$request_filter = sanitize_text_field( wp_unslash( $_GET['status'] ?? '' ) );
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

$request_statuses = [ 'Semua', 'Draft', 'Perlu Revisi', 'Review HR', 'Disetujui', 'Lowongan Dibuka' ];
$request_labels = [ 'Semua' => '', 'Draft' => 'Draft', 'Perlu Revisi' => 'Perlu Revisi', 'Review HR' => 'Review HR', 'Disetujui' => 'Disetujui', 'Lowongan Dibuka' => 'Lowongan Dibuka' ];
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
	if ( $matches_search ) {
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
	++$interview_stats[ [ 'Menunggu Jadwal' => 'waiting', 'Terjadwal' => 'scheduled', 'Sudah Dinilai' => 'done' ][ $interview['status'] ] ];
}
$greeting_name = trim( (string) preg_replace( '/\s+.*/', '', $display_name ) );
?>
<div class="daw-user-department">
	<aside class="daw-ud-sidebar" aria-label="Navigasi User Department">
		<a class="daw-ud-brand" href="<?= esc_url( $view_url( 'dashboard' ) ) ?>">
			<span class="daw-ud-brand__mark">D</span>
			<span><strong>Daya Adicipta Wisesa</strong><small>User Department</small></span>
		</a>
		<div class="daw-ud-identity"><span>User Department</span><strong><?= esc_html( $department ) ?> — <?= esc_html( $display_name ) ?></strong></div>
		<nav class="daw-ud-nav" aria-label="Menu User Department">
			<a class="<?= 'dashboard' === $view ? 'is-active' : '' ?>" href="<?= esc_url( $view_url( 'dashboard' ) ) ?>"><span class="dashicons dashicons-dashboard" aria-hidden="true"></span>Dashboard</a>
			<a class="<?= 'requests' === $view ? 'is-active' : '' ?>" href="<?= esc_url( $view_url( 'requests' ) ) ?>"><span class="dashicons dashicons-clipboard" aria-hidden="true"></span>Request Lowongan</a>
			<a class="<?= 'interviews' === $view ? 'is-active' : '' ?>" href="<?= esc_url( $view_url( 'interviews' ) ) ?>"><span class="dashicons dashicons-groups" aria-hidden="true"></span><span>Kandidat Wawancara</span><b>5</b></a>
			<a class="<?= 'applicants' === $view ? 'is-active' : '' ?>" href="<?= esc_url( $view_url( 'applicants' ) ) ?>"><span class="dashicons dashicons-database" aria-hidden="true"></span>Data Pelamar</a>
			<a class="<?= 'results' === $view ? 'is-active' : '' ?>" href="<?= esc_url( $view_url( 'results' ) ) ?>"><span class="dashicons dashicons-list-view" aria-hidden="true"></span>Hasil Wawancara User</a>
		</nav>
		<div class="daw-ud-sidebar__footer"><span class="dashicons dashicons-building" aria-hidden="true"></span><?= esc_html( $department ) ?> Department</div>
	</aside>

	<div class="daw-ud-workspace">
		<header class="daw-ud-topbar"><h1><?= esc_html( $view_titles[ $view ] ) ?></h1><div><span class="daw-ud-user-avatar">UD</span><span><strong><?= esc_html( $display_name ) ?></strong><small><?= esc_html( $department ) ?> Department</small></span></div></header>
		<main class="daw-ud-main">
			<?php if ( 'dashboard' === $view ) : ?>
				<section class="daw-ud-welcome"><p>Selamat datang, <strong><?= esc_html( $display_name ) ?></strong></p><span>Departemen <?= esc_html( $department ) ?> — <?= esc_html( date_i18n( 'l, j F Y' ) ) ?></span></section>
				<section class="daw-ud-section">
					<div class="daw-ud-section__heading"><h2>Permintaan Lowongan</h2><a href="<?= esc_url( $view_url( 'requests' ) ) ?>">Lihat Semua</a></div>
					<div class="daw-ud-stats daw-ud-stats--four">
						<article><span>Draft</span><strong>2</strong></article><article><span>Menunggu Review HR</span><strong class="is-orange">3</strong></article><article><span>Perlu Revisi</span><strong class="is-red">1</strong><small>Perlu tindakan</small></article><article><span>Disetujui</span><strong class="is-green">5</strong></article>
					</div>
				</section>
				<section class="daw-ud-section">
					<div class="daw-ud-section__heading"><h2>Kandidat User Interview</h2><a href="<?= esc_url( $view_url( 'interviews' ) ) ?>">Lihat Semua</a></div>
					<div class="daw-ud-stats daw-ud-stats--three">
						<article><span>Menunggu Jadwal</span><strong class="is-orange"><?= esc_html( (string) $interview_stats['waiting'] ) ?></strong></article><article><span>Terjadwal</span><strong><?= esc_html( (string) $interview_stats['scheduled'] ) ?></strong></article><article><span>Sudah Dinilai</span><strong class="is-green"><?= esc_html( (string) $interview_stats['done'] ) ?></strong></article>
					</div>
				</section>
				<section class="daw-ud-section daw-ud-activity-section"><h2>Aktivitas Terkini</h2><div class="daw-ud-activity">
					<div><i class="is-green"></i><span>Request FPTK-2026-031 disetujui HR<small>22 Sep 2026</small></span></div>
					<div><i class="is-blue"></i><span>Kandidat Budi Santoso terjadwal wawancara user — 24 Sep 2026<small>20 Sep 2026</small></span></div>
					<div><i class="is-orange"></i><span>Request FPTK-2026-029 perlu revisi — lihat catatan HR<small>18 Sep 2026</small></span></div>
					<div><i class="is-green"></i><span>Hasil wawancara Aiko Hanako berhasil dikirim<small>15 Sep 2026</small></span></div>
				</div></section>

			<?php elseif ( 'requests' === $view ) : ?>
				<div class="daw-ud-page-heading"><div><p>Total <?= esc_html( (string) count( $requests ) ) ?> permintaan</p></div><button type="button" class="daw-ud-primary-button"><span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span>Buat Request Baru</button></div>
				<nav class="daw-ud-pills" aria-label="Filter status request">
					<?php foreach ( $request_labels as $label => $value ) : ?><a class="<?= $request_filter === $value ? 'is-active' : '' ?>" href="<?= esc_url( add_query_arg( [ 'status' => $value ], $view_url( 'requests' ) ) ) ?>"><?= esc_html( $label ) ?></a><?php endforeach; ?>
				</nav>
				<?php if ( '' === $request_filter || 'Perlu Revisi' === $request_filter ) : ?><div class="daw-ud-alert"><span class="dashicons dashicons-warning" aria-hidden="true"></span><span><strong>Ada permintaan yang perlu direvisi</strong><small>FPTK-2026-029: Mohon lengkapi profil kandidat minimum pendidikan.</small></span></div><?php endif; ?>
				<form class="daw-ud-search" method="get" action="<?= esc_url( admin_url( 'admin.php' ) ) ?>"><input type="hidden" name="page" value="daw-user-department" /><input type="hidden" name="view" value="requests" /><input type="hidden" name="status" value="<?= esc_attr( $request_filter ) ?>" /><label><span class="screen-reader-text">Cari request</span><input type="search" name="search" value="<?= esc_attr( $search ) ?>" placeholder="Cari nomor FPTK atau posisi..." /></label><button class="button" type="submit">Cari</button></form>
				<div class="daw-ud-table-wrap"><table class="daw-ud-table"><thead><tr><th>No. FPTK</th><th>Posisi</th><th>Jml</th><th>Prioritas</th><th>Tgl Pengajuan</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
				<?php if ( empty( $filtered_requests ) ) : ?><tr><td colspan="7" class="daw-ud-empty">Tidak ada request yang cocok.</td></tr><?php endif; ?>
				<?php foreach ( $filtered_requests as $request ) : ?><tr><td><strong><?= esc_html( $request['code'] ) ?></strong></td><td><?= esc_html( $request['position'] ) ?></td><td><?= esc_html( (string) $request['count'] ) ?></td><td class="priority-<?= esc_attr( strtolower( $request['priority'] ) ) ?>"><?= esc_html( $request['priority'] ) ?></td><td><?= esc_html( $request['date'] ) ?></td><td><span class="daw-ud-status <?= esc_attr( sanitize_title( $request['status'] ) ) ?>"><?= esc_html( $request['status'] ) ?></span></td><td><a class="daw-ud-link" href="#request-<?= esc_attr( $request['code'] ) ?>">Lihat</a></td></tr><?php endforeach; ?>
				</tbody></table></div>

			<?php elseif ( 'interviews' === $view ) : ?>
				<section class="daw-ud-intro"><h2>Kandidat Wawancara User</h2><p>Kandidat yang telah melewati HR Interview dan diarahkan HR ke departemen Anda.</p></section>
				<nav class="daw-ud-pills" aria-label="Filter status wawancara"><a class="is-active" href="<?= esc_url( $view_url( 'interviews' ) ) ?>">Semua <span><?= esc_html( (string) count( $interviews ) ) ?></span></a><a href="#waiting">Menunggu Jadwal <span><?= esc_html( (string) $interview_stats['waiting'] ) ?></span></a><a href="#scheduled">Terjadwal <span><?= esc_html( (string) $interview_stats['scheduled'] ) ?></span></a><a href="#done">Sudah Dinilai <span><?= esc_html( (string) $interview_stats['done'] ) ?></span></a></nav>
				<form class="daw-ud-search" method="get" action="<?= esc_url( admin_url( 'admin.php' ) ) ?>"><input type="hidden" name="page" value="daw-user-department" /><input type="hidden" name="view" value="interviews" /><label><span class="screen-reader-text">Cari kandidat wawancara</span><input type="search" name="search" value="<?= esc_attr( $search ) ?>" placeholder="Cari nama, kode, atau posisi..." /></label><button class="button" type="submit">Cari</button></form>
				<div class="daw-ud-interview-list">
				<?php foreach ( $filtered_interviews as $interview ) : $status_class = 'Sudah Dinilai' === $interview['status'] ? 'done' : ( 'Terjadwal' === $interview['status'] ? 'scheduled' : 'waiting' ); ?>
					<article class="daw-ud-interview-card" id="<?= esc_attr( $status_class ) ?>">
						<span class="daw-ud-candidate-avatar"><?= esc_html( strtoupper( substr( preg_replace( '/[^A-Za-z]/', '', $interview['name'] ), 0, 2 ) ) ) ?></span>
						<div class="daw-ud-interview-card__candidate"><strong><?= esc_html( $interview['name'] ) ?></strong><span class="daw-ud-status <?= esc_attr( sanitize_title( $interview['status'] ) ) ?>"><?= esc_html( $interview['status'] ) ?></span><small><?= esc_html( $interview['code'] ) ?> · <?= esc_html( $interview['position'] ) ?> · <?= esc_html( $interview['dealer'] ) ?></small></div>
						<div class="daw-ud-interview-card__meta"><span>Hasil HR<strong><?= esc_html( $interview['hr_result'] ) ?></strong></span><?php if ( '' !== $interview['score'] ) : ?><span>Nilai<strong class="is-green"><?= esc_html( $interview['score'] ) ?></strong></span><?php endif; ?></div>
						<div class="daw-ud-interview-card__date"><small>Jadwal</small><strong><?= esc_html( $interview['date'] ) ?></strong></div>
						<?php if ( 'Terjadwal' === $interview['status'] ) : ?><button type="button" class="daw-ud-primary-button">Mulai Wawancara</button><?php elseif ( 'Sudah Dinilai' === $interview['status'] ) : ?><a class="button" href="<?= esc_url( $view_url( 'results' ) ) ?>">Lihat Hasil</a><?php else : ?><button type="button" class="button">Lihat Detail</button><?php endif; ?>
					</article>
				<?php endforeach; ?>
				</div>

			<?php elseif ( 'applicants' === $view ) : ?>
				<section class="daw-ud-intro"><h2>Data Pelamar</h2><p>Data kandidat yang telah diarahkan HR ke departemen Anda. Hanya kandidat yang secara eksplisit diarahkan oleh HR yang tampil di sini.</p></section>
				<section class="daw-ud-stats daw-ud-stats--four daw-ud-applicant-stats"><article><span>Total Diarahkan</span><strong><?= esc_html( (string) count( $applicants ) ) ?></strong></article><article><span>Diterima</span><strong class="is-green">2</strong></article><article><span>Ditolak</span><strong class="is-red">1</strong></article><article><span>Sedang Proses</span><strong class="is-orange">3</strong></article></section>
				<form class="daw-ud-search" method="get" action="<?= esc_url( admin_url( 'admin.php' ) ) ?>"><input type="hidden" name="page" value="daw-user-department" /><input type="hidden" name="view" value="applicants" /><label><span class="screen-reader-text">Cari pelamar</span><input type="search" name="search" value="<?= esc_attr( $search ) ?>" placeholder="Cari nama, kode, atau posisi..." /></label><button class="button" type="submit">Cari</button></form>
				<div class="daw-ud-table-wrap"><table class="daw-ud-table"><thead><tr><th>Nama Pelamar</th><th>Posisi</th><th>Dealer</th><th>Tgl Masuk</th><th>Hasil HR</th><th>Hasil User</th><th>Status Final</th><th>Aksi</th></tr></thead><tbody>
				<?php foreach ( $filtered_applicants as $applicant ) : ?><tr><td><strong><?= esc_html( $applicant['name'] ) ?></strong><small><?= esc_html( $applicant['code'] ) ?></small></td><td><?= esc_html( $applicant['position'] ) ?></td><td><?= esc_html( $applicant['dealer'] ) ?></td><td><?= esc_html( $applicant['date'] ) ?></td><td><?= esc_html( $applicant['hr_result'] ) ?></td><td><?= esc_html( $applicant['user_result'] ) ?></td><td><span class="daw-ud-status <?= esc_attr( sanitize_title( $applicant['status'] ) ) ?>"><?= esc_html( $applicant['status'] ) ?></span></td><td><a class="daw-ud-link" href="<?= esc_url( $view_url( 'interviews' ) ) ?>">Profil</a></td></tr><?php endforeach; ?>
				<?php if ( empty( $filtered_applicants ) ) : ?><tr><td colspan="8" class="daw-ud-empty">Tidak ada pelamar yang cocok.</td></tr><?php endif; ?>
				</tbody></table></div><p class="daw-ud-info"><span class="dashicons dashicons-info-outline" aria-hidden="true"></span>Data pelamar hanya mencakup kandidat yang secara eksplisit diarahkan oleh HR. Catatan internal HR dan detail psikotes rahasia tidak ditampilkan.</p>

			<?php else : ?>
				<section class="daw-ud-intro"><h2>Hasil Wawancara User</h2><p>Rekap penilaian user interview yang telah Anda kirimkan.</p></section>
				<section class="daw-ud-stats daw-ud-stats--four daw-ud-result-stats"><article><span>Total Dinilai</span><strong><?= esc_html( (string) count( $results ) ) ?></strong></article><article><span>Direkomendasikan Terima</span><strong class="is-green">3</strong></article><article><span>Diterima HR</span><strong class="is-green">2</strong></article><article><span>Menunggu Keputusan</span><strong class="is-orange">1</strong></article></section>
				<div class="daw-ud-table-wrap"><table class="daw-ud-table"><thead><tr><th>Kandidat</th><th>Posisi</th><th>Tgl Wawancara</th><th>Skor</th><th>Rekomendasi Anda</th><th>Keputusan Final HR</th></tr></thead><tbody>
				<?php foreach ( $results as $result ) : ?><tr><td><strong><?= esc_html( $result['name'] ) ?></strong><small><?= esc_html( $result['code'] ) ?></small></td><td><?= esc_html( $result['position'] ) ?></td><td><?= esc_html( $result['date'] ) ?></td><td class="score-<?= (int) $result['score'] >= 75 ? 'good' : 'low' ?>"><?= esc_html( (string) $result['score'] ) ?></td><td><span class="daw-ud-status <?= esc_attr( sanitize_title( $result['recommendation'] ) ) ?>"><?= esc_html( $result['recommendation'] ) ?></span></td><td class="decision-<?= esc_attr( sanitize_title( $result['decision'] ) ) ?>"><?= esc_html( $result['decision'] ) ?></td></tr><?php endforeach; ?>
				</tbody></table></div>
			<?php endif; ?>
			<p class="daw-ud-demo-note">Data pada halaman ini merupakan prototipe tampilan dan belum terhubung ke data produksi.</p>
		</main>
	</div>
</div>