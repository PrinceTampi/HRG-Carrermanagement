<?php
/** Register and render the WordPress-native Recruitment admin screens. */
class Recruitment_Admin {

	public function init(): void {
		add_action( 'admin_menu', [ $this, 'register_admin_menu' ], 9 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'admin_footer', [ $this, 'render_screen_explorer' ] );
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

		remove_submenu_page( 'recruitment-dashboard', 'recruitment-dashboard' );
		add_submenu_page( 'recruitment-dashboard', 'Dashboard', 'Dashboard', 'manage_recruitment', 'recruitment-dashboard', [ $this, 'render_dashboard' ] );
		add_submenu_page( 'recruitment-dashboard', 'Pelamar', 'Pelamar', 'manage_recruitment', 'recruitment-applicants', [ $this, 'render_applicants' ] );
		add_submenu_page( 'recruitment-dashboard', 'Lamaran', 'Lamaran', 'manage_recruitment', 'recruitment-applications', [ $this, 'render_applications' ] );
		add_submenu_page( 'recruitment-dashboard', 'Pengaturan', 'Pengaturan', 'manage_recruitment', 'recruitment-settings', [ $this, 'render_settings' ] );
	}

	public function enqueue_admin_assets(): void {
		$page      = sanitize_key( $_GET['page'] ?? '' );
		$post_type = sanitize_key( $_GET['post_type'] ?? '' );
		$admin_pages = [ 'recruitment-dashboard', 'recruitment-applicants', 'recruitment-applications', 'recruitment-settings' ];

		if ( ! in_array( $page, $admin_pages, true ) && 'daw_vacancy' !== $post_type ) {
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
		if ( ! current_user_can( 'manage_recruitment' ) ) {
			return;
		}

		$page      = sanitize_key( $_GET['page'] ?? '' );
		$post_type = sanitize_key( $_GET['post_type'] ?? '' );
		$admin_pages = [ 'recruitment-dashboard', 'recruitment-applicants', 'recruitment-applications', 'recruitment-settings' ];

		if ( ! in_array( $page, $admin_pages, true ) && 'daw_vacancy' !== $post_type ) {
			return;
		}

		require recruitment_get_plugin_path( 'public/components/screen-explorer.php' );
	}

	public function render_dashboard(): void {
		$this->render_page( 'dashboard' );
	}

	public function render_applicants(): void {
		$this->render_page( 'applicants' );
	}

	public function render_applications(): void {
		$this->render_page( 'applications' );
	}

	public function render_settings(): void {
		$this->render_page( 'settings' );
	}

	private function render_page( string $page ): void {
		if ( ! current_user_can( 'manage_recruitment' ) ) {
			wp_die( esc_html__( 'Anda tidak memiliki izin untuk mengakses halaman ini.', 'recruitment-plugin' ) );
		}

		( new Recruitment_Router() )->render( $page );
	}
}

