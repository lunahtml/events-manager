# Events Manager Professional WordPress Plugin

A professional events management system for WordPress with clean architecture and modern development practices.

## Features

- ✅ Custom Post Type 'event' with meta fields
- ✅ Custom taxonomies (Categories & Tags)
- ✅ Meta boxes for event details
- ✅ Shortcode [events_list] with multiple attributes
- ✅ AJAX load more functionality
- ✅ Clean responsive design
- ✅ Full translation ready
- ✅ PSR-4 autoloading
- ✅ Template system with theme override
- ✅ WordPress Coding Standards compliant

## Requirements

- WordPress 5.8+
- PHP 7.4+
- MySQL 5.6+

## Installation

1. Upload the plugin to `/wp-content/plugins/`
2. Run `composer install` in plugin directory
3. Run `npm install && npm run build` for frontend assets
4. Activate the plugin through WordPress admin

## Usage

### Basic Shortcode
[events_list]
[events_list limit="6" show_past="false" category="conferences" order="ASC" template="grid"]

### Parameters
- `limit` - Number of events to show (default: 3)
- `show_past` - Show past events (default: false)
- `category` - Filter by category slug
- `order` - ASC or DESC (default: ASC)
- `template` - Template style (default, grid, list)

## Development

### Architecture
events-manager/
├── events-manager.php                 (главный файл плагина)
├── uninstall.php                       (очистка при удалении)
├── composer.json                       (автолоадинг)
├── README.md                           
├── .gitignore
├── src/
│   ├── Core/
│   │   ├── Plugin.php                   (ядро плагина)
│   │   ├── Loader.php                    (автозагрузка)
│   │   └── Activator.php                 (активация)
│   ├── PostTypes/
│   │   └── EventPostType.php             (регистрация CPT)
│   ├── Admin/
│   │   ├── MetaBoxes/
│   │   │   └── EventDetailsMetabox.php   (метабокс)
│   │   └── AdminAssets.php                (админские ассеты)
│   ├── Frontend/
│   │   ├── Shortcodes/
│   │   │   └── EventsListShortcode.php   (шорткод)
│   │   ├── Ajax/
│   │   │   └── LoadMoreEventsHandler.php (AJAX обработчик)
│   │   └── Assets/
│   │       ├── Css/
│   │       │   └── EventsStyles.php       (регистрация CSS)
│   │       └── Js/
│   │           └── EventsScripts.php      (регистрация JS)
│   ├── Database/
│   │   ├── Models/
│   │   │   └── EventModel.php             (модель события)
│   │   └── Repositories/
│   │       └── EventRepository.php        (репозиторий)
│   ├── Templates/
│   │   ├── event-list.php                 (шаблон списка)
│   │   └── event-item.php                 (шаблон элемента)
│   └── Helpers/
│       └── DateHelper.php                 (хелпер для дат)
├── assets/
│   ├── css/
│   │   ├── events.css                      (основной CSS)
│   │   └── events.min.css                   (минифицированный)
│   ├── js/
│   │   ├── events.js                        (основной JS)
│   │   └── events.min.js                     (минифицированный)
│   └── build/                                (сборка)
├── languages/                                (переводы)
├── tests/                                     (тесты)
├── vendor/                                    (зависимости)
└── package.json                               (для фронтенд сборки)
## Ключевые особенности профессиональной архитектуры:

### ✅ **Паттерны проектирования**
- **Singleton** - для главного класса плагина
- **Repository** - для работы с данными
- **Dependency Injection** - в конструкторах классов

### ✅ **SOLID принципы**
- Single Responsibility - каждый класс отвечает за одну задачу
- Open/Closed - классы открыты для расширения
- Liskov Substitution - интерфейсы и абстракции
- Interface Segregation - четкие интерфейсы
- Dependency Inversion - зависимость от абстракций

### ✅ **WordPress Best Practices**
- Правильные хуки и фильтры
- Безопасность (nonce, sanitization, validation)
- Интернационализация
- Кэширование запросов

### ✅ **Современные практики**
- PSR-4 автозагрузка
- Composer для зависимостей
- NPM для фронтенд сборки
- Система шаблонов с переопределением в теме

### ✅ **Расширяемость**
- Действия и фильтры для кастомизации
- Поддерка тем для переопределения шаблонов
- Готовность к REST API

Эта архитектура позволяет легко масштабировать плагин, добавлять новые фичи и поддерживать код на профессиональном уровне.