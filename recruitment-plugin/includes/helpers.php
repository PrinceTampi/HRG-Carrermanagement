<?php

/**
 * helpers.php — helper functions and WordPress mock (sandbox only).
 *
 * Berisi:
 * 1. Helper functions yang dipakai lintas module.
 * 2. Mock WordPress functions agar sandbox dapat berjalan tanpa WordPress.
 *    Hapus bagian mock ini saat dijalankan di WordPress asli.
 */

// ---------------------------------------------------------------------------
// Plugin helper functions
// ---------------------------------------------------------------------------

/**
 * Get the absolute URL to the plugin root.
 */
function get_plugin_url(): string {
    return 'http://localhost:8000/';
}

/**
 * Get the absolute filesystem path to the plugin root.
 */
function get_plugin_path(): string {
    return __DIR__ . '/../';
}

/**
 * Get the URL to a specific public application page.
 *
 * @param string $slug E.g. 'careers', 'job-detail', 'application-form'.
 */
function get_application_url( string $slug = 'careers' ): string {
    return get_plugin_url() . '?page=' . $slug;
}

/**
 * Get the URL to a specific admin page.
 *
 * @param string $slug E.g. 'dashboard', 'vacancies', 'applicants'.
 */
function get_admin_url( string $slug = 'dashboard' ): string {
    return get_plugin_url() . '?page=' . $slug;
}

// ---------------------------------------------------------------------------
// Mock WordPress functions (sandbox only — remove when running in WordPress)
// ---------------------------------------------------------------------------

function is_user_logged_in(): bool {
    return isset( $_SESSION['user'] );
}

function wp_signon( string $username, string $password ): bool {
    $valid_users = [ 'admin' => 'admin123' ];

    if ( ! isset( $valid_users[ $username ] ) || $valid_users[ $username ] !== $password ) {
        return false;
    }

    $_SESSION['user'] = [
        'display_name' => 'Administrator',
        'user_login'   => $username,
        'user_email'   => 'admin@example.test',
        'role'         => 'administrator',
    ];

    return true;
}

function wp_get_current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function wp_logout(): void {
    unset( $_SESSION['user'] );
}
