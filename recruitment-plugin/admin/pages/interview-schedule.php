<?php
$user = function_exists( 'wp_get_current_user' ) ? wp_get_current_user() : null;
$display_name = $user && ! empty( $user->display_name ) ? $user->display_name : 'Admin HR';
$user_email = $user && ! empty( $user->user_email ) ? $user->user_email : 'hr@daw.co.id';
$logout_url = function_exists( 'wp_logout_url' ) ? wp_logout_url( home_url( '/' ) ) : '?page=login';
$schedule_days = [
	21 => [ 'status' => 'Penuh', 'class' => 'is-full', 'slots' => 3, 'booked' => 3 ],
	22 => [ 'status' => 'Penuh', 'class' => 'is-full', 'slots' => 3, 'booked' => 3 ],
	25 => [ 'status' => 'Sebagian', 'class' => 'is-partial', 'slots' => 2, 'booked' => 1 ],
	26 => [ 'status' => 'Penuh', 'class' => 'is-full', 'slots' => 2, 'booked' => 2 ],
];
$calendar_days = array_merge( array_fill( 0, 2, null ), range( 1, 30 ) );
while ( 0 !== count( $calendar_days ) % 7 ) {
	$calendar_days[] = null;
}
?>
<div class="daw-user-interview daw-interview-schedule">
	<header class="daw-user-interview__header">
		<nav class="daw-user-interview__breadcrumb" aria-label="Breadcrumb">
			<span class="daw-user-interview__breadcrumb-root">DAW Admin</span>
			<span aria-hidden="true">/</span>
			<strong>Jadwal Wawancara</strong>
		</nav>
		<div class="daw-user-interview__account">
			<span class="daw-user-interview__avatar" aria-hidden="true">HR</span>
			<span class="daw-user-interview__account-name"><strong><?= esc_html( $display_name ) ?></strong><small><?= esc_html( $user_email ) ?></small></span>
			<a class="daw-user-interview__logout" href="<?= esc_url( $logout_url ) ?>">Keluar</a>
		</div>
	</header>

	<main class="daw-schedule__main">
		<div class="daw-schedule__intro">
			<div>
				<h1>September 2026</h1>
				<p>Klik tanggal untuk memilih, lalu atur slot waktu wawancara secara massal.</p>
			</div>
			<div class="daw-schedule__legend" aria-label="Status jadwal">
				<span><i class="is-available" aria-hidden="true"></i>Tersedia</span>
				<span><i class="is-partial" aria-hidden="true"></i>Sebagian</span>
				<span><i class="is-full" aria-hidden="true"></i>Penuh</span>
				<span><i class="is-selected" aria-hidden="true"></i>Dipilih</span>
			</div>
		</div>
		<div class="daw-schedule__layout">
			<section class="daw-schedule__calendar" aria-label="Kalender September 2026">
				<div class="daw-schedule__weekdays" aria-hidden="true">
					<?php foreach ( [ 'Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab' ] as $weekday ) : ?>
						<span><?= esc_html( $weekday ) ?></span>
					<?php endforeach; ?>
				</div>
				<div class="daw-schedule__days">
					<?php foreach ( $calendar_days as $day ) : ?>
						<div class="daw-schedule__cell">
							<?php if ( null === $day ) : ?>
								<span class="daw-schedule__blank" aria-hidden="true"></span>
							<?php elseif ( isset( $schedule_days[ $day ] ) ) :
								$schedule = $schedule_days[ $day ];
								?>
								<button class="daw-schedule__date <?= esc_attr( $schedule['class'] ) ?>" type="button" data-schedule-date="<?= esc_attr( $day ) ?>" aria-pressed="false" aria-label="<?= esc_attr( $day . ' September 2026, ' . $schedule['status'] . ', ' . $schedule['booked'] . ' dari ' . $schedule['slots'] . ' slot terisi' ) ?>"><?= esc_html( $day ) ?></button>
							<?php else : ?>
								<button class="daw-schedule__date" type="button" data-schedule-date="<?= esc_attr( $day ) ?>" aria-pressed="false" aria-label="<?= esc_attr( $day . ' September 2026' ) ?>"><?= esc_html( $day ) ?></button>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</section>

			<aside class="daw-schedule__saved" aria-label="Jadwal tersimpan">
				<header class="daw-schedule__saved-heading"><h2>Jadwal Tersimpan</h2><span><?= esc_html( count( $schedule_days ) ) ?> tanggal</span></header>
				<?php foreach ( $schedule_days as $day => $schedule ) : ?>
					<details class="daw-schedule__entry" id="schedule-day-<?= esc_attr( $day ) ?>">
						<summary>
							<span class="daw-schedule__entry-date"><strong><?= esc_html( $day . ' Sep 2026' ) ?></strong><small><?= esc_html( $schedule['slots'] . ' slot · ' . $schedule['booked'] . '/' . $schedule['slots'] . ' terisi' ) ?></small></span>
							<span class="daw-schedule__entry-status <?= esc_attr( $schedule['class'] ) ?>"><?= esc_html( $schedule['status'] ) ?></span>
							<span class="daw-schedule__chevron" aria-hidden="true"></span>
						</summary>
						<p><?= esc_html( $schedule['booked'] . ' dari ' . $schedule['slots'] . ' slot wawancara telah terisi.' ) ?></p>
					</details>
				<?php endforeach; ?>
			</aside>
		</div>
	</main>
</div>