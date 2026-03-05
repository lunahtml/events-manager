<?php
/**
 * Single Event Template
 * 
 * @package EventsManager\Templates
 */

use EventsManager\Database\Models\EventModel;

get_header();

while (have_posts()) : the_post();
    $event = EventModel::fromPost($post);
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <?php if (has_post_thumbnail()): ?>
                    <div class="post-thumbnail">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>
                
                <h1 class="entry-title"><?php the_title(); ?></h1>
                
                <div class="event-meta-header">
                    <span class="event-date">📅 <?php echo $event->getFormattedDate(); ?></span>
                    <span class="event-place">📍 <?php echo $event->getEventPlace(); ?></span>
                </div>
                
                <?php 
                $categories = $event->getCategories();
                if (!empty($categories)): ?>
                    <div class="event-categories">
                        <?php foreach ($categories as $cat): ?>
                            <span class="event-category-tag">
                                <a href="<?php echo get_term_link($cat); ?>">
                                    <?php echo $cat->name; ?>
                                </a>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </header>

            <div class="entry-content">
                <?php the_content(); ?>
                
                <?php 
                // Карта
                if (!empty($event->getEventPlace())) {
                    $map = new \EventsManager\Frontend\Map\EventMap();
                    echo $map->renderEventMap($event);
                }
                ?>
            </div>

            <footer class="entry-footer">
                <?php 
                $tags = $event->getTags();
                if (!empty($tags)): ?>
                    <div class="event-tags">
                        <strong><?php _e('Tags:', 'events-manager'); ?></strong>
                        <?php foreach ($tags as $tag): ?>
                            <a href="<?php echo get_term_link($tag); ?>" class="event-tag">
                                #<?php echo $tag->name; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </footer>
        </article>
    </main>
</div>

<?php
endwhile;
get_sidebar();
get_footer();