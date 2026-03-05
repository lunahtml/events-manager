<?php
namespace EventsManager\Frontend\Ajax;

use EventsManager\Database\Repositories\EventRepository;

class LoadMoreEventsHandler {
    
    private EventRepository $repository;
    
    public function __construct(EventRepository $repository) {
        $this->repository = $repository;
    }
    
    public function register(): void {
        add_action('wp_ajax_load_more_events', [$this, 'handle']);
        add_action('wp_ajax_nopriv_load_more_events', [$this, 'handle']);
    }
    
    public function handle(): void {
        // Log AJAX request
        error_log('=== AJAX LOAD MORE REQUEST ===');
        error_log('POST data: ' . print_r($_POST, true));
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'load_more_events')) {
            error_log('Nonce verification failed');
            wp_send_json_error(['message' => 'Security check failed']);
        }
        
        $offset = intval($_POST['offset'] ?? 0);
        $limit = intval($_POST['limit'] ?? 3);
        $category = sanitize_text_field($_POST['category'] ?? '');
        
        error_log("Loading events: offset=$offset, limit=$limit, category=$category");
        
        // Build filters with category
        $filters = [
            'limit' => $limit,
            'offset' => $offset,
            'upcoming' => true,
            'order' => 'ASC'
        ];
        
        if (!empty($category)) {
            $filters['tax_query'] = [
                [
                    'taxonomy' => 'event_category',
                    'field' => 'slug',
                    'terms' => explode(',', $category),
                    'operator' => 'IN'
                ]
            ];
            error_log('Category filter applied: ' . $category);
        }
        
        // Get events
        $events = $this->repository->findAll($filters);
        
        error_log('Found ' . count($events) . ' events');
        
        // 👇 ИСПОЛЬЗУЕМ ШАБЛОН
        ob_start();
        foreach ($events as $event) {
            include EM_PLUGIN_DIR . 'src/Templates/event-item.php';
        }
        $html = ob_get_clean();
        
        $response = [
            'html' => $html,
            'count' => count($events),
            'total' => $this->repository->countUpcoming()
        ];
        
        error_log('Sending response: ' . print_r($response, true));
        
        wp_send_json_success($response);
    }
}