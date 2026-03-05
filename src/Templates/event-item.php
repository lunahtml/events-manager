<?php
/**
 * Single Event Item Template
 * 
 * @var EventModel $event
 */
?>
<article class="event-item" data-event-id="<?php echo esc_attr($event->getId()); ?>">
    <div class="event-card">
        <?php if (has_post_thumbnail($event->getId())): ?>
            <div class="event-image">
                <a href="<?php echo esc_url(get_permalink($event->getId())); ?>">
                    <?php echo get_the_post_thumbnail($event->getId(), 'medium', [
                        'class' => 'event-thumbnail',
                        'alt' => esc_attr($event->getTitle())
                    ]); ?>
                </a>
            </div>
        <?php endif; ?>
        
        <div class="event-content">
            <h3 class="event-title">
                <a href="<?php echo esc_url(get_permalink($event->getId())); ?>">
                    <?php echo esc_html($event->getTitle()); ?>
                </a>
            </h3>
            
            <div class="event-meta">
                <div class="event-date">
                    <span class="meta-icon">📅</span>
                    <span class="meta-text">
                        <?php echo esc_html($event->getFormattedDate()); ?>
                    </span>
                </div>
                
                <div class="event-place">
                    <span class="meta-icon">📍</span>
                    <span class="meta-text">
                        <?php echo esc_html($event->getEventPlace()); ?>
                    </span>
                </div>
            </div>
            
            <?php 
            // Выводим категории
            $categories = $event->getCategories();
            if (!empty($categories)): 
            ?>
            <div class="event-categories">
                <?php foreach ($categories as $category): ?>
                    <span class="event-category-tag" style="background: <?php echo get_term_meta($category->term_id, 'category_color', true) ?: '#e0e0e0'; ?>">
                        <a href="<?php echo esc_url(get_term_link($category)); ?>">
                            <?php echo esc_html($category->name); ?>
                        </a>
                    </span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <?php 
            // Выводим теги
            $tags = $event->getTags();
            if (!empty($tags)): 
            ?>
            <div class="event-tags">
                <?php foreach ($tags as $tag): ?>
                    <span class="event-tag">
                        <a href="<?php echo esc_url(get_term_link($tag)); ?>">
                            #<?php echo esc_html($tag->name); ?>
                        </a>
                    </span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($event->isUpcoming()): ?>
                <div class="event-badge upcoming">
                    <?php esc_html_e('Upcoming', 'events-manager'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php 
    echo '<!-- DEBUG: is_singular = ' . (is_singular('event') ? 'yes' : 'no') . ' -->';
    echo '<!-- DEBUG: event place = ' . $event->getEventPlace() . ' -->';
    
    if (is_singular('event')) {
        echo '<!-- DEBUG: Добавляем карту для события #' . $event->getId() . ' -->';
        echo '<!-- DEBUG: Место: "' . $event->getEventPlace() . '" -->';
        
        if (!empty($event->getEventPlace())) {
            try {
                $map = new \EventsManager\Frontend\Map\EventMap();
                echo $map->renderEventMap($event);
                echo '<!-- DEBUG: Карта добавлена -->';
            } catch (Exception $e) {
                echo '<!-- DEBUG: Ошибка карты: ' . $e->getMessage() . ' -->';
            }
        } else {
            echo '<!-- DEBUG: Место не указано, карта не добавляется -->';
        }
    } else {
        echo '<!-- DEBUG: Не singular event (это страница списка) -->';
    }
    ?>
    <!-- КОНЕЦ КАРТЫ -->
    
</article>