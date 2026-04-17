<?php
/**
 * Minimal Autoloader for Milal Homepage API
 * Handles PSR-4 namespace loading
 */

spl_autoload_register(function ($class) {
    // Project namespace
    $prefix = 'MillalHomepage\\';
    $base_dir = __DIR__ . '/../src/';
    
    // Check if the class uses the project namespace
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return false;
    }
    
    // Get the relative class name
    $relative_class = substr($class, $len);
    
    // Replace namespace separators with directory separators
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // Check if file exists and require it
    if (file_exists($file)) {
        require $file;
        return true;
    }
    
    return false;
});
