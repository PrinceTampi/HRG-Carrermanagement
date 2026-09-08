<?php
/**
 * Plugin Name: Recruitment Plugin
 * Plugin URI:  https://example.com/recruitment-plugin
 * Description: Custom recruitment plugin with public career pages and HRD admin dashboard.
 * Version:     1.0.0
 * Author:      Your Name
 * License:     GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Load core includes.
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/class-database.php';
require_once __DIR__ . '/includes/class-auth.php';
require_once __DIR__ . '/includes/class-router.php';
require_once __DIR__ . '/includes/class-plugin.php';

// Initialize the plugin.
( new Recruitment_Plugin() )->init();
