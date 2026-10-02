<?php

/**
 * Recruitment_Plugin — main plugin bootstrap / controller.
 *
 * Tanggung jawab:
 * - Initialize public module
 * - Initialize admin module
 * - Register hooks
 */
class Recruitment_Plugin {

    /**
     * Plugin info used across the system.
     *
     * @return array<string, string>
     */
    public function get_info(): array {
        return [
            'name'      => 'Recruitment Plugin',
            'status'    => 'Active',
            'version'   => RECRUITMENT_PLUGIN_VERSION,
            'site_url'  => recruitment_get_plugin_url(),
            'shortcode' => '[recruitment_careers]',
        ];
    }

    /**
     * Bootstrap the plugin (hooks, loaders, etc.).
     * Called from recruitment-plugin.php on init.
     */
    public function init(): void {
        add_action( 'init', [ $this, 'register_content' ] );
        add_action( 'init', [ $this, 'register_user_department_role' ] );
        add_action( 'template_redirect', [ $this, 'handle_portal_login' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_public_assets' ] );
        add_filter( 'render_block', [ $this, 'remove_theme_footer' ], 10, 2 );
        add_filter( 'the_content', [ $this, 'render_front_page' ] );
        add_filter( 'login_url', [ $this, 'filter_admin_login_url' ], 10, 3 );

        add_shortcode( 'recruitment_careers', [ $this, 'render_careers_shortcode' ] );

        ( new Recruitment_Admin() )->init();
    }

    public function register_user_department_role(): void {
        $department_role = get_role( 'daw_user_department' );
        if ( ! $department_role ) {
            $department_role = add_role( 'daw_user_department', 'User Department', [ 'read' => true ] );
        }

        if ( $department_role && ! $department_role->has_cap( 'manage_user_department' ) ) {
            $department_role->add_cap( 'manage_user_department' );
        }

        $administrator = get_role( 'administrator' );
        if ( $administrator && ! $administrator->has_cap( 'manage_user_department' ) ) {
            $administrator->add_cap( 'manage_user_department' );
        }
    }

    public function handle_portal_login(): void {
        $requested_screen = sanitize_key( wp_unslash( $_GET['recruitment_page'] ?? '' ) );
        if ( ! in_array( $requested_screen, [ 'portal', 'login' ], true ) ) {
            return;
        }

        $target = 'user_department' === sanitize_key( wp_unslash( $_REQUEST['target'] ?? '' ) ) ? 'user_department' : 'hr';
        $capability = 'user_department' === $target ? 'manage_user_department' : 'manage_recruitment';
        $dashboard_url = 'user_department' === $target
            ? admin_url( 'admin.php?page=daw-user-department' )
            : admin_url( 'admin.php?page=recruitment-dashboard' );

        if ( is_user_logged_in() ) {
            $current_user = wp_get_current_user();
            if ( user_can( $current_user, $capability ) ) {
                wp_safe_redirect( $dashboard_url );
                exit;
            }
        }

        if ( 'login' !== $requested_screen || 'POST' !== strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) ) ) {
            return;
        }

        $nonce = sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ?? '' ) );
        if ( ! wp_verify_nonce( $nonce, 'daw_admin_portal_login' ) ) {
            $this->redirect_to_portal_login( $target, 'invalid_request' );
        }

        $user = wp_signon(
            [
                'user_login'    => sanitize_text_field( wp_unslash( $_POST['username'] ?? '' ) ),
                'user_password' => (string) wp_unslash( $_POST['password'] ?? '' ),
                'remember'      => true,
            ],
            is_ssl()
        );

        if ( is_wp_error( $user ) ) {
            $this->redirect_to_portal_login( $target, 'invalid_credentials' );
        }

        if ( ! user_can( $user, $capability ) ) {
            wp_logout();
            $this->redirect_to_portal_login( $target, 'no_access' );
        }

        wp_safe_redirect( $dashboard_url );
        exit;
    }

    private function redirect_to_portal_login( string $target, string $error ): void {
        wp_safe_redirect(
            recruitment_get_public_url(
                'portal',
                [
                    'recruitment_page' => 'login',
                    'target' => $target,
                    'login_error' => $error,
                ]
            )
        );
        exit;
    }

    public function filter_admin_login_url( string $login_url, string $redirect = '', bool $force_reauth = false ): string {
        $redirect_query = (string) parse_url( $redirect, PHP_URL_QUERY );
        parse_str( $redirect_query, $query_args );
        $page = sanitize_key( $query_args['page'] ?? '' );
        $hr_pages = [ 'recruitment-dashboard', 'recruitment-vacancies', 'recruitment-applicants', 'recruitment-applications', 'recruitment-user-interview', 'recruitment-form-builder', 'recruitment-psychotest-settings', 'recruitment-question-bank', 'recruitment-settings' ];

        if ( 'daw-user-department' === $page ) {
            return recruitment_get_public_url( 'portal', [ 'recruitment_page' => 'login', 'target' => 'user_department' ] );
        }

        if ( in_array( $page, $hr_pages, true ) ) {
            return recruitment_get_public_url( 'portal', [ 'recruitment_page' => 'login', 'target' => 'hr' ] );
        }

        return $login_url;
    }

    public function remove_theme_footer( string $block_content, array $block ): string {
    if ( is_admin() ) {
        return $block_content;
    }

    if ( ! is_page() ) {
        return $block_content;
    }

    // Hanya berlaku untuk halaman Recruitment/Career
    if ( ! has_shortcode( get_post_field( 'post_content', get_queried_object_id() ), 'recruitment_careers' ) ) {
        return $block_content;
    }

    // Remove the empty paragraph block that WordPress appends after the shortcode.
    if (
        isset( $block['blockName'] ) &&
        'core/paragraph' === $block['blockName'] &&
        '' === trim( wp_strip_all_tags( $block_content ) )
    ) {
        return '';
    }

    // Hapus template footer bawaan Twenty Twenty-Five
    if (
        isset( $block['blockName'] ) &&
        'core/template-part' === $block['blockName'] &&
        isset( $block['attrs']['slug'] ) &&
        'footer' === $block['attrs']['slug']
    ) {
        return '';
    }

    return $block_content;
}

    public function render_front_page( string $content ): string {
        if ( is_admin() || ! is_front_page() || ! is_main_query() || ! in_the_loop() ) {
            return $content;
        }

        return do_shortcode( '[recruitment_careers]' );
    }

    public function register_content(): void {
        register_post_type(
            'daw_vacancy',
            [
                'labels'       => [ 'name' => 'Lowongan', 'singular_name' => 'Lowongan' ],
                'public'       => true,
                'show_in_rest' => true,
                'supports'     => [ 'title', 'editor', 'excerpt' ],
                'menu_icon'    => 'dashicons-businessperson',
                'show_in_menu' => 'recruitment-dashboard',
                'rewrite'      => [ 'slug' => 'lowongan' ],
            ]
        );
    }

    public function enqueue_public_assets(): void {
        $public_css = recruitment_get_plugin_path( 'assets/css/public.css' );
        $public_js  = recruitment_get_plugin_path( 'assets/js/public.js' );
        wp_enqueue_style( 'recruitment-public', recruitment_get_plugin_url( 'assets/css/public.css' ), [], file_exists( $public_css ) ? filemtime( $public_css ) : RECRUITMENT_PLUGIN_VERSION );
        wp_enqueue_script( 'recruitment-public', recruitment_get_plugin_url( 'assets/js/public.js' ), [], file_exists( $public_js ) ? filemtime( $public_js ) : RECRUITMENT_PLUGIN_VERSION, true );

        if ( in_array( sanitize_key( $_GET['recruitment_page'] ?? '' ), [ 'login', 'portal' ], true ) ) {
            $admin_css = recruitment_get_plugin_path( 'assets/css/admin.css' );
            wp_enqueue_style( 'recruitment-admin-login', recruitment_get_plugin_url( 'assets/css/admin.css' ), [ 'recruitment-public' ], file_exists( $admin_css ) ? filemtime( $admin_css ) : RECRUITMENT_PLUGIN_VERSION );
        }
    }

    public function render_careers_shortcode(): string {
        $page_id = get_queried_object_id();
        if ( $page_id ) {
            update_option( 'recruitment_public_page_id', absint( $page_id ) );
        }

        $requested_screen = sanitize_key( $_GET['recruitment_page'] ?? 'careers' );
        $screen = [ 'application' => 'application-form' ][ $requested_screen ] ?? $requested_screen;
        if ( 'tracking' === $requested_screen ) {
            $screen = '' !== sanitize_text_field( wp_unslash( $_GET['token'] ?? '' ) ) ? 'tracking-detail' : 'application-status';
        }
        $page_data = [];
        if ( 'login' === $screen ) {
            $error_messages = [
                'invalid_request' => 'Sesi login tidak valid. Silakan coba kembali.',
                'invalid_credentials' => 'Email/username atau password tidak sesuai.',
                'no_access' => 'Akun ini belum memiliki akses ke portal yang dipilih.',
            ];
            $error_key = sanitize_key( wp_unslash( $_GET['login_error'] ?? '' ) );
            $page_data['error'] = $error_messages[ $error_key ] ?? '';
            $page_data['target'] = 'user_department' === sanitize_key( wp_unslash( $_GET['target'] ?? '' ) ) ? 'user_department' : 'hr';
        }
        if ( 'application-form' === $screen && function_exists( 'nocache_headers' ) ) {
            nocache_headers();
        }

        ob_start();
        ( new Recruitment_Router() )->render( $screen, $page_data );
        return ob_get_clean();
    }

}
