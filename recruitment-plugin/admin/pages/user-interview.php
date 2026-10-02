<?php
$user = function_exists( 'wp_get_current_user' ) ? wp_get_current_user() : null;
$display_name = $user && ! empty( $user->display_name ) ? $user->display_name : 'Admin HR';
$user_email = $user && ! empty( $user->user_email ) ? $user->user_email : 'hr@daw.co.id';
$logout_url = function_exists( 'wp_logout_url' ) ? wp_logout_url( home_url( '/' ) ) : '?page=login';
$list_url = recruitment_get_admin_url( 'user-interview' );
$department_filter = sanitize_text_field( wp_unslash( $_GET['department'] ?? '' ) );
$result_filter = sanitize_text_field( wp_unslash( $_GET['result'] ?? '' ) );
$search = sanitize_text_field( wp_unslash( $_GET['search'] ?? '' ) );

$interviews = [
	[ 'id' => 1, 'name' => 'Sari Dewi Putri', 'code' => 'DW-2026-0761', 'position' => 'Customer Service', 'department' => 'Sales', 'dealer' => 'DAW Airmadidi', 'result' => 'Sangat Baik', 'final' => 'Diterima', 'date' => '20 Sep 2026', 'interviewer' => 'Andi Wirawan', 'note' => 'Komunikasi sangat baik, pengalaman relevan, antusias dan target-oriented.' ],
	[ 'id' => 2, 'name' => 'Rizal Fauzy', 'code' => 'DW-2026-0755', 'position' => 'Customer Service', 'department' => 'Sales', 'dealer' => 'DAW Manado', 'result' => 'Dipertimbangkan', 'final' => 'Tidak Diterima', 'date' => '19 Sep 2026', 'interviewer' => 'Andi Wirawan', 'note' => 'Perlu penguatan pada pemahaman kebutuhan pelanggan dan konsistensi komunikasi.' ],
	[ 'id' => 3, 'name' => 'Mega Lestari', 'code' => 'DW-2026-0749', 'position' => 'Sales Supervisor', 'department' => 'Sales', 'dealer' => 'DAW Kotamobagu', 'result' => 'Baik', 'final' => 'Menunggu Keputusan', 'date' => '18 Sep 2026', 'interviewer' => 'Andi Wirawan', 'note' => 'Memiliki pengalaman memimpin tim. Perlu pembahasan akhir terkait penempatan.' ],
	[ 'id' => 4, 'name' => 'Doni Prasetyo', 'code' => 'DW-2026-0731', 'position' => 'Sales Executive', 'department' => 'Sales', 'dealer' => 'DAW Airmadidi', 'result' => 'Dipilih', 'final' => 'Diterima', 'date' => '12 Sep 2026', 'interviewer' => 'Andi Wirawan', 'note' => 'Menunjukkan kesiapan kerja dan pemahaman target penjualan.' ],
	[ 'id' => 5, 'name' => 'Benny Kurniawan', 'code' => 'DW-2026-0718', 'position' => 'Service Advisor', 'department' => 'After Sales', 'dealer' => 'DAW Manado', 'result' => 'Tidak Dilanjutkan', 'final' => 'Tidak Diterima', 'date' => '10 Sep 2026', 'interviewer' => 'Benny Kurnia', 'note' => 'Kompetensi teknis belum sesuai dengan kebutuhan posisi saat ini.' ],
	[ 'id' => 6, 'name' => 'Nabila Yusuf', 'code' => 'DW-2026-0788', 'position' => 'Finance Staff', 'department' => 'Finance', 'dealer' => 'DAW Manado', 'result' => 'Baik', 'final' => 'Menunggu Keputusan', 'date' => '21 Sep 2026', 'interviewer' => 'Meity Rondonuwu', 'note' => 'Teliti dan memahami proses administrasi keuangan. Menunggu keputusan akhir departemen.' ],
	[ 'id' => 7, 'name' => 'Yosefin Langi', 'code' => 'DW-2025-0612', 'position' => 'Finance Staff', 'department' => 'Finance', 'dealer' => 'DAW Manado', 'result' => 'Baik', 'final' => 'Diterima', 'date' => '24 Nov 2025', 'interviewer' => 'Meity Rondonuwu', 'note' => 'Menunjukkan ketelitian, integritas, dan kecocokan yang baik dengan tim Finance.' ],
];

