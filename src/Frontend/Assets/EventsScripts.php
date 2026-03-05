<?php
namespace EventsManager\Frontend\Assets;

/**
 * Events Scripts Handler
 * 
 * @package EventsManager\Frontend\Assets
 */
class EventsScripts {
    
    /**
     * Register scripts
     */
    public function register(): void {
        add_action('wp_enqueue_scripts', [$this, 'enqueue']);
    }
    
    /**
     * Enqueue scripts
     */
    public function enqueue(): void {
        // Main script
        wp_enqueue_script(
            'events-manager-js',
            EM_PLUGIN_URL . 'assets/js/events-manager.js',
            [],
            EM_VERSION,
            true
        );
        
        // Minified version for production
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            wp_enqueue_script(
                'events-manager-js-min',
                EM_PLUGIN_URL . 'assets/js/events-manager.min.js',
                [],
                EM_VERSION,
                true
            );
        }
        
        // Localize script for AJAX
        wp_localize_script('events-manager-js', 'events_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('load_more_events')
        ]);
    }
}