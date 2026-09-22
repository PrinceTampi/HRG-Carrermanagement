<?php

/**
 * Central route registry and page renderer.
 */
class Recruitment_Router {

    /** @var array<string, string> */
    private const ROUTES = [
        'careers'            => 'pages/public/careers.php',
        'job-detail'         => 'pages/public/job-detail.php',
        'application-form'   => 'pages/public/application-form.php',
        'application-status' => 'pages/public/application-status.php',
        'tracking-detail'    => 'pages/public/application-tracking.php',
        'login'              => 'pages/admin/login.php',
        'dashboard'          => 'pages/admin/dashboard.php',
        'vacancies'          => 'pages/admin/vacancies.php',
        'applicants'         => 'pages/admin/applicants.php',
        'applications'       => 'pages/admin/applications.php',
        'settings'           => 'pages/admin/settings.php',
    ];

    /** @var string[] */
    private const PROTECTED = [ 'dashboard', 'vacancies', 'applicants', 'applications', 'settings' ];

    /**
     * @param array<string, mixed> $data
     */
    public function render( string $page, array $data = [] ): void {
        $slug = array_key_exists( $page, self::ROUTES ) ? $page : 'careers';

        if ( in_array( $slug, self::PROTECTED, true ) && ! is_user_logged_in() ) {
            wp_safe_redirect( recruitment_get_admin_url( 'login' ) );
            exit;
        }

        if ( 'applicants' === $slug ) {
            $data['plugin_info'] = ( new Recruitment_Plugin() )->get_info();
        }

        $data['page'] = $slug;
        extract( $data, EXTR_SKIP );
        require recruitment_get_plugin_path( self::ROUTES[ $slug ] );
    }
}