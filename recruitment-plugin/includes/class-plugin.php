<?php

/**
 * Recruitment_Plugin — main plugin bootstrap / controller.
 *
 * Tanggung jawab:
 * - Initialize public module
 * - Initialize admin module
 * - Register hooks
 */
class Recruitment_Plugin {

    /**
     * Plugin info used across the system.
     *
     * @return array<string, string>
     */
    public function get_info(): array {
        return [
            'name'      => 'Recruitment Plugin',
            'status'    => 'Active',
            'version'   => '1.0.0',
            'site_url'  => get_plugin_url(),
            'shortcode' => '[recruitment_careers]',
        ];
    }

    /**
     * Bootstrap the plugin (hooks, loaders, etc.).
     * Called from recruitment-plugin.php on init.
     */
    public function init(): void {
        // TODO: Register WordPress hooks here.
        // add_action( 'init', [ $this, 'register_routes' ] );
    }
}
