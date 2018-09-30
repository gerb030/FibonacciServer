<?php

define('SCRIPT_START_TIME', microtime(true));

require("autoloader.php");
require("../php/include.inc.php");

if (!defined('ROOT_PHP')) {
    define('ROOT_PHP', __DIR__);
}
try {
    Controller_Front::run();
} catch (Exception_Http $e) {
    header('HTTP/1.1 500 Internal Server Error', true, 500);
    Logger::critical('Http Exception: ' . $e->getMessage());
    echo 'Something went wrong :-(';
} catch (Exception_Internal $e) {
    header('HTTP/1.1 500 Internal Server Error', true, 500);
    Logger::critical('Internal Exception: ' . $e->getMessage());
    echo 'Something went wrong :-(';
} catch(PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error', true, 500);
    Logger::critical('PDOException: "'.$e->getMessage() . '" on ' . $e->getFile() . ':' . $e->getLine());
    echo 'Something went wrong :-(';
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error', true, 500);
    Logger::critical('Exception: ' . $e->getMessage());
    echo 'Something went wrong :-(';
}
