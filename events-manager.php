<?php
/**
 * Plugin Name: Events Manager Pro
 * Plugin URI: 
 * Description: Professional events management system for WordPress
 * Version: 1.0.0
 * Author: Anna
 * Author URI: 
 * License: GPL v2 or later
 * Text Domain: events-manager
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

namespace EventsManager;

// Защита от прямого доступа
if (!defined('ABSPATH')) {
    exit;
}

// константы плагина
define('EM_VERSION', '1.0.0');
define('EM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('EM_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Автозагрузка классов
require_once EM_PLUGIN_DIR . 'src/Core/Loader.php';

// зависимости если есть Composer
if (file_exists(EM_PLUGIN_DIR . 'vendor/autoload.php')) {
    require_once EM_PLUGIN_DIR . 'vendor/autoload.php';
}

// Инициализация плагина
use EventsManager\Core\Plugin;

function init_plugin(): void {
    static $plugin = null;
    
    if (null === $plugin) {
        $plugin = Plugin::getInstance();
        $plugin->initialize();
    }
}

// Запускаем плагин
add_action('plugins_loaded', 'EventsManager\\init_plugin');

// Хуки активации/деактивации
register_activation_hook(__FILE__, ['EventsManager\Core\Activator', 'activate']);
register_deactivation_hook(__FILE__, ['EventsManager\Core\Activator', 'deactivate']);