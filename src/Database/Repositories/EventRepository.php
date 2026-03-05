<?php
namespace EventsManager\Database\Repositories;

use EventsManager\Database\Models\EventModel;
use WP_Query;
use InvalidArgumentException;

/**
 * Event Repository Pattern
 * 
 * @package EventsManager\Database\Repositories
 */
class EventRepository {
    
    /**
     * @var string Post type
     */
    private const POST_TYPE = 'event';
    
    /**
     * Find event by ID
     */
    public function find(int $id): ?EventModel {
        $post = get_post($id);
        
        if (!$post || $post->post_type !== self::POST_TYPE) {
            return null;
        }
        
        return EventModel::fromPost($post);
    }
    
    /**
     * Find all events with filters
     */
    public function findAll(array $filters = []): array {
        $args = $this->buildQueryArgs($filters);
        $query = new WP_Query($args);
        
        return array_map(function($post) {
            return EventModel::fromPost($post);
        }, $query->posts);
    }
    
    /**
     * Find upcoming events
     */
    public function findUpcoming(int $limit = 10, int $offset = 0): array {
        $filters = [
            'limit' => $limit,
            'offset' => $offset,
            'upcoming' => true,
            'order' => 'ASC'
        ];
        
        return $this->findAll($filters);
    }
    
    /**
     * Count upcoming events
     */
    public function countUpcoming(): int {
        $args = [
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_key' => 'event_date',
            'meta_type' => 'DATE',
            'meta_query' => [
                [
                    'key' => 'event_date',
                    'value' => current_time('Y-m-d'),
                    'compare' => '>=',
                    'type' => 'DATE'
                ]
            ],
            'fields' => 'ids'
        ];
        
        $query = new WP_Query($args);
        return $query->found_posts;
    }
    
    /**
     * Save event
     */
    public function save(EventModel $event): int {
        $event->validate();
        
        $data = $event->toWpPostArray();
        
        if ($event->getId()) {
            $result = wp_update_post($data, true);
        } else {
            $result = wp_insert_post($data, true);
        }
        
        if (is_wp_error($result)) {
            throw new InvalidArgumentException($result->get_error_message());
        }
        
        return $result;
    }
    
    /**
     * Delete event
     */
    public function delete(int $id): bool {
        $result = wp_delete_post($id, true);
        return (bool) $result;
    }
    
/**
 * Build query arguments from filters
 */
private function buildQueryArgs(array $filters): array {
    $args = [
        'post_type' => self::POST_TYPE,
        'post_status' => $filters['status'] ?? 'publish',
        'posts_per_page' => $filters['limit'] ?? 10,
        'offset' => $filters['offset'] ?? 0,
        'meta_key' => 'event_date',
        'orderby' => 'meta_value',
        'order' => $filters['order'] ?? 'ASC',
        'meta_type' => 'DATE'
    ];
    
    // Add date filter for upcoming events
    if (!empty($filters['upcoming'])) {
        $args['meta_query'] = [
            [
                'key' => 'event_date',
                'value' => current_time('Y-m-d'),
                'compare' => '>=',
                'type' => 'DATE'
            ]
        ];
    }
    
    // ========== ВАЖНО: Добавляем поддержку tax_query ==========
    if (!empty($filters['tax_query'])) {
        $args['tax_query'] = $filters['tax_query'];
        
        // Добавим отладку
        error_log('Tax query applied: ' . print_r($filters['tax_query'], true));
    }
    // =========================================================
    
    // Add custom meta filters
    if (!empty($filters['meta_query'])) {
        if (isset($args['meta_query'])) {
            $args['meta_query'] = array_merge($args['meta_query'], $filters['meta_query']);
        } else {
            $args['meta_query'] = $filters['meta_query'];
        }
    }
    
    // Add date range filter
    if (!empty($filters['date_from'])) {
        $args['meta_query'][] = [
            'key' => 'event_date',
            'value' => $filters['date_from'],
            'compare' => '>=',
            'type' => 'DATE'
        ];
    }
    
    if (!empty($filters['date_to'])) {
        $args['meta_query'][] = [
            'key' => 'event_date',
            'value' => $filters['date_to'],
            'compare' => '<=',
            'type' => 'DATE'
        ];
    }
    
    // Отладка финальных аргументов
    error_log('Final WP_Query args: ' . print_r($args, true));
    
    return $args;
}
}