<?php
namespace EventsManager\Admin\Settings;

/**
 * Map Settings Page
 * 
 * @package EventsManager\Admin\Settings
 */
class MapSettings {
    
    /**
     * Register settings
     */
    public function register(): void {
        add_action('admin_menu', [$this, 'addSettingsPage']);
        add_action('admin_init', [$this, 'registerSettings']);
    }
    
    /**
     * Add settings page
     */
    public function addSettingsPage(): void {
        add_options_page(
            __('Events Map Settings', 'events-manager'),
            __('Events Map', 'events-manager'),
            'manage_options',
            'events-map-settings',
            [$this, 'renderSettingsPage']
        );
    }
    
    /**
     * Register settings
     */
    public function registerSettings(): void {
        register_setting('events_manager_options', 'events_manager_google_maps_key');
        register_setting('events_manager_options', 'events_manager_yandex_maps_key');
        register_setting('events_manager_options', 'events_manager_map_provider');
    }
    
    /**
     * Render settings page
     */
    public function renderSettingsPage(): void {
        $provider = get_option('events_manager_map_provider', 'google');
        ?>
        <div class="wrap">
            <h1><?php _e('Events Map Settings', 'events-manager'); ?></h1>
            
            <form method="post" action="options.php" class="events-map-settings">
                <?php settings_fields('events_manager_options'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="map_provider"><?php _e('Map Provider', 'events-manager'); ?></label>
                        </th>
                        <td>
                            <select name="events_manager_map_provider" id="map_provider">
                                <option value="google" <?php selected($provider, 'google'); ?>>
                                    <?php _e('Google Maps', 'events-manager'); ?>
                                </option>
                                <option value="yandex" <?php selected($provider, 'yandex'); ?>>
                                    <?php _e('Yandex Maps', 'events-manager'); ?>
                                </option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="google_maps_key"><?php _e('Google Maps API Key', 'events-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   name="events_manager_google_maps_key" 
                                   id="google_maps_key"
                                   value="<?php echo esc_attr(get_option('events_manager_google_maps_key', '')); ?>"
                                   class="regular-text">
                            <p class="description">
                                <?php _e('Get your API key from', 'events-manager'); ?> 
                                <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">
                                    Google Cloud Console
                                </a>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="yandex_maps_key"><?php _e('Yandex Maps API Key', 'events-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   name="events_manager_yandex_maps_key" 
                                   id="yandex_maps_key"
                                   value="<?php echo esc_attr(get_option('events_manager_yandex_maps_key', '')); ?>"
                                   class="regular-text">
                            <p class="description">
                                <?php _e('Get your API key from', 'events-manager'); ?> 
                                <a href="https://developer.tech.yandex.ru/services/" target="_blank">
                                    Yandex Developer Dashboard
                                </a>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <div class="map-preview">
                <h2><?php _e('Preview', 'events-manager'); ?></h2>
                <p><?php _e('Map will appear here when you have events with addresses.', 'events-manager'); ?></p>
            </div>
        </div>
        <?php
    }
}