<?php
if (!defined('ROOT_INC')) {
    define('ROOT_INC', __DIR__);
}

/**
 * Autoloader function
 * @param  string $class Contains class name
 * @return void
 */
function __autoload($class)
{
    // Main service
    if (strstr($class, "_")) {
        // Generate path to classfile
        $filePath = str_replace("_", "/", $class);
        @include_once("../php/classes/{$filePath}.php");
        return;
    } else {
        if (!strstr($class, "Test")) {
            @include_once("../php/classes/{$class}.php");
            return;
        }
    }
}

spl_autoload_register('__autoload');


