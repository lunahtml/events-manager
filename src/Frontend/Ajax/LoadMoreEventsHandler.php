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
        
        // Build filters
        $filters = [
            'limit' => $limit,
            'offset' => $offset,
            'upcoming' => true,
            'order' => 'ASC'
        ];
        
        // 👇 ДОБАВЛЯЕМ КАТЕГОРИЮ В ФИЛЬТР
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
        
        // Render HTML
        ob_start();
        foreach ($events as $event) {
            $date = $event->getFormattedDate();
            $place = $event->getEventPlace();
            $categories = $event->getCategories();
            ?>
            <div class="event-item">
                <h3 class="event-title"><?php echo esc_html($event->getTitle()); ?></h3>
                <div class="event-meta">
                    <span class="event-date">📅 <?php echo esc_html($date); ?></span>
                    <span class="event-place">📍 <?php echo esc_html($place); ?></span>
                </div>
                
                <?php if (!empty($categories)): ?>
                <div class="event-categories">
                    <?php foreach ($categories as $cat): ?>
                        <span class="event-category-tag">
                            <?php echo esc_html($cat->name); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php
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