$departments = [ 'Sales', 'After Sales', 'Finance' ];
$results = [ 'Sangat Baik', 'Baik', 'Dipertimbangkan', 'Tidak Dilanjutkan' ];
$filtered_interviews = [];
foreach ( $interviews as $interview ) {
	$matches_department = '' === $department_filter || $interview['department'] === $department_filter;
	$matches_result = '' === $result_filter || $interview['result'] === $result_filter;
	$searchable = strtolower( $interview['name'] . ' ' . $interview['code'] . ' ' . $interview['position'] . ' ' . $interview['dealer'] );
	$matches_search = '' === $search || false !== strpos( $searchable, strtolower( $search ) );
	if ( $matches_department && $matches_result && $matches_search ) {
		$filtered_interviews[] = $interview;
	}
}

$stats = [ 'total' => count( $filtered_interviews ), 'good' => 0, 'accepted' => 0, 'waiting' => 0 ];
foreach ( $filtered_interviews as $interview ) {
	if ( in_array( $interview['result'], [ 'Sangat Baik', 'Baik', 'Dipilih' ], true ) ) {
		$stats['good']++;
	}
	if ( 'Diterima' === $interview['final'] ) {
		$stats['accepted']++;
	}
	if ( 'Menunggu Keputusan' === $interview['final'] ) {
		$stats['waiting']++;
	}
}

