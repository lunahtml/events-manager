<?php
namespace EventsManager\Frontend\Assets;

/**
 * Events Styles Handler
 * 
 * @package EventsManager\Frontend\Assets
 */
class EventsStyles {
    
    /**
     * Register styles
     */
    public function register(): void {
        add_action('wp_enqueue_scripts', [$this, 'enqueue']);
    }
    
    /**
     * Enqueue styles
     */
    public function enqueue(): void {
        // Main stylesheet
        wp_enqueue_style(
            'events-manager-css',
            EM_PLUGIN_URL . 'assets/css/events-manager.css',
            [],
            EM_VERSION
        );
        
        // Minified version for production
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            wp_enqueue_style(
                'events-manager-css-min',
                EM_PLUGIN_URL . 'assets/css/events-manager.min.css',
                [],
                EM_VERSION
            );
        }
    }
}