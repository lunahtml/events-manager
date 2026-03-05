<?php
namespace EventsManager\Core;

use EventsManager\PostTypes\EventPostType;
use EventsManager\Admin\MetaBoxes\EventDetailsMetabox;
use EventsManager\Frontend\Shortcodes\EventsListShortcode;
use EventsManager\Frontend\Ajax\LoadMoreEventsHandler;
use EventsManager\Frontend\Assets\EventsStyles;
use EventsManager\Frontend\Assets\EventsScripts;
use EventsManager\Database\Repositories\EventRepository;
use EventsManager\Admin\Settings\MapSettings;
use EventsManager\Frontend\Map\EventMap;      

/**
 * Class Plugin - Main plugin class
 * 
 * @package EventsManager\Core
 */
final class Plugin {
    
    /**
     * @var Plugin|null Singleton instance
     */
    private static ?self $instance = null;
    
    /**
     * @var EventRepository Repository instance
     */
    private EventRepository $repository;
    
    /**
     * Private constructor for singleton
     */
    private function __construct() {
        $this->repository = new EventRepository();
    }
    
    /**
     * Get plugin instance
     */
    public static function getInstance(): self {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Initialize plugin components
     */
    public function initialize(): void {
        $this->registerHooks();
        $this->initComponents();
    }
    
    /**
     * Register WordPress hooks
     */
    private function registerHooks(): void {
        add_action('init', [$this, 'loadTextDomain']);
        add_filter('plugin_action_links_' . EM_PLUGIN_BASENAME, [$this, 'addActionLinks']);
    }
    
    /**
     * Initialize all plugin components
     */

private function initComponents(): void {
    // Post Types - регистрируем всегда
    (new EventPostType())->register();
    
    // Admin components - только в админке
    if (is_admin()) {
        (new EventDetailsMetabox())->register();
        (new MapSettings())->register(); // Settings page в админке
    }
    
    // Frontend components - для всех
    (new EventsListShortcode($this->repository))->register();
    (new LoadMoreEventsHandler($this->repository))->register();
    
    // Assets - для всех
    (new EventsStyles())->register();
    (new EventsScripts())->register();

    // Map - подключаем через хук wp, когда уже известен тип страницы
    add_action('wp', [$this, 'initMap']);
    add_filter('template_include', [$this, 'loadEventTemplate']);
}
/**
 * Load custom template for events
 */
public function loadEventTemplate($template) {
    if (is_singular(EventPostType::POST_TYPE)) {
        // Ищем шаблон в плагине
        $plugin_template = EM_PLUGIN_DIR . 'src/Templates/single-event.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    return $template;
}
    /**
     * Initialize map on frontend
     */
    public function initMap(): void {
        if (is_singular(EventPostType::POST_TYPE)) {
            $map_provider = get_option('events_manager_map_provider', 'google');
            new EventMap($map_provider);
        }
    }
    
    /**
     * Load plugin text domain for translations
     */
    public function loadTextDomain(): void {
        load_plugin_textdomain(
            'events-manager',
            false,
            dirname(EM_PLUGIN_BASENAME) . '/languages'
        );
    }
    
    /**
     * Add action links to plugins page
     */
    public function addActionLinks(array $links): array {
        $customLinks = [
            '<a href="' . admin_url('edit.php?post_type=event') . '">' . 
            __('Manage Events', 'events-manager') . '</a>',
            '<a href="' . admin_url('post-new.php?post_type=event') . '">' . 
            __('Add New', 'events-manager') . '</a>'
        ];
        
        return array_merge($customLinks, $links);
    }
    
    /**
     * Get repository instance
     */
    public function getRepository(): EventRepository {
        return $this->repository;
    }
}