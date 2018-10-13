<?php
/**
 * Main exception file
 */

/**
 * Http exception handler.
 * This class contains the implementation for exceptions
 */
class Exception_Http extends Exception
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
    }

    /**
     * Return exception response string (json encoded)
     *
     * @return string
     */
    public function getResponseString()
    {
        $exceptionObj = new stdClass();
        $exceptionObj->code = $this->code;
        $exceptionObj->message = $this->message;
        return json_encode($exceptionObj);
    }

    public function getHeader()
    {
        return sprintf("HTTP/1.1 %s %s", $this->code, $this->_getStatusCode());
    }

    private function _getStatusCode()
    {
        switch($this->code) {
            case 418:
                return 'I\'m a teapot';
            case 417:
                return 'Expectation Failed';
            case 416:
                return 'Requested Range Not Satisfiable';
            case 415:
                return 'Unsupported Media Type';
            case 414:
                return 'Request-URI Too Long';
            case 413:
                return 'Request Entity Too Large';
            case 412:
                return 'Precondition Failed';
            case 411:
                return 'Length Required';
            case 410:
                return 'Gone';
            case 409:
                return 'Conflict';
            case 408:
                return 'Request Timeout';
            case 407:
                return 'Proxy Authentication Required';
            case 406:
                return 'Not Acceptable';
            case 405:
                return 'Method Not Allowed';
            case 404:
                return 'Not Found';
            case 403:
                return 'Forbidden';
            case 402:
                return 'Payment Required';
            case 401:
                return 'Unauthorized';
            case 400:
                return 'Bad Request';
            case 510:
                return 'Not Extended';
            case 509:
                return 'Bandwidth Limit Exceeded';
            case 507:
                return 'Insufficient Storage';
            case 506:
                return 'Variant Also Negotiates';
            case 505:
                return 'HTTP Version Not Supported';
            case 504:
                return 'Gateway Timeout';
            case 503:
                return 'Service Unavailable';
            case 502:
                return 'Bad Gateway';
            case 501:
                return 'Not Implemented';
            case 500:
            default:
                return 'Internal Server Error';
        }
    }

}