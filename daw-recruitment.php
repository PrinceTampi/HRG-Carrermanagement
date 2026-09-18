<?php
/**
 * Plugin Name: Recruitment Plugin
 * Plugin URI:  https://example.com/recruitment-plugin
 * Description: Plugin rekrutmen untuk mengelola lowongan kerja, data pelamar, proses lamaran, dan halaman karier publik melalui dashboard HRD.
 * Version:     1.0.0
 * Author:      PT. Daya Adicipta Wisesa
 * License:     GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/recruitment-plugin/recruitment-plugin.php';

register_activation_hook(
    __FILE__,
    static function (): void {
        $role = get_role( 'administrator' );
        if ( $role ) {
            $role->add_cap( 'manage_recruitment' );
        }
        flush_rewrite_rules();
    }
);

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );