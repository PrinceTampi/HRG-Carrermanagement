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
        add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_public_assets' ] );
        add_filter( 'render_block', [ $this, 'remove_theme_footer' ], 10, 2 );
        add_filter( 'the_content', [ $this, 'render_front_page' ] );

        add_shortcode( 'recruitment_careers', [ $this, 'render_careers_shortcode' ] );
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
                'rewrite'      => [ 'slug' => 'lowongan' ],
            ]
        );
    }

    public function register_admin_menu(): void {
        add_menu_page(
            'Recruitment',
            'Recruitment',
            'manage_recruitment',
            'recruitment-dashboard',
            [ $this, 'render_admin_dashboard' ],
            'dashicons-groups',
            26
        );
    }

    public function enqueue_public_assets(): void {
        wp_enqueue_style( 'recruitment-public', recruitment_get_plugin_url( 'assets/css/public.css' ), [], RECRUITMENT_PLUGIN_VERSION );
        wp_enqueue_script( 'recruitment-public', recruitment_get_plugin_url( 'assets/js/public.js' ), [], RECRUITMENT_PLUGIN_VERSION, true );
    }

    public function enqueue_admin_assets(): void {
        wp_enqueue_style( 'recruitment-admin', recruitment_get_plugin_url( 'assets/css/admin.css' ), [], RECRUITMENT_PLUGIN_VERSION );
        wp_enqueue_script( 'recruitment-admin', recruitment_get_plugin_url( 'assets/js/admin.js' ), [], RECRUITMENT_PLUGIN_VERSION, true );
    }

    public function render_careers_shortcode(): string {
        $page_id = get_queried_object_id();
        if ( $page_id ) {
            update_option( 'recruitment_public_page_id', absint( $page_id ) );
        }

        $screen = sanitize_key( $_GET['recruitment_page'] ?? 'careers' );
        $screen = [ 'application' => 'application-form', 'tracking' => 'application-status' ][ $screen ] ?? $screen;

        ob_start();
        ( new Recruitment_Router() )->render( $screen );
        return ob_get_clean();
    }

    public function render_admin_dashboard(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Anda tidak memiliki izin untuk mengakses halaman ini.', 'recruitment-plugin' ) );
        }

        ( new Recruitment_Router() )->render( 'dashboard' );
    }
}
