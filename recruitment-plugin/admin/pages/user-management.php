<?php
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'Anda tidak memiliki izin untuk mengelola akun pengguna.', 'recruitment-plugin' ) );
}

$page_url = admin_url( 'admin.php?page=recruitment-user-management' );
$departments = [ 'Sales', 'After Sales', 'Finance' ];
$notice_code = sanitize_key( wp_unslash( $_GET['notice'] ?? '' ) );
$notice_messages = [
	'created' => 'Akun departemen berhasil ditambahkan.',
	'updated' => 'Informasi akun departemen berhasil diperbarui.',
	'deleted' => 'Akun departemen berhasil dihapus.',
	'invalid' => 'Data akun belum lengkap atau tidak valid.',
	'password' => 'Password minimal 8 karakter dan konfirmasi harus sama.',
	'failed' => 'Akun tidak dapat diproses. Periksa email atau username yang dimasukkan.',
];

if ( 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) && isset( $_POST['daw_department_user_action'] ) ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Anda tidak memiliki izin untuk mengelola akun pengguna.', 'recruitment-plugin' ) );
	}

	$action = sanitize_key( wp_unslash( $_POST['daw_department_user_action'] ) );
	$notice = 'failed';

	if ( 'delete' === $action ) {
		$user_id = absint( $_POST['user_id'] ?? 0 );
		check_admin_referer( 'daw_department_user_delete_' . $user_id );

		if (
			$user_id
			&& '1' === get_user_meta( $user_id, 'daw_department_user', true )
			&& ! user_can( $user_id, 'manage_options' )
			&& current_user_can( 'delete_user', $user_id )
			&& $user_id !== get_current_user_id()
		) {
			if ( ! function_exists( 'wp_delete_user' ) ) {
				require_once ABSPATH . 'wp-admin/includes/user.php';
			}
			if ( wp_delete_user( $user_id, get_current_user_id() ) ) {
				$notice = 'deleted';
			}
		}
	} elseif ( 'save' === $action ) {
		check_admin_referer( 'daw_department_user_save' );

		$user_id = absint( $_POST['user_id'] ?? 0 );
		$username = sanitize_user( wp_unslash( $_POST['user_login'] ?? '' ), true );
		$display_name = sanitize_text_field( wp_unslash( $_POST['display_name'] ?? '' ) );
		$email = sanitize_email( wp_unslash( $_POST['user_email'] ?? '' ) );
		$department = sanitize_text_field( wp_unslash( $_POST['department'] ?? '' ) );
		$password = (string) wp_unslash( $_POST['user_pass'] ?? '' );
		$password_confirmation = (string) wp_unslash( $_POST['user_pass_confirmation'] ?? '' );
		$is_department_user = $user_id
			&& '1' === get_user_meta( $user_id, 'daw_department_user', true )
			&& ! user_can( $user_id, 'manage_options' );
		$valid_data = '' !== $display_name && is_email( $email ) && in_array( $department, $departments, true );
		$valid_data = $valid_data && ( $user_id ? $is_department_user : ( '' !== $username && validate_username( $username ) ) );
		$valid_password = $user_id && '' === $password
			? '' === $password_confirmation
			: strlen( $password ) >= 8 && $password === $password_confirmation;

		if ( ! $valid_data ) {
			$notice = 'invalid';
		} elseif ( ! $valid_password ) {
			$notice = 'password';
		} else {
			$user_data = [
				'user_email' => $email,
				'display_name' => $display_name,
			];
			if ( $user_id ) {
				$user_data['ID'] = $user_id;
			} else {
				$user_data['user_login'] = $username;
				$user_data['role'] = 'subscriber';
			}
			if ( '' !== $password ) {
				$user_data['user_pass'] = $password;
			}

			$saved_user = $user_id ? wp_update_user( $user_data ) : wp_insert_user( $user_data );
			if ( ! is_wp_error( $saved_user ) ) {
				update_user_meta( $saved_user, 'daw_department_user', '1' );
				update_user_meta( $saved_user, 'daw_department', $department );
				$notice = $user_id ? 'updated' : 'created';
			}
		}
	}

	wp_safe_redirect( add_query_arg( 'notice', $notice, $page_url ) );
	exit;
}

$search = sanitize_text_field( wp_unslash( $_GET['search'] ?? '' ) );
$department_filter = sanitize_text_field( wp_unslash( $_GET['department'] ?? '' ) );
if ( ! in_array( $department_filter, $departments, true ) ) {
	$department_filter = '';
}
$all_users = get_users( [ 'orderby' => 'display_name', 'order' => 'ASC' ] );
$department_user_count = 0;
$department_counts = array_fill_keys( $departments, 0 );
$visible_users = [];

