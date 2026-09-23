<?php

/**
 * Central route registry and page renderer.
 */
class Recruitment_Router {

    /** @var array<string, string> */
    private const ROUTES = [
        'careers'            => 'public/pages/careers.php',
        'job-detail'         => 'public/pages/job-detail.php',
        'application-form'   => 'public/pages/application-form.php',
        'application-confirmation' => 'public/pages/application-confirmation.php',
        'psych-test'         => 'public/pages/psychological-test.php',
        'application-status' => 'public/pages/application-status.php',
        'tracking-detail'    => 'public/pages/application-tracking.php',
        'login'              => 'admin/pages/login.php',
        'dashboard'          => 'admin/pages/dashboard.php',
        'vacancies'          => 'admin/pages/vacancies.php',
        'applicants'         => 'admin/pages/applicants.php',
        'applications'       => 'admin/pages/applications.php',
        'settings'           => 'admin/pages/settings.php',
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