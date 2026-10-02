<?php
/** Register and render the WordPress-native Recruitment admin screens. */
class Recruitment_Admin {

	public function init(): void {
		add_action( 'admin_menu', [ $this, 'register_admin_menu' ], 9 );
		add_action( 'admin_post_daw_save_department_request', [ $this, 'handle_department_request_post' ] );
		add_action( 'admin_menu', [ $this, 'remove_vacancy_post_type_submenu' ], 99 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'in_admin_header', [ $this, 'render_admin_sidebar' ] );
		add_action( 'admin_notices', [ $this, 'render_vacancy_overview' ] );
		add_action( 'admin_footer', [ $this, 'render_screen_explorer' ] );
		add_filter( 'admin_body_class', [ $this, 'add_admin_body_class' ] );
	}

	public function add_admin_body_class( string $classes ): string {
		$page = sanitize_key( $_GET['page'] ?? '' );
		$can_access_screen = 'daw-user-department' === $page
			? current_user_can( 'manage_user_department' )
			: current_user_can( 'manage_recruitment' );
		if ( $this->is_recruitment_screen() && $can_access_screen ) {
			$classes .= ' daw-recruitment-admin';
		}

		return $classes;
	}

	public function render_admin_sidebar(): void {
		if ( 'daw-user-department' === sanitize_key( $_GET['page'] ?? '' ) || ! $this->is_recruitment_screen() || ! current_user_can( 'manage_recruitment' ) ) {
			return;
		}

		require recruitment_get_plugin_path( 'admin/components/sidebar.php' );
	}

	public function render_vacancy_overview(): void {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || 'edit' !== $screen->base || 'daw_vacancy' !== $screen->post_type || ! current_user_can( 'manage_recruitment' ) ) {
			return;
		}

