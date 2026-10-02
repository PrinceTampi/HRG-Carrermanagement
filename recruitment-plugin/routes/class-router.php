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
        'interview-booking'  => 'public/pages/interview-booking.php',
        'psychotest'         => 'public/pages/psychotest-home.php',
        'psychotest-preparation' => 'public/pages/psychotest-preparation.php',
        'psychotest-device'  => 'public/pages/psychotest-device.php',
        'application-status' => 'public/pages/application-status.php',
        'tracking-detail'    => 'public/pages/application-tracking.php',
        'tracking-result-lamaran-diterima' => 'public/pages/tracking-result-lamaran-diterima.php',
        'tracking-result-seleksi-administrasi' => 'public/pages/tracking-result-seleksi-administrasi.php',
        'tracking-result-tes-psikologi' => 'public/pages/tracking-result-tes-psikologi.php',
        'tracking-result-wawancara-hr' => 'public/pages/tracking-result-wawancara-hr.php',
        'tracking-result-wawancara-user' => 'public/pages/tracking-result-wawancara-user.php',
        'tracking-result-diterima' => 'public/pages/tracking-result-diterima.php',
        'tracking-result-proses-selesai' => 'public/pages/tracking-result-proses-selesai.php',
        'tracking-result-administrasi-diperiksa' => 'public/pages/tracking-result-administrasi-diperiksa.php',
        'tracking-result-keputusan-akhir-tidak-lolos' => 'public/pages/tracking-result-keputusan-akhir-tidak-lolos.php',
        'login'              => 'admin/pages/login.php',
        'dashboard'          => 'admin/pages/dashboard.php',
        'vacancies'          => 'admin/pages/vacancies.php',
        'applicants'         => 'admin/pages/applicants.php',
        'applications'       => 'admin/pages/applications.php',
        'final-decision'     => 'admin/pages/final-decision.php',
        'user-interview'     => 'admin/pages/user-interview.php',
<<<<<<< HEAD
        'user-department'    => 'admin/pages/user-department.php',
=======
        'interview-schedule' => 'admin/pages/interview-schedule.php',
        'user-management'    => 'admin/pages/user-management.php',
>>>>>>> origin/master
        'application-form-builder' => 'admin/pages/application-form-builder.php',
        'psychotest-settings' => 'admin/pages/psychotest-settings.php',
        'question-bank'      => 'admin/pages/question-bank.php',
        'settings'           => 'admin/pages/settings.php',
    ];

    /** @var string[] */
<<<<<<< HEAD
    private const PROTECTED = [ 'dashboard', 'vacancies', 'applicants', 'applications', 'final-decision', 'user-interview', 'user-department', 'application-form-builder', 'psychotest-settings', 'question-bank', 'settings' ];
=======
    private const PROTECTED = [ 'dashboard', 'vacancies', 'applicants', 'applications', 'final-decision', 'user-interview', 'interview-schedule', 'user-management', 'application-form-builder', 'psychotest-settings', 'question-bank', 'settings' ];
>>>>>>> origin/master

    /**
     * @param array<string, mixed> $data
     */
    public function render( string $page, array $data = [] ): void {
        $slug = array_key_exists( $page, self::ROUTES ) ? $page : 'careers';

        $sandbox_preview = in_array( $slug, [ 'user-interview', 'interview-schedule', 'vacancies' ], true )
            && defined( 'RECRUITMENT_SANDBOX' )
            && RECRUITMENT_SANDBOX
            && '1' === sanitize_text_field( wp_unslash( $_GET['daw_ui_preview'] ?? '' ) );

        if ( in_array( $slug, self::PROTECTED, true ) && ! $sandbox_preview ) {
            if ( ! is_user_logged_in() ) {
                $redirect_url = 'user-department' === $slug
                    ? admin_url( 'admin.php?page=daw-user-department' )
                    : recruitment_get_admin_url( $slug );
                wp_safe_redirect( wp_login_url( $redirect_url ) );
                exit;
            }

            $capability = 'user-department' === $slug ? 'manage_user_department' : 'manage_recruitment';
            if ( ! current_user_can( $capability ) ) {
                wp_die( esc_html__( 'Anda tidak memiliki izin untuk mengakses halaman ini.', 'recruitment-plugin' ) );
            }
        }

        if ( 'applicants' === $slug ) {
            $data['plugin_info'] = ( new Recruitment_Plugin() )->get_info();
        }

        $data['page'] = $slug;
        extract( $data, EXTR_SKIP );
        require recruitment_get_plugin_path( self::ROUTES[ $slug ] );
    }
}