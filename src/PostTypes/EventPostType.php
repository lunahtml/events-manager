<?php
namespace EventsManager\PostTypes;

/**
 * Event Custom Post Type Registration
 * 
 * @package EventsManager\PostTypes
 */
class EventPostType {
    
    /**
     * @var string Post type key
     */
    public const POST_TYPE = 'event';
    
    /**
     * Register post type
     */
    public function register(): void {
        add_action('init', [$this, 'registerPostType']);
        add_action('init', [$this, 'registerTaxonomies']);
        add_filter('post_updated_messages', [$this, 'customMessages']);
        add_filter('enter_title_here', [$this, 'customTitlePlaceholder'], 10, 2);
    }
    
    /**
     * Register the custom post type
     */
    public function registerPostType(): void {
        $labels = $this->getLabels();
        $args = $this->getArguments($labels);
        
        register_post_type(self::POST_TYPE, $args);
    }
    
    /**
     * Register custom taxonomies
     */
    public function registerTaxonomies(): void {
        // Event Categories
        register_taxonomy(
            'event_category',
            self::POST_TYPE,
            [
                'labels' => [
                    'name' => __('Event Categories', 'events-manager'),
                    'singular_name' => __('Event Category', 'events-manager'),
                    'search_items' => __('Search Categories', 'events-manager'),
                    'all_items' => __('All Categories', 'events-manager'),
                    'parent_item' => __('Parent Category', 'events-manager'),
                    'parent_item_colon' => __('Parent Category:', 'events-manager'),
                    'edit_item' => __('Edit Category', 'events-manager'),
                    'update_item' => __('Update Category', 'events-manager'),
                    'add_new_item' => __('Add New Category', 'events-manager'),
                    'new_item_name' => __('New Category Name', 'events-manager'),
                    'menu_name' => __('Categories', 'events-manager'),
                ],
                'hierarchical' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => ['slug' => 'event-category'],
                'show_in_rest' => true,
            ]
        );
        
        // Event Tags
        register_taxonomy(
            'event_tag',
            self::POST_TYPE,
            [
                'labels' => [
                    'name' => __('Event Tags', 'events-manager'),
                    'singular_name' => __('Event Tag', 'events-manager'),
                    'search_items' => __('Search Tags', 'events-manager'),
                    'popular_items' => __('Popular Tags', 'events-manager'),
                    'all_items' => __('All Tags', 'events-manager'),
                    'edit_item' => __('Edit Tag', 'events-manager'),
                    'update_item' => __('Update Tag', 'events-manager'),
                    'add_new_item' => __('Add New Tag', 'events-manager'),
                    'new_item_name' => __('New Tag Name', 'events-manager'),
                    'separate_items_with_commas' => __('Separate tags with commas', 'events-manager'),
                    'add_or_remove_items' => __('Add or remove tags', 'events-manager'),
                    'choose_from_most_used' => __('Choose from the most used tags', 'events-manager'),
                    'menu_name' => __('Tags', 'events-manager'),
                ],
                'hierarchical' => false,
                'show_ui' => true,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => ['slug' => 'event-tag'],
                'show_in_rest' => true,
            ]
        );
    }
    
    /**
     * Get post type labels
     */
    private function getLabels(): array {
        return [
            'name'                  => __('Events', 'events-manager'),
            'singular_name'         => __('Event', 'events-manager'),
            'menu_name'             => __('Events', 'events-manager'),
            'name_admin_bar'        => __('Event', 'events-manager'),
            'add_new'               => __('Add New', 'events-manager'),
            'add_new_item'          => __('Add New Event', 'events-manager'),
            'new_item'              => __('New Event', 'events-manager'),
            'edit_item'             => __('Edit Event', 'events-manager'),
            'view_item'             => __('View Event', 'events-manager'),
            'all_items'             => __('All Events', 'events-manager'),
            'search_items'          => __('Search Events', 'events-manager'),
            'parent_item_colon'     => __('Parent Events:', 'events-manager'),
            'not_found'             => __('No events found.', 'events-manager'),
            'not_found_in_trash'    => __('No events found in Trash.', 'events-manager'),
            'featured_image'        => __('Event Image', 'events-manager'),
            'set_featured_image'    => __('Set event image', 'events-manager'),
            'remove_featured_image' => __('Remove event image', 'events-manager'),
            'use_featured_image'    => __('Use as event image', 'events-manager'),
            'archives'              => __('Event archives', 'events-manager'),
            'insert_into_item'      => __('Insert into event', 'events-manager'),
            'uploaded_to_this_item' => __('Uploaded to this event', 'events-manager'),
            'filter_items_list'     => __('Filter events list', 'events-manager'),
            'items_list_navigation' => __('Events list navigation', 'events-manager'),
            'items_list'            => __('Events list', 'events-manager'),
        ];
    }
    
    /**
     * Get post type arguments
     */
    private function getArguments(array $labels): array {
        return [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [
                'slug'       => 'events',
                'with_front' => false,
                'feeds'      => true,
                'pages'      => true
            ],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-calendar-alt',
            'supports'           => [
                'title',
                'editor',
                'thumbnail',
                'excerpt',
                'custom-fields',
                'comments',
                'revisions',
                'author'
            ],
            'show_in_rest'       => true, // Enable Gutenberg editor
            'rest_base'          => 'events',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'taxonomies'         => ['event_category', 'event_tag'],
        ];
    }
    
    /**
     * Customize post update messages
     */
    public function customMessages(array $messages): array {
        global $post;
        
        $messages['event'] = [
            0  => '',
            1  => __('Event updated.', 'events-manager'),
            2  => __('Custom field updated.', 'events-manager'),
            3  => __('Custom field deleted.', 'events-manager'),
            4  => __('Event updated.', 'events-manager'),
            5  => isset($_GET['revision']) ? sprintf(
                __('Event restored to revision from %s', 'events-manager'),
                wp_post_revision_title((int) $_GET['revision'], false)
            ) : false,
            6  => __('Event published.', 'events-manager'),
            7  => __('Event saved.', 'events-manager'),
            8  => __('Event submitted.', 'events-manager'),
            9  => sprintf(
                __('Event scheduled for: <strong>%1$s</strong>.', 'events-manager'),
                date_i18n(__('M j, Y @ G:i', 'events-manager'), strtotime($post->post_date))
            ),
            10 => __('Event draft updated.', 'events-manager'),
        ];
        
        return $messages;
    }
    
    /**
     * Customize title placeholder
     */
    public function customTitlePlaceholder(string $placeholder, \WP_Post $post): string {
        if ($post->post_type === self::POST_TYPE) {
            $placeholder = __('Enter event title here', 'events-manager');
        }
        
        return $placeholder;
    }
}