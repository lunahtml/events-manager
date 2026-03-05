<?php
namespace EventsManager\Core;

use EventsManager\PostTypes\EventPostType;

/**
 * Plugin activation/deactivation handler
 */
class Activator {
    
    /**
     * Plugin activation
     */
    public static function activate(): void {
        // Register post type before flush
        (new EventPostType())->register();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Set default options
        self::setDefaultOptions();
        
        // Create database tables if needed
        self::createTables();
        
        // Log activation
        self::logEvent('activated');
    }
    
    /**
     * Plugin deactivation
     */
    public static function deactivate(): void {
        // Clean up temporary data
        self::cleanupTempData();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Log deactivation
        self::logEvent('deactivated');
    }
    
    /**
     * Set default plugin options
     */
    private static function setDefaultOptions(): void {
        $defaults = [
            'events_per_page' => 10,
            'date_format' => 'd.m.Y',
            'show_past_events' => false,
            'enable_ajax' => true,
            'version' => EM_VERSION
        ];
        
        add_option('events_manager_options', $defaults);
    }
    
    /**
     * Create custom database tables
     */
    private static function createTables(): void {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'events_attendees';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            event_id bigint(20) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(20) DEFAULT NULL,
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY event_id (event_id),
            KEY email (email)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Cleanup temporary data
     */
    private static function cleanupTempData(): void {
        // Delete transients
        delete_transient('events_manager_cache');
        
        // Clear scheduled hooks
        wp_clear_scheduled_hook('events_manager_daily_cleanup');
    }
    
    /**
     * Log plugin events
     */
    private static function logEvent(string $event): void {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log(sprintf(
                '[Events Manager] Plugin %s - Version: %s',
                $event,
                EM_VERSION
            ));
        }
    }
}