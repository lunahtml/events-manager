<?php
namespace EventsManager\Admin\MetaBoxes;

use EventsManager\PostTypes\EventPostType;
use EventsManager\Database\Models\EventModel;

/**
 * Event Details Metabox
 * 
 * @package EventsManager\Admin\MetaBoxes
 */
class EventDetailsMetabox {
    
    /**
     * @var string Metabox ID
     */
    private const METABOX_ID = 'event_details';
    
    /**
     * Register metabox hooks
     */
    public function register(): void {
        add_action('add_meta_boxes', [$this, 'addMetabox']);
        add_action('save_post_' . EventPostType::POST_TYPE, [$this, 'saveMetabox']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }
    
    /**
     * Add metabox to event edit screen
     */
    public function addMetabox(): void {
        add_meta_box(
            self::METABOX_ID,
            __('Event Details', 'events-manager'),
            [$this, 'renderMetabox'],
            EventPostType::POST_TYPE,
            'normal',
            'high'
        );
    }
    
    /**
     * Render metabox content
     */
    public function renderMetabox(\WP_Post $post): void {
        // Add nonce for security
        wp_nonce_field('event_details_metabox', 'event_details_nonce');
        
        // Get existing values
        $event = EventModel::fromPost($post);
        
        // Load template
        $this->loadTemplate('event-details-metabox', [
            'event_date' => $event->getEventDate(),
            'event_place' => $event->getEventPlace(),
            'post_id' => $post->ID
        ]);
    }
    
    /**
     * Save metabox data
     */
    public function saveMetabox(int $postId): void {
        // Security checks
        if (!$this->canSave($postId)) {
            return;
        }
        
        // Save event date
        if (isset($_POST['event_date'])) {
            $date = sanitize_text_field($_POST['event_date']);
            update_post_meta($postId, 'event_date', $date);
        }
        
        // Save event place
        if (isset($_POST['event_place'])) {
            $place = sanitize_text_field($_POST['event_place']);
            update_post_meta($postId, 'event_place', $place);
        }
        
        // Trigger action for other plugins
        do_action('events_manager_saved_event', $postId);
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueueAssets(string $hook): void {
        global $post;
        
        if (!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }
        
        if (!$post || $post->post_type !== EventPostType::POST_TYPE) {
            return;
        }
        
        wp_enqueue_style(
            'events-admin-css',
            EM_PLUGIN_URL . 'assets/css/admin.css',
            [],
            EM_VERSION
        );
        
        wp_enqueue_script(
            'events-admin-js',
            EM_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery', 'jquery-ui-datepicker'],
            EM_VERSION,
            true
        );
        
        wp_enqueue_style('jquery-ui-style', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
    }
    
    /**
     * Check if metabox can be saved
     */
    private function canSave(int $postId): bool {
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return false;
        }
        
        // Check nonce
        if (!isset($_POST['event_details_nonce']) || 
            !wp_verify_nonce($_POST['event_details_nonce'], 'event_details_metabox')) {
            return false;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $postId)) {
            return false;
        }
        
        // Check if this is an event post type
        if (get_post_type($postId) !== EventPostType::POST_TYPE) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Load template file
     */
    private function loadTemplate(string $template, array $data = []): void {
        extract($data);
        
        $templatePath = EM_PLUGIN_DIR . 'src/Admin/MetaBoxes/views/' . $template . '.php';
        
        if (file_exists($templatePath)) {
            include $templatePath;
        }
    }
}