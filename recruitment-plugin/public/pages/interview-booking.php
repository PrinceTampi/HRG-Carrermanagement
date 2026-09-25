<?php
$token = sanitize_text_field( wp_unslash( $_GET['token'] ?? '' ) );
$preview = '1' === sanitize_text_field( wp_unslash( $_GET['daw_ui_preview'] ?? '' ) );
$step = sanitize_key( $_POST['step'] ?? $_GET['step'] ?? 'dates' );
$step = in_array( $step, [ 'dates', 'slots', 'summary', 'confirm', 'success' ], true ) ? $step : 'dates';
$selected_date = sanitize_text_field( wp_unslash( $_POST['interview_date'] ?? $_GET['interview_date'] ?? '' ) );
$selected_slot = sanitize_text_field( wp_unslash( $_POST['interview_slot'] ?? $_GET['interview_slot'] ?? '' ) );
$database = new Recruitment_Database();
$application = '' !== $token ? $database->find_application_by_token( $token ) : null;
if ( $preview && 'DAW-PREVIEW-001' === $token ) {
    $application = [ 'id' => 'DAW-2026-00123', 'token' => $token, 'name' => 'John Doe', 'job_title' => 'Sales Counter', 'status' => 'submitted' ];
}
$booking_error = '';
$posted_slot = sanitize_text_field( wp_unslash( $_POST['interview_slot'] ?? '' ) );
if ( $application && 'confirm_booking' === ( $_POST['action'] ?? '' ) && '' !== $posted_slot ) {
    [ $posted_start, $posted_end ] = array_pad( explode( '-', $posted_slot, 2 ), 2, '' );
    $saved_booking = $database->save_interview_booking( $token, $selected_date, $posted_start, $posted_end );
    if ( ! empty( $saved_booking['success'] ) ) {
        $step = 'success';
    } else {
        $booking_error = (string) ( $saved_booking['error'] ?? 'Booking jadwal gagal disimpan. Silakan coba lagi.' );
    }
}
$availability = $application ? $database->get_interview_availability( $token ) : [];
$availability_error = $database->has_error();
$booking = $application ? $database->get_interview_booking( $token ) : null;
if ( $preview && $application && ! $availability ) {
    $availability = [
        [ 'date' => '2026-10-10', 'status' => 'available', 'slots' => [ [ 'start' => '09:00', 'end' => '09:30', 'status' => 'available' ], [ 'start' => '09:30', 'end' => '10:00', 'status' => 'available' ], [ 'start' => '10:00', 'end' => '10:30', 'status' => 'full' ], [ 'start' => '10:30', 'end' => '11:00', 'status' => 'available' ] ] ],
        [ 'date' => '2026-10-11', 'status' => 'available', 'slots' => [ [ 'start' => '09:00', 'end' => '09:30', 'status' => 'available' ] ] ],
        [ 'date' => '2026-10-12', 'status' => 'full', 'slots' => [ [ 'start' => '09:00', 'end' => '09:30', 'status' => 'full' ] ] ],
    ];
}
$selected_day = null;
foreach ( $availability as $day ) {
    if ( $selected_date === (string) ( $day['date'] ?? '' ) ) {
        $selected_day = $day;
        break;
    }
}
$selected_slot_data = null;
if ( $selected_day ) {
    foreach ( $selected_day['slots'] ?? [] as $slot ) {
        if ( $selected_slot === (string) ( $slot['start'] ?? '' ) . '-' . (string) ( $slot['end'] ?? '' ) ) {
            $selected_slot_data = $slot;
            break;
        }
    }
}
$format_date = static function ( string $date ): string {
    $timestamp = strtotime( $date );
    return $timestamp ? date( 'd F Y', $timestamp ) : $date;
};
$format_time = static function ( string $time ): string {
    return str_replace( ':', '.', $time );
};
$day_names = [ 'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu' ];
$month_names = [ 'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember' ];
$format_day = static fn ( string $date ): string => $day_names[ date( 'l', strtotime( $date ) ) ] ?? date( 'l', strtotime( $date ) );
$format_month = static fn ( string $date ): string => ( $month_names[ date( 'F', strtotime( $date ) ) ] ?? date( 'F', strtotime( $date ) ) ) . ' ' . date( 'Y', strtotime( $date ) );
$booking_url = recruitment_get_public_url( 'interview-booking', [ 'token' => $token, 'daw_ui_preview' => $preview ? '1' : '0' ] );
$available_days = array_filter( $availability, static fn ( array $day ): bool => 'available' === ( $day['status'] ?? '' ) );
$has_slots = (bool) $available_days;
$all_full = $availability && ! $has_slots;
?>
<div class="daw-recruitment daw-recruitment--interview-booking">
    <?php require recruitment_get_plugin_path( 'components/public/header.php' ); ?>
    <main class="daw-recruitment__interview-main">
        <div class="daw-recruitment__interview-container">
            <header class="daw-interview-heading"><span class="daw-recruitment__eyebrow">INTERVIEW SCHEDULING</span><h1>Booking Jadwal Wawancara</h1><p>Silakan pilih tanggal dan waktu wawancara yang tersedia.</p></header>
            <?php if ( ! $application ) : ?>
                <section class="daw-interview-state"><div class="daw-interview-state-icon">!</div><h2>Lamaran Tidak Ditemukan</h2><p>Data lamaran tidak dapat ditemukan. Silakan periksa kembali link atau token lamaran Anda.</p></section>
            <?php elseif ( $booking && 'success' !== $step ) : ?>
                <section class="daw-interview-card daw-interview-confirmed"><div class="daw-interview-success-icon">&#10003;</div><h2>Jadwal Wawancara Anda</h2><div class="daw-interview-detail"><span>Tanggal</span><strong><?= esc_html( $format_date( (string) $booking['date'] ) ) ?></strong><span>Waktu</span><strong><?= esc_html( $format_time( (string) $booking['start'] ) . ' – ' . $format_time( (string) $booking['end'] ) ) ?></strong><span>Status</span><strong class="daw-interview-status">Confirmed</strong></div><a class="daw-recruitment__button" href="<?= esc_url( recruitment_get_public_url( 'tracking', [ 'token' => $token, 'daw_ui_preview' => $preview ? '1' : '0' ] ) ) ?>">Lihat Detail</a></section>
            <?php elseif ( $availability_error ) : ?>
                <section class="daw-interview-state"><div class="daw-interview-state-icon">!</div><h2>Gagal Memuat Jadwal</h2><p>Terjadi kendala saat memuat jadwal wawancara. Silakan coba lagi.</p></section>
            <?php elseif ( ! $availability ) : ?>
                <section class="daw-interview-state"><div class="daw-interview-state-icon">&#128197;</div><h2>Jadwal Wawancara Belum Tersedia</h2><p>Jadwal wawancara belum tersedia saat ini. Silakan kembali lagi nanti untuk melihat jadwal yang tersedia.</p></section>
            <?php elseif ( $all_full ) : ?>
                <section class="daw-interview-state"><div class="daw-interview-state-icon">&#128197;</div><h2>Semua Jadwal Sudah Penuh</h2><p>Seluruh jadwal wawancara yang tersedia saat ini telah penuh.</p></section>
            <?php else : ?>
                <section class="daw-interview-applicant"><div><span>APPLICANT</span><strong><?= esc_html( $application['name'] ?? '' ) ?></strong><small><?= esc_html( $application['job_title'] ?? 'Posisi Lamaran' ) ?></small></div><div><span>APPLICATION ID</span><strong><?= esc_html( $application['id'] ?? $token ) ?></strong></div></section>
                <?php if ( 'success' === $step ) : ?>
                    <?php $success_booking = $database->get_interview_booking( $token ) ?: [ 'date' => $selected_date, 'start' => $posted_start ?? '', 'end' => $posted_end ?? '' ]; ?>
                    <section class="daw-interview-card daw-interview-confirmed"><div class="daw-interview-success-icon">&#10003;</div><h2>Booking Berhasil!</h2><p>Jadwal wawancara Anda telah berhasil dikonfirmasi.</p><div class="daw-interview-detail"><span>Tanggal</span><strong><?= esc_html( $format_date( (string) $success_booking['date'] ) ) ?></strong><span>Waktu</span><strong><?= esc_html( $format_time( (string) $success_booking['start'] ) . ' – ' . $format_time( (string) $success_booking['end'] ) ) ?></strong><span>Nama</span><strong><?= esc_html( $application['name'] ?? '' ) ?></strong><span>Posisi</span><strong><?= esc_html( $application['job_title'] ?? '' ) ?></strong><span>Application ID</span><strong><?= esc_html( $application['id'] ?? $token ) ?></strong></div><a class="daw-recruitment__button" href="<?= esc_url( recruitment_get_public_url( 'tracking', [ 'token' => $token, 'daw_ui_preview' => $preview ? '1' : '0' ] ) ) ?>">Lihat Tracking Lamaran</a></section>
                <?php elseif ( 'confirm' === $step && $selected_slot_data ) : ?>
                    <section class="daw-interview-card"><a class="daw-interview-back" href="<?= esc_url( add_query_arg( [ 'step' => 'summary', 'interview_date' => $selected_date, 'interview_slot' => $selected_slot ], $booking_url ) ) ?>">&larr; Kembali</a><h2>Konfirmasi Jadwal Wawancara</h2><p>Anda akan memilih jadwal:</p><div class="daw-interview-confirm-choice"><strong><?= esc_html( $format_date( $selected_date ) ) ?></strong><strong><?= esc_html( $format_time( $selected_slot_data['start'] ) . ' – ' . $format_time( $selected_slot_data['end'] ) ) ?></strong></div><p>Pastikan jadwal yang dipilih sudah benar.</p><?php if ( $booking_error ) : ?><div class="daw-interview-error"><?= esc_html( $booking_error ) ?></div><?php endif; ?><form method="post" action="<?= esc_url( $booking_url ) ?>"><input type="hidden" name="action" value="confirm_booking"><input type="hidden" name="step" value="confirm"><input type="hidden" name="interview_date" value="<?= esc_attr( $selected_date ) ?>"><input type="hidden" name="interview_slot" value="<?= esc_attr( $selected_slot ) ?>"><div class="daw-interview-actions"><a class="daw-recruitment__button daw-recruitment__button--muted" href="<?= esc_url( add_query_arg( [ 'step' => 'summary', 'interview_date' => $selected_date, 'interview_slot' => $selected_slot ], $booking_url ) ) ?>">Kembali</a><button class="daw-recruitment__button" type="submit">Konfirmasi</button></div></form></section>
                <?php elseif ( 'dates' === $step || ! $selected_day ) : ?>
                    <section class="daw-interview-card"><h2>Pilih Tanggal Wawancara</h2><div class="daw-interview-date-grid"><?php foreach ( $availability as $day ) : $is_available = 'available' === ( $day['status'] ?? '' ); ?><a class="daw-interview-date <?= $is_available ? '' : 'is-full' ?>" href="<?= $is_available ? esc_url( add_query_arg( [ 'step' => 'slots', 'interview_date' => $day['date'] ], $booking_url ) ) : '#' ?>" <?= $is_available ? '' : 'aria-disabled="true"' ?>><strong><?= esc_html( date( 'd', strtotime( $day['date'] ) ) ) ?></strong><span><?= esc_html( $format_day( $day['date'] ) ) ?></span><small><?= esc_html( $format_month( $day['date'] ) ) ?></small><b><?= $is_available ? 'Tersedia' : 'Penuh' ?></b></a><?php endforeach; ?></div></section>
                <?php elseif ( 'slots' === $step || ! $selected_slot_data ) : ?>
                    <section class="daw-interview-card"><a class="daw-interview-back" href="<?= esc_url( $booking_url ) ?>">&larr; Ganti tanggal</a><h2>Pilih Waktu Wawancara</h2><p class="daw-interview-selected-date"><?= esc_html( $format_date( $selected_date ) ) ?></p><div class="daw-interview-slot-list"><?php foreach ( $selected_day['slots'] ?? [] as $slot ) : $slot_available = 'available' === ( $slot['status'] ?? '' ); $slot_value = $slot['start'] . '-' . $slot['end']; ?><a class="daw-interview-slot <?= $slot_available ? '' : 'is-full' ?>" href="<?= $slot_available ? esc_url( add_query_arg( [ 'step' => 'summary', 'interview_date' => $selected_date, 'interview_slot' => $slot_value ], $booking_url ) ) : '#' ?>" <?= $slot_available ? '' : 'aria-disabled="true"' ?>><strong><?= esc_html( $format_time( $slot['start'] ) . ' – ' . $format_time( $slot['end'] ) ) ?></strong><span><?= $slot_available ? 'Tersedia' : 'Penuh' ?></span></a><?php endforeach; ?></div></section>
                <?php else : ?>
                    <section class="daw-interview-card"><a class="daw-interview-back" href="<?= esc_url( add_query_arg( [ 'step' => 'slots', 'interview_date' => $selected_date ], $booking_url ) ) ?>">&larr; Kembali</a><h2>Ringkasan Jadwal</h2><div class="daw-interview-summary"><span>Nama</span><strong><?= esc_html( $application['name'] ?? '' ) ?></strong><span>Posisi</span><strong><?= esc_html( $application['job_title'] ?? '' ) ?></strong><span>Tanggal</span><strong><?= esc_html( $format_date( $selected_date ) ) ?></strong><span>Waktu</span><strong><?= esc_html( $format_time( $selected_slot_data['start'] ) . ' – ' . $format_time( $selected_slot_data['end'] ) ) ?></strong></div><a class="daw-recruitment__button" href="<?= esc_url( add_query_arg( [ 'step' => 'confirm', 'interview_date' => $selected_date, 'interview_slot' => $selected_slot ], $booking_url ) ) ?>">Konfirmasi Jadwal</a></section>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
    <?php require recruitment_get_plugin_path( 'components/public/footer.php' ); ?>
</div>