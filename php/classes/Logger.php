<?php

/**
 * Logger class
 *
 * Levels:
 * -------
 *
 *  1 = Critical
 *  2 = Error
 *  3 = Warning
 *  4 = Information
 *  5 = Debug
 *
 * @copyright 2011 Spil Games
 */
class Logger
{
    /**
     * Error level
     * @var int
    */
    private static $_level = 3;

    public static function setLevel($level = 3)
    {
        self::$_level = $level;
    }

    /**
     * Write log entry
     *
     * @static
     * @param $message
     * @param $title
     * @param $level
     * @param null $method
     * @return mixed
     */
    private static function write($message, $title, $level, $method = null)
    {
        $config = Config::instance();
        $options = $config->getOptions();
        if (!$options) return;
        if (!isset($options['logging']) || $options['logging']['enabled'] == false || self::$_level < $level) {
            return;
        }
        $logfilename = $options->logging->logfile;
        $get = json_encode($_GET);
        $post = json_encode($_POST);
        $ip = self::getIpAddress();
        $timestamp = date("d/m/Y H:i:s");

        if ($method == null) {
            $method = self::getCaller();
        }


        $entry = "[{$title}] [{$timestamp}] [{$ip}] [get:{$get}] [post:{$post}] ";
        if ($method !== null) {
            $entry .= "[method:{$method}] ";
        }
        $entry .= "{$message}" . PHP_EOL;
        error_log($entry);

    }

    /**
     * Write a critical log entry
     *
     * @access public
     * @param string $message
     * @param string $method
     */
    public static function critical($message, $method = null)
    {
        self::write($message, 'CRITICAL', 1, $method);
    }

    /**
     * Write a error log entry
     *
     * @access public
     * @param string $message
     * @param string $method
     */
    public static function error($message, $method = null)
    {
        self::write($message, 'ERROR', 2, $method);
    }


    /**
     * Write a warning log entry
     *
     * @access public
     * @param string $message
     * @param string $method
     */
    public static function warning($message, $method = null)
    {
        self::write($message, 'WARNING', 3, $method);
    }


    /**
     * Write a information log entry
     *
     * @access public
     * @param string $message
     * @param string $method
     */
    public static function info($message, $method = null)
    {
        self::write($message, 'INFORMATION', 4, $method);
    }

    /**
     * Write a debug log entry
     *
     * @access public
     * @param string $message
     * @param string $method
     */
    public static function debug($message, $method = null)
    {
        self::write($message, 'DEBUG', 5, $method);
    }

    /**
     * Get client ip address
     *
     * Order priority
     * - HTTP_X_PAYMENTS_COUNTRYIP  - for testing by developers
     * - HTTP_SPILTESTIPADDRESS     - for testing by developers
     * - HTTP_X_CLUSTER_CLIENT_IP   - set by loadbalancers on staging and live
     * - REMOTE_ADDR                - default fallback
     *
     * @access public
     * @return string
     */
    public static function getIpAddress()
    {
        if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        // default
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        // dafuq?
        } else {
            $ip = "Unknown IP";
        }

        return $ip;
    }

    private static function getCaller()
    {
        $trace=debug_backtrace();

        if (count($trace) >= 3) {
            $caller=@$trace[3];

            $function = $caller['function'];
            if (isset($caller['class'])) {
                $class = $caller['class'];
            }
            $method = @"{$class}::{$function}";

            return $method;
        }

        return false;
    }
    
}
