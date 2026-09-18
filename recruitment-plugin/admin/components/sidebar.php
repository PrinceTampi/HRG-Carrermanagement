<?php
/**
 * admin/components/sidebar.php
 *
 * Sidebar navigasi dashboard HRD.
 *
 * Navigasi:
 * - Dashboard
 * - Vacancies
 * - Applicants
 * - Applications
 * - Settings
 * - Logout
 */
$current = sanitize_key( $_GET['page'] ?? 'recruitment-dashboard' );
$nav_items = [
    'recruitment-dashboard' => 'Dashboard',
    'vacancies'    => 'Vacancies',
    'applicants'   => 'Applicants',
    'applications' => 'Applications',
    'settings'     => 'Settings',
];
?>
<aside class="admin-sidebar">
    <nav aria-label="Admin menu">
        <?php foreach ( $nav_items as $slug => $label ) : ?>
            <a href="<?= esc_url( recruitment_get_admin_url( $slug ) ) ?>" class="<?= $current === $slug ? 'active' : '' ?>">
                <?= esc_html( $label ) ?>
            </a>
        <?php endforeach; ?>
        <a href="<?= esc_url( wp_logout_url( home_url( '/' ) ) ) ?>" class="nav-action">Logout</a>
    </nav>
</aside>
