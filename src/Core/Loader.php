<?php
namespace EventsManager\Core;

/**
 * PSR-4 autoloader implementation
 */
class Loader {
    
    /**
     * @var array Namespace prefixes with base directories
     */
    protected static array $prefixes = [];
    
    /**
     * Register autoloader
     */
    public static function register(): void {
        spl_autoload_register([__CLASS__, 'loadClass']);
        
        // Register plugin namespace
        self::addNamespace('EventsManager', EM_PLUGIN_DIR . 'src');
    }
    
    /**
     * Add namespace prefix
     */
    public static function addNamespace(string $prefix, string $baseDir): void {
        $prefix = trim($prefix, '\\') . '\\';
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . '/';
        
        self::$prefixes[$prefix] = $baseDir;
    }
    
    /**
     * Load class file
     */
    public static function loadClass(string $class): ?string {
        $prefix = $class;
        
        while (false !== $pos = strrpos($prefix, '\\')) {
            $prefix = substr($class, 0, $pos + 1);
            $relativeClass = substr($class, $pos + 1);
            
            if ($mappedFile = self::loadMappedFile($prefix, $relativeClass)) {
                return $mappedFile;
            }
            
            $prefix = rtrim($prefix, '\\');
        }
        
        return null;
    }
    
    /**
     * Load mapped file
     */
    protected static function loadMappedFile(string $prefix, string $relativeClass): ?string {
        if (!isset(self::$prefixes[$prefix])) {
            return null;
        }
        
        $file = self::$prefixes[$prefix] 
                . str_replace('\\', '/', $relativeClass) 
                . '.php';
        
        if (file_exists($file)) {
            require $file;
            return $file;
        }
        
        return null;
    }
}

// Register autoloader
Loader::register();