$filter_url = static function ( string $department, string $result ) use ( $list_url, $search ): string {
	return add_query_arg( [ 'department' => $department, 'result' => $result, 'search' => $search ], $list_url );
};
?>
<div class="daw-user-interview">
	<header class="daw-user-interview__header">
		<nav class="daw-user-interview__breadcrumb" aria-label="Breadcrumb">
			<span class="daw-user-interview__breadcrumb-root">DAW Admin</span>
			<span aria-hidden="true">/</span>
			<strong>Detail Hasil Wawancara Departemen</strong>
		</nav>
		<div class="daw-user-interview__account">
			<span class="daw-user-interview__avatar" aria-hidden="true">HR</span>
			<span class="daw-user-interview__account-name"><strong><?= esc_html( $display_name ) ?></strong><small><?= esc_html( $user_email ) ?></small></span>
			<a class="daw-user-interview__logout" href="<?= esc_url( $logout_url ) ?>">Keluar</a>
		</div>
	</header>

	<main class="daw-user-interview__main">
		<section class="daw-ui-heading">
			<h1>Detail Hasil Wawancara Departemen</h1>
			<p>Rekap hasil wawancara user yang telah disampaikan oleh Kepala Departemen. Data ini hanya untuk review HR — bukan tempat HR melakukan wawancara user.</p>
		</section>

		<section class="daw-ui-stats" aria-label="Ringkasan wawancara departemen">
			<article><span>Total Diwawancarai</span><strong><?= esc_html( number_format_i18n( $stats['total'] ) ) ?></strong></article>
			<article><span>Sangat Baik / Baik</span><strong class="is-good"><?= esc_html( number_format_i18n( $stats['good'] ) ) ?></strong></article>
			<article><span>Diterima</span><strong class="is-accepted"><?= esc_html( number_format_i18n( $stats['accepted'] ) ) ?></strong></article>
			<article><span>Menunggu Keputusan</span><strong class="is-waiting"><?= esc_html( number_format_i18n( $stats['waiting'] ) ) ?></strong></article>
		</section>

		<form method="get" class="daw-ui-search" action="<?= esc_url( admin_url( 'admin.php' ) ) ?>">
			<input type="hidden" name="page" value="recruitment-user-interview" />
			<input type="hidden" name="department" value="<?= esc_attr( $department_filter ) ?>" />
			<input type="hidden" name="result" value="<?= esc_attr( $result_filter ) ?>" />
			<label><span class="screen-reader-text">Cari kandidat</span><input type="search" name="search" value="<?= esc_attr( $search ) ?>" placeholder="Cari nama atau kode lamaran..." /></label>
			<button class="button" type="submit">Cari</button>
			<a class="button" href="<?= esc_url( $list_url ) ?>">Reset</a>
		</form>

		<div class="daw-ui-filters" aria-label="Filter kandidat">
			<div class="daw-ui-filter-group" aria-label="Filter departemen">
				<?php foreach ( array_merge( [ 'Semua' => '' ], array_combine( $departments, $departments ) ) as $label => $value ) : ?>
					<a href="<?= esc_url( $filter_url( $value, $result_filter ) ) ?>" class="<?= $department_filter === $value ? 'is-active' : '' ?>"><?= esc_html( $label ) ?></a>
				<?php endforeach; ?>
			</div>
			<div class="daw-ui-filter-group" aria-label="Filter hasil wawancara">
				<?php foreach ( array_merge( [ 'Semua' => '' ], array_combine( $results, $results ) ) as $label => $value ) : ?>
					<a href="<?= esc_url( $filter_url( $department_filter, $value ) ) ?>" class="<?= $result_filter === $value ? 'is-active' : '' ?>"><?= esc_html( $label ) ?></a>
				<?php endforeach; ?>
			</div>
		</div>

		<section class="daw-ui-list" aria-label="Hasil wawancara kandidat">
			<?php if ( empty( $filtered_interviews ) ) : ?>
				<p class="daw-ui-empty">Tidak ada hasil wawancara yang cocok dengan filter.</p>
			<?php endif; ?>
			<?php foreach ( $filtered_interviews as $index => $interview ) :
				$result_class = 'Tidak Dilanjutkan' === $interview['result'] ? 'is-red' : ( 'Dipertimbangkan' === $interview['result'] ? 'is-amber' : ( 'Baik' === $interview['result'] ? 'is-blue' : 'is-green' ) );
				$final_class = 'Diterima' === $interview['final'] ? 'is-green' : ( 'Tidak Diterima' === $interview['final'] ? 'is-red' : 'is-amber' );
				$initials = strtoupper( substr( preg_replace( '/[^A-Za-z]/', '', $interview['name'] ), 0, 2 ) );
				$final_decision_url = add_query_arg( [ 'page' => 'recruitment-dashboard', 'view' => 'final-decision', 'section' => 'decision', 'candidate_id' => $interview['id'] ], admin_url( 'admin.php' ) );
				?>
				<details class="daw-ui-candidate" <?= 0 === $index ? 'open' : '' ?>>
					<summary>
						<span class="daw-ui-avatar" aria-hidden="true"><?= esc_html( $initials ) ?></span>
						<span class="daw-ui-candidate__identity"><strong><?= esc_html( $interview['name'] ) ?></strong><small><?= esc_html( $interview['code'] ) ?> · <?= esc_html( $interview['position'] ) ?> · <?= esc_html( $interview['department'] ) ?> · <?= esc_html( $interview['dealer'] ) ?></small></span>
						<span class="daw-ui-candidate__badges"><span class="daw-ui-badge <?= esc_attr( $result_class ) ?>"><?= esc_html( $interview['result'] ) ?></span><span class="daw-ui-badge <?= esc_attr( $final_class ) ?>">Final: <?= esc_html( 'Menunggu Keputusan' === $interview['final'] ? 'Pending' : ( 'Diterima' === $interview['final'] ? 'Diterima' : 'Ditolak' ) ) ?></span></span>
						<span class="daw-ui-interviewer"><small>Interviewer</small><strong><?= esc_html( $interview['interviewer'] ) ?></strong><small><?= esc_html( $interview['date'] ) ?></small></span>
						<span class="daw-ui-chevron" aria-hidden="true"></span>
					</summary>
					<div class="daw-ui-candidate__details">
						<div class="daw-ui-facts">
							<div><span>Departemen</span><strong><?= esc_html( $interview['department'] ) ?></strong></div>
							<div><span>Interviewer</span><strong><?= esc_html( $interview['interviewer'] ) ?></strong></div>
							<div><span>Tanggal Wawancara</span><strong><?= esc_html( $interview['date'] ) ?></strong></div>
							<div><span>Hasil</span><strong><span class="daw-ui-badge <?= esc_attr( $result_class ) ?>"><?= esc_html( $interview['result'] ) ?></span></strong></div>
						</div>
						<div class="daw-ui-note"><span>Catatan Wawancara Departemen</span><p>“<?= esc_html( $interview['note'] ) ?>”</p></div>
						<div class="daw-ui-detail-actions"><a class="daw-ui-decision-link" href="<?= esc_url( $final_decision_url ) ?>"><span class="dashicons dashicons-yes-alt" aria-hidden="true"></span>Buat Keputusan Final</a></div>
					</div>
				</details>
			<?php endforeach; ?>
		</section>
	</main>
</div>
<?php if ( defined( 'RECRUITMENT_SANDBOX' ) ) : ?>
	<?php require recruitment_get_plugin_path( 'public/components/screen-explorer.php' ); ?>
<?php endif; ?>
