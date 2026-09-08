<?php
/**
 * public/components/header.php
 *
 * Header halaman Karir (public area).
 *
 * Contoh navigasi:
 *   Logo | Home | Karir | Tentang
 */
$logged_in = is_user_logged_in();
?>
<header class="site-header">
    <a class="brand" href="?page=careers">Recruitment<span>Plugin</span></a>
    <nav aria-label="Navigasi utama">
        <a href="?page=careers">Karir</a>
        <a href="?page=application-status">Cek Status</a>
        <?php if ( $logged_in ) : ?>
            <a href="?page=dashboard">Dashboard</a>
            <a class="nav-action" href="?page=logout">Logout</a>
        <?php else : ?>
            <a class="nav-action" href="?page=login">HRD Login</a>
        <?php endif; ?>
    </nav>
</header>