		$counts = wp_count_posts( 'daw_vacancy' );
		$published = (int) ( $counts->publish ?? 0 );
		$draft = (int) ( $counts->draft ?? 0 ) + (int) ( $counts->pending ?? 0 ) + (int) ( $counts->future ?? 0 );
		$archived = (int) ( $counts->trash ?? 0 );
		$total = $published + $draft + (int) ( $counts->private ?? 0 );
		?>
		<section class="daw-vacancy-overview" aria-labelledby="daw-vacancy-overview-title">
			<div class="daw-vacancy-overview__heading">
				<div>
					<p class="daw-vacancy-overview__breadcrumb">DAW Admin <span>/</span> Recruitment</p>
					<h1 id="daw-vacancy-overview-title">Lowongan</h1>
					<p class="daw-vacancy-overview__description">Kelola posisi dan publikasi lowongan kerja.</p>
				</div>
				<a class="daw-vacancy-overview__create" href="<?= esc_url( admin_url( 'post-new.php?post_type=daw_vacancy' ) ) ?>"><span aria-hidden="true">+</span> Buat Lowongan</a>
			</div>
			<div class="daw-vacancy-overview__stats" aria-label="Ringkasan lowongan">
				<div class="daw-vacancy-overview__stat daw-vacancy-overview__stat--total"><span>Total Lowongan</span><strong><?= esc_html( number_format_i18n( $total ) ) ?></strong></div>
				<div class="daw-vacancy-overview__stat daw-vacancy-overview__stat--published"><span>Dipublikasikan</span><strong><?= esc_html( number_format_i18n( $published ) ) ?></strong></div>
				<div class="daw-vacancy-overview__stat daw-vacancy-overview__stat--draft"><span>Draft / Terjadwal</span><strong><?= esc_html( number_format_i18n( $draft ) ) ?></strong></div>
				<div class="daw-vacancy-overview__stat daw-vacancy-overview__stat--archived"><span>Arsip</span><strong><?= esc_html( number_format_i18n( $archived ) ) ?></strong></div>
			</div>
		</section>
		<?php
	}

	public function register_admin_menu(): void {
		add_menu_page(
			'Recruitment',
			'Recruitment',
			'manage_recruitment',
			'recruitment-dashboard',
			[ $this, 'render_dashboard' ],
			'dashicons-groups',
			26
		);

		add_menu_page(
			'Admin User Department',
			'User Department',
			'manage_user_department',
			'daw-user-department',
			[ $this, 'render_user_department' ],
			'dashicons-groups',
			27
		);

		remove_submenu_page( 'recruitment-dashboard', 'recruitment-dashboard' );
		add_submenu_page( 'recruitment-dashboard', 'Dashboard', 'Dashboard', 'manage_recruitment', 'recruitment-dashboard', [ $this, 'render_dashboard' ] );
		add_submenu_page( 'recruitment-dashboard', 'Lowongan', 'Lowongan', 'manage_recruitment', 'recruitment-vacancies', [ $this, 'render_vacancies' ] );
		add_submenu_page( 'recruitment-dashboard', 'Pelamar', 'Pelamar', 'manage_recruitment', 'recruitment-applicants', [ $this, 'render_applicants' ] );
		add_submenu_page( 'recruitment-dashboard', 'Lamaran', 'Lamaran', 'manage_recruitment', 'recruitment-applications', [ $this, 'render_applications' ] );
		add_submenu_page( 'recruitment-dashboard', 'Final Decision', 'Final Decision', 'manage_recruitment', 'recruitment-final-decision', [ $this, 'render_final_decision' ] );
		add_submenu_page( 'recruitment-dashboard', 'Wawancara User', 'Wawancara User', 'manage_recruitment', 'recruitment-user-interview', [ $this, 'render_user_interview' ] );
		add_submenu_page( 'recruitment-dashboard', 'Form Lamaran', 'Form Lamaran', 'manage_recruitment', 'recruitment-form-builder', [ $this, 'render_form_builder' ] );
		add_submenu_page( 'recruitment-dashboard', 'Setting Psikotes', 'Setting Psikotes', 'manage_recruitment', 'recruitment-psychotest-settings', [ $this, 'render_psychotest_settings' ] );
		add_submenu_page( 'recruitment-dashboard', 'Bank Soal', 'Bank Soal', 'manage_recruitment', 'recruitment-question-bank', [ $this, 'render_question_bank' ] );
		add_submenu_page( 'recruitment-dashboard', 'Pengaturan', 'Pengaturan', 'manage_recruitment', 'recruitment-settings', [ $this, 'render_settings' ] );
	}

	public function remove_vacancy_post_type_submenu(): void {
		remove_submenu_page( 'recruitment-dashboard', 'edit.php?post_type=daw_vacancy' );
	}

	public function enqueue_admin_assets(): void {
		if ( ! $this->is_recruitment_screen() ) {
			return;
		}

		$css_path = recruitment_get_plugin_path( 'assets/css/admin.css' );
		$js_path  = recruitment_get_plugin_path( 'assets/js/admin.js' );
		$public_css_path = recruitment_get_plugin_path( 'assets/css/public.css' );
		$public_js_path  = recruitment_get_plugin_path( 'assets/js/public.js' );
		wp_enqueue_style( 'recruitment-public', recruitment_get_plugin_url( 'assets/css/public.css' ), [], file_exists( $public_css_path ) ? filemtime( $public_css_path ) : RECRUITMENT_PLUGIN_VERSION );
		wp_enqueue_script( 'recruitment-public', recruitment_get_plugin_url( 'assets/js/public.js' ), [], file_exists( $public_js_path ) ? filemtime( $public_js_path ) : RECRUITMENT_PLUGIN_VERSION, true );
		wp_enqueue_style( 'recruitment-admin', recruitment_get_plugin_url( 'assets/css/admin.css' ), [], file_exists( $css_path ) ? filemtime( $css_path ) : RECRUITMENT_PLUGIN_VERSION );
		wp_enqueue_script( 'recruitment-admin', recruitment_get_plugin_url( 'assets/js/admin.js' ), [], file_exists( $js_path ) ? filemtime( $js_path ) : RECRUITMENT_PLUGIN_VERSION, true );
	}

	public function render_screen_explorer(): void {
		if ( 'daw-user-department' === sanitize_key( $_GET['page'] ?? '' ) || ! current_user_can( 'manage_recruitment' ) ) {
			return;
		}

		if ( ! $this->is_recruitment_screen() ) {
			return;
		}

		require recruitment_get_plugin_path( 'public/components/screen-explorer.php' );
	}

	public function render_dashboard(): void {
		$this->render_page( 'dashboard' );
	}

	public function render_vacancies(): void {
		$this->render_page( 'vacancies' );
	}

	public function render_applicants(): void {
		$this->render_page( 'applicants' );
	}

	public function render_applications(): void {
		$this->render_page( 'applications' );
	}

	public function render_final_decision(): void {
		$this->render_page( 'final-decision' );
	}

	public function render_user_department(): void {
		$this->render_page( 'user-department' );
	}

	public function handle_department_request_post(): void {
		if ( ! current_user_can( 'manage_user_department' ) || 'daw-user-department' !== sanitize_key( $_GET['page'] ?? '' ) ) {
			wp_die( esc_html__( 'Anda tidak memiliki izin untuk mengirim request ini.', 'recruitment-plugin' ) );
		}

		require recruitment_get_plugin_path( 'admin/pages/user-department.php' );
	}

	public function render_user_interview(): void {
		$this->render_page( 'user-interview' );
	}

	public function render_form_builder(): void {
		$this->render_page( 'application-form-builder' );
	}

	public function render_psychotest_settings(): void {
		$this->render_page( 'psychotest-settings' );
	}

	public function render_question_bank(): void {
		$this->render_page( 'question-bank' );
	}

	public function render_settings(): void {
		$this->render_page( 'settings' );
	}

	private function render_page( string $page ): void {
		$capability = 'user-department' === $page ? 'manage_user_department' : 'manage_recruitment';
		if ( ! current_user_can( $capability ) ) {
			wp_die( esc_html__( 'Anda tidak memiliki izin untuk mengakses halaman ini.', 'recruitment-plugin' ) );
		}

		( new Recruitment_Router() )->render( $page );
	}

	private function is_recruitment_screen(): bool {
		$page = sanitize_key( $_GET['page'] ?? '' );
		$admin_pages = [ 'recruitment-dashboard', 'recruitment-vacancies', 'recruitment-applicants', 'recruitment-applications', 'recruitment-final-decision', 'recruitment-user-interview', 'recruitment-form-builder', 'recruitment-psychotest-settings', 'recruitment-question-bank', 'recruitment-settings', 'daw-user-department' ];

		if ( in_array( $page, $admin_pages, true ) ) {
			return true;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		$post_type = sanitize_key( $_GET['post_type'] ?? ( $screen->post_type ?? '' ) );
		if ( 'daw_vacancy' === $post_type ) {
			return true;
		}

		return $screen
			&& in_array( $screen->id, [ 'users', 'users-network' ], true )
			&& current_user_can( 'manage_recruitment' )
			&& current_user_can( 'list_users' );
	}
}

