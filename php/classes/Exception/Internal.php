<?php
/**
 * Main exception file
 */

/**
 * Http exception handler.
 * This class contains the implementation for exceptions
 */
class Exception_Internal extends Exception_Http
{
    /**
     * Object Constructor
     *
     * @param string $message Error message
     * @param int    $code    Error code
     */
    public function __construct($message, $code)
    {
        parent::__construct($message, $code);

        error_log($this);
    }
}