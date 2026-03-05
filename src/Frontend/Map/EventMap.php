<?php
namespace EventsManager\Frontend\Map;

use EventsManager\Database\Models\EventModel;

/**
 * Event Map Handler
 * 
 * @package EventsManager\Frontend\Map
 */
class EventMap {
    
    /**
     * @var string Map provider (google or yandex)
     */
    private string $provider = 'google';
    
    /**
     * Constructor
     */
    public function __construct(string $provider = null) {
        error_log('=== EVENT MAP CONSTRUCTOR ===');
        error_log('Provider parameter passed: ' . ($provider ?? 'null'));
        
        // Получаем провайдер из настроек WordPress, если не передан параметр
        if ($provider === null) {
            $this->provider = get_option('events_manager_map_provider', 'google');
        } else {
            $this->provider = $provider;
        }
        
        error_log('Saved provider from DB: ' . get_option('events_manager_map_provider', 'google'));
        error_log('Final provider: ' . $this->provider);
        
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
    }
    /**
     * Enqueue map scripts
     */
    public function enqueueScripts(): void {
        if ($this->provider === 'google') {
            wp_enqueue_script(
                'google-maps',
                'https://maps.googleapis.com/maps/api/js?key=' . get_option('events_manager_google_maps_key', ''),
                [],
                null,
                true
            );
        } else {
            wp_enqueue_script(
                'yandex-maps',
                'https://api-maps.yandex.ru/2.1/?apikey=' . get_option('events_manager_yandex_maps_key', '') . '&lang=ru_RU',
                [],
                null,
                true
            );
        }
        
        wp_enqueue_script(
            'events-map',
            EM_PLUGIN_URL . 'assets/js/events-map.js',
            ['jquery', ($this->provider === 'google' ? 'google-maps' : 'yandex-maps')],
            EM_VERSION,
            true
        );
    }
    
    /**
     * Render map for single event
     */
    public function renderEventMap(EventModel $event): string {
        $address = $event->getEventPlace();
        if (empty($address)) {
            return '';
        }
        
        ob_start();
        ?>
        <div class="event-map-container" 
             data-address="<?php echo esc_attr($address); ?>"
             data-provider="<?php echo esc_attr($this->provider); ?>"
             data-event-id="<?php echo esc_attr($event->getId()); ?>">
            <div class="event-map" id="map-<?php echo esc_attr($event->getId()); ?>" style="height: 300px; width: 100%;"></div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Add map to event content
     */
    public function addMapToContent(string $content, EventModel $event): string {
        $map = $this->renderEventMap($event);
        return $content . $map;
    }
}