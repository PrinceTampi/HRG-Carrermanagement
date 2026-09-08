<?php
/**
 * admin/components/header.php
 *
 * Header dashboard HRD.
 */
$user = wp_get_current_user();
?>
<header class="site-header">
    <a class="brand" href="?page=dashboard">Recruitment<span>Plugin</span></a>
    <nav aria-label="Admin navigasi">
        <a href="?page=careers">← Public Site</a>
        <?php if ( $user ) : ?>
            <span><?= htmlspecialchars( $user['display_name'], ENT_QUOTES, 'UTF-8' ) ?></span>
        <?php endif; ?>
        <a class="nav-action" href="?page=logout">Logout</a>
    </nav>
</header>