foreach ( $all_users as $account_user ) {
	$is_department_user = '1' === get_user_meta( $account_user->ID, 'daw_department_user', true )
		&& ! user_can( $account_user, 'manage_options' );
	$account_department = $is_department_user ? (string) get_user_meta( $account_user->ID, 'daw_department', true ) : '';
	if ( $is_department_user ) {
		$department_user_count++;
		if ( isset( $department_counts[ $account_department ] ) ) {
			$department_counts[ $account_department ]++;
		}
	}

	$searchable = strtolower( $account_user->display_name . ' ' . $account_user->user_login . ' ' . $account_user->user_email );
	$matches_search = '' === $search || false !== strpos( $searchable, strtolower( $search ) );
	$matches_department = '' === $department_filter || $account_department === $department_filter;
	if ( $matches_search && $matches_department ) {
		$visible_users[] = [
			'user' => $account_user,
			'is_department_user' => $is_department_user,
			'department' => $account_department,
		];
	}
}
?>
<div class="daw-account-settings">
	<header class="daw-account-settings__heading">
		<div>
			<p class="daw-account-settings__breadcrumb">DAW Admin <span>/</span> Pengaturan</p>
			<h1>Pengaturan</h1>
			<p>Kelola akun dan akses pengguna departemen.</p>
		</div>
		<button class="daw-account-settings__add" type="button" data-open-account-dialog>
			<span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span> Tambah Akun
		</button>
	</header>

	<?php if ( isset( $notice_messages[ $notice_code ] ) ) : ?>
		<div class="daw-account-settings__notice <?= in_array( $notice_code, [ 'invalid', 'password', 'failed' ], true ) ? 'is-error' : 'is-success' ?>" role="status"><?= esc_html( $notice_messages[ $notice_code ] ) ?></div>
	<?php endif; ?>

	<section class="daw-account-settings__stats" aria-label="Ringkasan akun departemen">
		<article><span>Total Akun Departemen</span><strong><?= esc_html( number_format_i18n( $department_user_count ) ) ?></strong></article>
		<?php foreach ( $departments as $department ) : ?>
			<article><span><?= esc_html( $department ) ?></span><strong><?= esc_html( number_format_i18n( $department_counts[ $department ] ) ) ?></strong></article>
		<?php endforeach; ?>
	</section>

	<section class="daw-account-settings__panel" aria-label="Daftar akun pengguna">
		<form class="daw-account-settings__filters" method="get" action="<?= esc_url( admin_url( 'admin.php' ) ) ?>">
			<input type="hidden" name="page" value="recruitment-user-management" />
			<label class="daw-account-settings__search"><span class="screen-reader-text">Cari akun</span><span class="dashicons dashicons-search" aria-hidden="true"></span><input type="search" name="search" value="<?= esc_attr( $search ) ?>" placeholder="Cari nama, username, atau email" /></label>
			<label class="screen-reader-text" for="daw-account-department-filter">Filter departemen</label>
			<select id="daw-account-department-filter" name="department">
				<option value="">Semua departemen</option>
				<?php foreach ( $departments as $department ) : ?>
					<option value="<?= esc_attr( $department ) ?>" <?= selected( $department_filter, $department, false ) ?>><?= esc_html( $department ) ?></option>
				<?php endforeach; ?>
			</select>
			<button type="submit" class="daw-account-settings__filter-button">Terapkan</button>
			<a class="daw-account-settings__reset" href="<?= esc_url( $page_url ) ?>" aria-label="Reset filter">Reset</a>
		</form>

		<div class="daw-account-settings__table-wrap">
			<table class="daw-account-settings__table">
				<thead><tr><th scope="col">Nama</th><th scope="col">Username</th><th scope="col">Email</th><th scope="col">Departemen</th><th scope="col">Akses</th><th scope="col">Aksi</th></tr></thead>
				<tbody>
					<?php foreach ( $visible_users as $row ) :
						$account_user = $row['user'];
						$role_slug = $account_user->roles[0] ?? '';
						$role_name = $role_slug && isset( wp_roles()->roles[ $role_slug ]['name'] ) ? translate_user_role( wp_roles()->roles[ $role_slug ]['name'] ) : '—';
						?>
						<tr>
							<td data-label="Nama"><strong><?= esc_html( $account_user->display_name ?: $account_user->user_login ) ?></strong><small><?= esc_html( mysql2date( 'd M Y', $account_user->user_registered ) ) ?></small></td>
							<td data-label="Username"><?= esc_html( $account_user->user_login ) ?></td>
							<td data-label="Email"><a href="mailto:<?= esc_attr( $account_user->user_email ) ?>"><?= esc_html( $account_user->user_email ) ?></a></td>
							<td data-label="Departemen"><?= esc_html( $row['department'] ?: '—' ) ?></td>
							<td data-label="Akses"><span class="daw-account-settings__role <?= $row['is_department_user'] ? 'is-department' : '' ?>"><?= esc_html( $row['is_department_user'] ? 'User Departemen' : $role_name ) ?></span></td>
							<td data-label="Aksi">
								<?php if ( $row['is_department_user'] ) : ?>
									<div class="daw-account-settings__actions">
										<button type="button" class="daw-account-settings__icon-button" data-edit-account data-account-id="<?= esc_attr( $account_user->ID ) ?>" data-account-login="<?= esc_attr( $account_user->user_login ) ?>" data-account-name="<?= esc_attr( $account_user->display_name ) ?>" data-account-email="<?= esc_attr( $account_user->user_email ) ?>" data-account-department="<?= esc_attr( $row['department'] ) ?>" aria-label="Edit akun <?= esc_attr( $account_user->user_login ) ?>" title="Edit akun"><span class="dashicons dashicons-edit" aria-hidden="true"></span></button>
										<form method="post" action="<?= esc_url( $page_url ) ?>" onsubmit="return confirm('Hapus akun <?= esc_js( $account_user->user_login ) ?>? Tindakan ini tidak dapat dibatalkan.');">
											<input type="hidden" name="daw_department_user_action" value="delete" />
											<input type="hidden" name="user_id" value="<?= esc_attr( $account_user->ID ) ?>" />
											<?php wp_nonce_field( 'daw_department_user_delete_' . $account_user->ID ); ?>
											<button type="submit" class="daw-account-settings__icon-button is-danger" aria-label="Hapus akun <?= esc_attr( $account_user->user_login ) ?>" title="Hapus akun"><span class="dashicons dashicons-trash" aria-hidden="true"></span></button>
										</form>
									</div>
								<?php else : ?>
									<span class="daw-account-settings__protected">Akun WordPress</span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if ( empty( $visible_users ) ) : ?>
						<tr><td class="daw-account-settings__empty" colspan="6"><?= $search || $department_filter ? 'Tidak ada akun yang sesuai dengan filter.' : 'Belum ada akun user departemen. Tambahkan akun pertama untuk mulai mengelola akses.' ?></td></tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<footer class="daw-account-settings__table-footer"><span><?= esc_html( number_format_i18n( count( $visible_users ) ) ) ?> akun ditampilkan</span></footer>
	</section>

	<dialog class="daw-account-settings__dialog" data-account-dialog aria-labelledby="daw-account-dialog-title">
		<form method="post" action="<?= esc_url( $page_url ) ?>" data-account-form>
			<input type="hidden" name="daw_department_user_action" value="save" />
			<input type="hidden" name="user_id" value="" data-account-id />
			<?php wp_nonce_field( 'daw_department_user_save' ); ?>
			<header><div><span class="daw-account-settings__dialog-eyebrow">AKUN DEPARTEMEN</span><h2 id="daw-account-dialog-title" data-account-dialog-title>Tambah Akun</h2></div><button type="button" data-close-account-dialog aria-label="Tutup"><span class="dashicons dashicons-no-alt" aria-hidden="true"></span></button></header>
			<div class="daw-account-settings__form-grid">
				<label>Nama Lengkap<input type="text" name="display_name" required maxlength="100" data-account-name /></label>
				<label>Username<input type="text" name="user_login" required maxlength="60" autocomplete="username" data-account-login /></label>
				<label>Email<input type="email" name="user_email" required autocomplete="email" data-account-email /></label>
				<label>Departemen<select name="department" required data-account-department><option value="">Pilih departemen</option><?php foreach ( $departments as $department ) : ?><option value="<?= esc_attr( $department ) ?>"><?= esc_html( $department ) ?></option><?php endforeach; ?></select></label>
				<label>Password Baru<input type="password" name="user_pass" minlength="8" autocomplete="new-password" data-account-password /><small data-password-help>Minimal 8 karakter.</small></label>
				<label>Konfirmasi Password<input type="password" name="user_pass_confirmation" minlength="8" autocomplete="new-password" data-account-password-confirm /></label>
			</div>
			<footer><button type="button" class="daw-account-settings__cancel" data-close-account-dialog>Batal</button><button type="submit" class="daw-account-settings__save"><span class="dashicons dashicons-saved" aria-hidden="true"></span> Simpan Akun</button></footer>
		</form>
	</dialog>
</div>