<?php
namespace EventsManager\Frontend\Shortcodes;

use EventsManager\Database\Repositories\EventRepository;

/**
 * Events List Shortcode
 * 
 * @package EventsManager\Frontend\Shortcodes
 */
class EventsListShortcode {
    
    /**
     * @var string Shortcode tag
     */
    public const SHORTCODE_TAG = 'events_list';
    
    /**
     * @var EventRepository
     */
    private EventRepository $repository;
    
    /**
     * Constructor
     */
    public function __construct(EventRepository $repository) {
        $this->repository = $repository;
    }
    
    /**
     * Register shortcode
     */
    public function register(): void {
        add_shortcode(self::SHORTCODE_TAG, [$this, 'render']);
    }
    
    /**
     * Render shortcode
     */
    public function render(array $atts = [], ?string $content = null): string {
        // Parse attributes with defaults
        $atts = shortcode_atts([
            'limit' => 3,
            'show_past' => 'false',
            'category' => '',
            'order' => 'ASC',
            'template' => 'grid'
        ], $atts, self::SHORTCODE_TAG);
        
        // Convert types
        $limit = intval($atts['limit']);
        $show_past = filter_var($atts['show_past'], FILTER_VALIDATE_BOOLEAN);
        
        // Build filters with category support
        $filters = $this->buildFilters($atts);
        
        // ===== ОТЛАДКА =====
        error_log('=== EVENTS MANAGER SHORTCODE DEBUG ===');
        error_log('1. Original atts: ' . print_r($atts, true));
        error_log('2. Converted limit: ' . $limit);
        error_log('3. Show past events: ' . ($show_past ? 'yes' : 'no'));
        error_log('4. Category: ' . ($atts['category'] ?: 'none'));
        error_log('5. Filters built: ' . print_r($filters, true));
        // ===================
        
        // Get events
        $events = $this->repository->findAll($filters);
        
        // ===== ОТЛАДКА =====
        error_log('6. Events found: ' . count($events));
        if (count($events) > 0) {
            error_log('7. First event title: ' . $events[0]->getTitle());
            error_log('8. First event date: ' . $events[0]->getEventDate());
            
            // Проверим категории первого события
            $categories = wp_get_post_terms($events[0]->getId(), 'event_category');
            error_log('9. First event categories: ' . print_r($categories, true));
        }
        // ===================
        
        // Get total count for AJAX
        $total_events = $this->repository->countUpcoming();
        
        // ===== ОТЛАДКА =====
        error_log('10. Total upcoming events: ' . $total_events);
        error_log('11. Show button: ' . ((count($events) >= $limit && $total_events > $limit) ? 'yes' : 'no'));
        error_log('=== END DEBUG ===');
        // ===================
        
        // Pass data to JavaScript
        wp_localize_script('events-manager-js', 'events_shortcode_data', [
            'limit' => $limit,
            'total' => $total_events,
            'nonce' => wp_create_nonce('load_more_events')
        ]);
        
        // Render template
        ob_start();
        
        $this->loadTemplate('events-list', [
            'events' => $events,
            'limit' => $limit,
            'total' => $total_events,
            'show_button' => count($events) >= $limit && $total_events > $limit,
            'atts' => $atts,
            'category' => $atts['category'] ?? ''
        ]);
        
        return ob_get_clean();
    }
    
    /**
     * Build filters from shortcode attributes
     */
    private function buildFilters(array $atts): array {
        $filters = [
            'limit' => intval($atts['limit']),
            'order' => $atts['order']
        ];
        
        // Add upcoming filter
        $show_past = filter_var($atts['show_past'], FILTER_VALIDATE_BOOLEAN);
        if (!$show_past) {
            $filters['upcoming'] = true;
        }
        
        // Add category filter if specified
        if (!empty($atts['category'])) {
            $filters['tax_query'] = [
                [
                    'taxonomy' => 'event_category',
                    'field' => 'slug',
                    'terms' => explode(',', $atts['category']),
                    'operator' => 'IN'
                ]
            ];
        }
        
        return apply_filters('events_manager_shortcode_filters', $filters, $atts);
    }
    
    /**
     * Load template file
     */
    private function loadTemplate(string $template, array $data = []): void {
        extract($data);
        
        // Check theme override
        $theme_template = locate_template("events-manager/{$template}.php");
        
        if ($theme_template) {
            include $theme_template;
        } else {
            $plugin_template = EM_PLUGIN_DIR . "src/Templates/{$template}.php";
            if (file_exists($plugin_template)) {
                include $plugin_template;
            }
        }
    }
}