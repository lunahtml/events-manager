<?php
/**
 * Events List Template
 * 
 * Available variables:
 * @var array $events Array of EventModel objects
 * @var int $limit Number of events to show
 * @var int $total Total number of upcoming events
 * @var bool $show_button Whether to show load more button
 */

use EventsManager\Database\Models\EventModel;

// ВИЗУАЛЬНАЯ ОТЛАДКА - будет видно на странице
echo "<!-- EVENTS DEBUG: count=" . count($events) . ", limit=$limit, total=$total, show_button=" . ($show_button ? 'yes' : 'no') . " -->";

if (empty($events)): ?>
    <div class="events-manager-no-events">
        <p><?php esc_html_e('No upcoming events found.', 'events-manager'); ?></p>
    </div>
<?php else: ?>
    <div class="events-manager-container" 
     data-limit="<?php echo esc_attr($limit); ?>"
     data-offset="<?php echo count($events); ?>"
     data-total="<?php echo esc_attr($total); ?>"
     data-category="<?php echo esc_attr($category); ?>">
        
        <div class="events-list">
            <?php 
            $counter = 0;
            foreach ($events as $event): 
                $counter++;
                /* @var $event EventModel */ 
                echo "<!-- EVENT $counter: " . $event->getTitle() . " -->";
                // Подключаем шаблон элемента
                include EM_PLUGIN_DIR . 'src/Templates/event-item.php';
            endforeach; 
            ?>
        </div>
        
        <?php if ($show_button): ?>
            <div class="events-load-more-wrapper">
                <button class="events-load-more-btn">
                    <span class="btn-text">
                        <?php esc_html_e('Show More Events', 'events-manager'); ?>
                    </span>
                    <span class="btn-loader" style="display: none;"></span>
                </button>
            </div>
        <?php endif; ?>
        
        <div class="events-status-message" style="display: none;"></div>
    </div>
<?php endif; ?>