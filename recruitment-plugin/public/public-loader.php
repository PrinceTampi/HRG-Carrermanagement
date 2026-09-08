<?php
/**
 * public/public-loader.php
 *
 * Menginisialisasi seluruh public functionality plugin.
 *
 * Tanggung jawab:
 * - register shortcode
 * - register public hooks
 * - enqueue public CSS
 * - enqueue public JS
 *
 * File ini dipanggil oleh class-plugin.php saat WordPress init.
 *
 * TODO: Implementasikan saat plugin dijalankan di WordPress asli.
 */

// Register shortcode untuk halaman Karir.
// add_shortcode( 'recruitment_careers', 'recruitment_render_careers' );

// Enqueue public assets.
// add_action( 'wp_enqueue_scripts', function () {
//     wp_enqueue_style(
//         'recruitment-public',
//         get_plugin_url() . 'assets/css/public.css',
//         [],
//         '1.0.0'
//     );
//     wp_enqueue_script(
//         'recruitment-public',
//         get_plugin_url() . 'assets/js/public.js',
//         [],
//         '1.0.0',
//         true
//     );
// } );
