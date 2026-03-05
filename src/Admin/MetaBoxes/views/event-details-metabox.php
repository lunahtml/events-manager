<?php
/**
 * Event Details Metabox Template
 * 
 * @var string $event_date
 * @var string $event_place
 * @var int $post_id
 */
?>
<div class="event-details-metabox">
    <style>
        .event-details-metabox {
            padding: 12px;
        }
        .event-details-metabox .field-row {
            margin-bottom: 20px;
        }
        .event-details-metabox label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #23282d;
        }
        .event-details-metabox input[type="date"],
        .event-details-metabox input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #8c8f94;
            border-radius: 4px;
            font-size: 14px;
        }
        .event-details-metabox input[type="date"]:focus,
        .event-details-metabox input[type="text"]:focus {
            border-color: #2271b1;
            box-shadow: 0 0 0 1px #2271b1;
            outline: none;
        }
        .event-details-metabox .description {
            color: #646970;
            font-size: 12px;
            margin-top: 4px;
            font-style: italic;
        }
    </style>

    <div class="field-row">
        <label for="event_date"><?php _e('Event Date', 'events-manager'); ?></label>
        <input type="date" 
               id="event_date" 
               name="event_date" 
               value="<?php echo esc_attr($event_date); ?>" 
               class="widefat"
               placeholder="YYYY-MM-DD">
        <p class="description"><?php _e('Select the date of the event', 'events-manager'); ?></p>
    </div>

    <div class="field-row">
        <label for="event_place"><?php _e('Event Place', 'events-manager'); ?></label>
        <input type="text" 
               id="event_place" 
               name="event_place" 
               value="<?php echo esc_attr($event_place); ?>" 
               class="widefat"
               placeholder="<?php _e('e.g., Conference Hall, Online, etc.', 'events-manager'); ?>">
        <p class="description"><?php _e('Enter the venue or location of the event', 'events-manager'); ?></p>
    </div>
</div>