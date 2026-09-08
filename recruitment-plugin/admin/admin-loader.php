<?php
/**
 * admin/admin-loader.php
 *
 * Menginisialisasi seluruh admin functionality plugin.
 *
 * Tanggung jawab:
 * - register routes
 * - register admin hooks
 * - check authentication
 * - check capability
 * - enqueue admin CSS
 * - enqueue admin JS
 *
 * TODO: Implementasikan saat plugin dijalankan di WordPress asli.
 */

// Enqueue admin assets.
// add_action( 'admin_enqueue_scripts', function () {
//     wp_enqueue_style(
//         'recruitment-admin',
//         get_plugin_url() . 'assets/css/admin.css',
//         [],
//         '1.0.0'
//     );
//     wp_enqueue_script(
//         'recruitment-admin',
//         get_plugin_url() . 'assets/js/admin.js',
//         [],
//         '1.0.0',
//         true
//     );
// } );
