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
$current = $_GET['page'] ?? 'dashboard';
$nav_items = [
    'dashboard'    => 'Dashboard',
    'vacancies'    => 'Vacancies',
    'applicants'   => 'Applicants',
    'applications' => 'Applications',
    'settings'     => 'Settings',
];
?>
<aside class="admin-sidebar">
    <nav aria-label="Admin menu">
        <?php foreach ( $nav_items as $slug => $label ) : ?>
            <a href="?page=<?= $slug ?>" class="<?= $current === $slug ? 'active' : '' ?>">
                <?= htmlspecialchars( $label, ENT_QUOTES, 'UTF-8' ) ?>
            </a>
        <?php endforeach; ?>
        <a href="?page=logout" class="nav-action">Logout</a>
    </nav>
</aside>
