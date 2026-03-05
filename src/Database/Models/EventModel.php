<?php
namespace EventsManager\Database\Models;

use DateTime;
use InvalidArgumentException;

/**
 * Event Model
 * 
 * @package EventsManager\Database\Models
 */
class EventModel {
    
    private ?int $id = null;
    private string $title;
    private string $content = '';
    private string $eventDate;
    private string $eventPlace;
    private string $status = 'publish';
    private ?DateTime $createdAt = null;
    private ?DateTime $updatedAt = null;
    
    /**
     * @var array Validation rules
     */
    private const VALIDATION_RULES = [
        'title' => 'required|max:255',
        'eventDate' => 'required|date',
        'eventPlace' => 'required|max:255',
        'status' => 'in:publish,draft,private'
    ];
    
    /**
     * Create from WordPress post
     */
    public static function fromPost(\WP_Post $post): self {
        $model = new self();
        
        $model->id = $post->ID;
        $model->title = $post->post_title;
        $model->content = $post->post_content;
        $model->status = $post->post_status;
        $model->eventDate = get_post_meta($post->ID, 'event_date', true);
        $model->eventPlace = get_post_meta($post->ID, 'event_place', true);
        $model->createdAt = new DateTime($post->post_date);
        $model->updatedAt = new DateTime($post->post_modified);
        
        return $model;
    }
    
    /**
     * Create from array
     */
    public static function fromArray(array $data): self {
        $model = new self();
        
        if (isset($data['id'])) {
            $model->id = (int) $data['id'];
        }
        
        $model->title = $data['title'] ?? '';
        $model->content = $data['content'] ?? '';
        $model->eventDate = $data['event_date'] ?? '';
        $model->eventPlace = $data['event_place'] ?? '';
        $model->status = $data['status'] ?? 'publish';
        
        return $model;
    }
    
    /**
     * Validate model data
     * 
     * @throws InvalidArgumentException
     */
    public function validate(): bool {
        $errors = [];
        
        foreach (self::VALIDATION_RULES as $field => $rules) {
            $value = $this->$field ?? '';
            $rulesList = explode('|', $rules);
            
            foreach ($rulesList as $rule) {
                if ($rule === 'required' && empty($value)) {
                    $errors[] = sprintf('Field %s is required', $field);
                }
                
                if (strpos($rule, 'max:') === 0) {
                    $max = (int) substr($rule, 4);
                    if (strlen($value) > $max) {
                        $errors[] = sprintf('Field %s exceeds maximum length of %d', $field, $max);
                    }
                }
                
                if ($rule === 'date' && !$this->isValidDate($value)) {
                    $errors[] = sprintf('Field %s must be a valid date', $field);
                }
            }
        }
        
        if (!empty($errors)) {
            throw new InvalidArgumentException(implode(', ', $errors));
        }
        
        return true;
    }
    
    /**
     * Check if event is upcoming
     */
    public function isUpcoming(): bool {
        $today = new DateTime('today');
        $eventDate = DateTime::createFromFormat('Y-m-d', $this->eventDate);
        
        return $eventDate && $eventDate >= $today;
    }
    
/**
 * Get formatted date with WordPress timezone
 * 
 * @param string $format Date format (default: d.m.Y)
 * @return string
 */
public function getFormattedDate(string $format = 'd.m.Y'): string {
    if (empty($this->eventDate)) {
        return '';
    }
    
    // Получаем временную зону WordPress
    $wp_timezone = wp_timezone();
    
    // Создаем объект DateTime из даты события
    $date = new \DateTime($this->eventDate, $wp_timezone);
    
    // Форматируем с учетом временной зоны
    return $date->format($format);
}

    /**
     * Get raw date for sorting
     */
    public function getRawDate(): string {
        return $this->eventDate;
    }

    /**
     * Get timestamp with timezone
     */
    public function getTimestamp(): int {
        $wp_timezone = wp_timezone();
        $date = new \DateTime($this->eventDate, $wp_timezone);
        return $date->getTimestamp();
    }
        
    /**
     * Convert to array
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'event_date' => $this->eventDate,
            'event_place' => $this->eventPlace,
            'status' => $this->status,
            'formatted_date' => $this->getFormattedDate(),
            'is_upcoming' => $this->isUpcoming()
        ];
    }
    
    /**
     * Convert to WordPress post array
     */
    public function toWpPostArray(): array {
        return [
            'ID' => $this->id,
            'post_title' => $this->title,
            'post_content' => $this->content,
            'post_status' => $this->status,
            'post_type' => 'event',
            'meta_input' => [
                'event_date' => $this->eventDate,
                'event_place' => $this->eventPlace
            ]
        ];
    }
    
    /**
     * Check if date is valid
     */
    private function isValidDate(string $date): bool {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
    
    // Getters and setters with fluent interface
    
    public function getId(): ?int {
        return $this->id;
    }
    
    public function getTitle(): string {
        return $this->title;
    }
    
    public function setTitle(string $title): self {
        $this->title = $title;
        return $this;
    }
    
    public function getEventDate(): string {
        return $this->eventDate;
    }
    
    public function setEventDate(string $eventDate): self {
        $this->eventDate = $eventDate;
        return $this;
    }
    
    public function getEventPlace(): string {
        return $this->eventPlace;
    }
    
    public function setEventPlace(string $eventPlace): self {
        $this->eventPlace = $eventPlace;
        return $this;
    }
    
    public function getStatus(): string {
        return $this->status;
    }
    
    public function setStatus(string $status): self {
        $this->status = $status;
        return $this;
    }
    /**
 * Get event categories
 * 
 * @return array Array of WP_Term objects
 */
public function getCategories(): array {
    return wp_get_post_terms($this->id, 'event_category');
}

/**
 * Get event tags
 * 
 * @return array Array of WP_Term objects
 */
public function getTags(): array {
    return wp_get_post_terms($this->id, 'event_tag');
}

/**
 * Get categories as HTML links
 */
public function getCategoriesHtml(): string {
    $categories = $this->getCategories();
    if (empty($categories)) {
        return '';
    }
    
    $output = '<div class="event-categories">';
    foreach ($categories as $category) {
        $output .= sprintf(
            '<a href="%s" class="event-category" data-category-id="%d">%s</a>',
            esc_url(get_term_link($category)),
            $category->term_id,
            esc_html($category->name)
        );
    }
    $output .= '</div>';
    
    return $output;
}

/**
 * Get tags as HTML
 */
public function getTagsHtml(): string {
    $tags = $this->getTags();
    if (empty($tags)) {
        return '';
    }
    
    $output = '<div class="event-tags">';
    foreach ($tags as $tag) {
        $output .= sprintf(
            '<a href="%s" class="event-tag" data-tag-id="%d">#%s</a>',
            esc_url(get_term_link($tag)),
            $tag->term_id,
            esc_html($tag->name)
        );
    }
    $output .= '</div>';
    
    return $output;
}

/**
 * Check if event has specific category
 */
public function hasCategory(string $categorySlug): bool {
    $categories = $this->getCategories();
    foreach ($categories as $category) {
        if ($category->slug === $categorySlug) {
            return true;
        }
    }
    return false;
}
}