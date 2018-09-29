<?php
/**
* Class holding the request parameters passed to the service. Contains
* mechanisms for setting and aquiring properties.
*/
class Request
{
    /**
    * Variable that will hold all the properties passed
    * to the service with one request
    *
    * @access private
    *
    * @var array
    */
    private $_properties;

    /**
     * Variable that will hold all properties
     * used for testing.
     *
     * @access private
     *
     * @var Array
     */
    private $_testProperties;

    /**
    * Variables to hold the _GET, _SERVER, and _REQUEST variables
    * to allow for dependency injection.
    *
    * @access private
    *
    * @var array
    */
    private $_get;
    private $_post;
    private $_server;
    private $_request;

    /**
    * The geolocation resource
    * @var resource
    */
    protected $_gi;

    /**
     * The country code.
     * @var string
     */
    private $_locationCode;

    /**
     * client browser locale code
     * @access  private
     * @var     string
     */
    private $_localeCode;

    /**
     * locales that we support. May want to make this configurable.
     * @access  private
     * @var     string
     */
    private $_acceptedLocales = array(
        'br',
        'chn',
        'de',
        'en',
        'es',
        'fr',
        'gb',
        'id',
        'it',
        'ja',
        'jp',
        'nl',
        'pl',
        'pt',
        'pt-br',
        'ru',
        'se',
        'sv',
        'tr',
        'ua',
        'uk',
        'us',
        'zh',
    );

    /**
    * Array holding messages that can be passed back to the user by
    * the controller
    *
    * @access private
    *
    * @var array
    */
    private $_feedback = array();

    /**
     * @param array $_get
     * @param array $_post
     * @param array $_server
     * @param array $_request
     */
    public function __construct($_get, $_post, $_server, $_request)
    {
        $this->_get         = $_get;
        $this->_post        = $_post;
        $this->_server      = $_server;
        $this->_request     = $_request;
        $this->_localeCode   = null;
        $this->init();
        $this->initTest();
    }

    /**
     * Class getter
     *
     * @access public
     *
     * @param $variable
     *
     * @return mixed|boolean
     */
    public function __get($variable)
    {
        if (isset($this->$variable)) {
            return $this->$variable;
        }

        return false;
    }

    /**
    * Function responsible for populating the properties variable.
    * It works with parameters passed through command line as well as
    * with HTTP requests
    *
    * @access public
    *
    * @return void
    */
    public function init()
    {
        if (isset($this->_server['REQUEST_METHOD'])) {
            $this->_properties = $this->_request;

            if (array_key_exists('parameters', $this->_get)
                && gettype($this->_get['parameters']) != 'array'
                && strlen(trim($this->_get['parameters'])) > 0
            ) {
                $paramData = explode('/', $this->_get['parameters']);
                for ($index = 0, $maxCount = sizeof($paramData); $index < $maxCount; $index += 2) {
                    if (isset($paramData[$index+1])) {
                        if ($paramData[$index+1] == "_") {
                            $value = "";
                        } else {
                            $value = $paramData[$index+1];
                        }
                        $this->setProperty(
                            $paramData[$index], $value
                        );
                    }
                }
            } elseif (isset($this->_get['parameters'])
                && gettype($this->_get['parameters']) == 'array'
                && count($this->_get['parameters']) > 0) {

                $paramData = $this->_get['parameters'];

                foreach ($paramData as $index => $value) {
                    $this->setProperty($index, $value);
                }
            }

            return;
        }

        if (isset($this->_server['argv'])) {
            foreach ($this->_server['argv'] as $arg) {
                if (strpos($arg, '=')) {
                    list($key, $val) = explode("=", $arg);
                    $this->setProperty($key, $val);
                }
            }
        }
    }

    private function initTest()
    {
        if ($this->_properties) {
            foreach ($this->_properties as $key=>$value) {
                if (strpos($key, 'SPILTEST') !== false) {
                    $this->_testProperties[$key] = $value;

                    switch ($key) {
                    case 'SPILTESTCOUNTRYCODE':
                        if (preg_match('/[A-Z]{2}/', $value)) {
                            $this->_locationCode = $value;
                        }
                        break;
                    case 'SPILTESTLOCALECODE':
                        $this->_localeCode = $value;
                        break;
                    }
                }
            }
        }
    }

    /**
     * Getter for a value from the properties array
    *
    * @access public
    *
    * @param string $key The key for which we're trying to get the value
    *
    * @return string $this->properties[$key] The value associated with the
    * key we've passed to the function
    */
    public function getProperty($key)
    {
        if (isset($this->_properties[$key])) {
            return $this->_properties[$key];
        }
    }

    /**
    * get HTTP referrer
    *
    * @access public
    *
    * @return string http referrer
    */
    public function getHttpReferrer()
    {
        if (isset($this->_server['HTTP_REFERER'])) {
            return $this->_server['HTTP_REFERER'];
        }
        return '';
    }

    /**
     * return the hostname
     *
     * @return string http hostname
     */
    public function getHttpHostname()
    {
        if (isset($this->_server['HTTP_HOST'])) {
            return $this->_server['HTTP_HOST'];
        }
        return '';
    }

    /**
     * return the prefered language
     *
     * @return string http hostname
     */
    public function getHttpLanguage()
    {
        if (isset($this->_server['HTTP_ACCEPT_LANGUAGE'])) {
            if (preg_match('/([a-z]{2}-[a-zA-Z]{2})/', $this->_server['HTTP_ACCEPT_LANGUAGE'], $matches)) {
                list($language, $locale) = explode('-', $matches[1]);
                $languageCode = $language .'-'. strtoupper($locale);
                return $languageCode;
            }
        }
        return 'nl-Nl';
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
    public function getIpAddress()
    {
        if (!empty($this->_server['HTTP_X_CLUSTER_CLIENT_IP'])) {
            $ip = $this->_server['HTTP_X_CLUSTER_CLIENT_IP'];
        // default
        } elseif (isset($this->_server['REMOTE_ADDR'])) {
            $ip = $this->_server['REMOTE_ADDR'];
        // dafuq?
        } else {
            $ip = "127.0.0.1";
        }
        Logger::debug('Client ip: ' . $ip);
        return $ip;
    }

    /**
    * Function which returns the country 2 letter code for a given ip
    *
    * @param string $ipaddress The IP for which we're trying to fetch the country code
    *
    * @return string The country code for the passed IP
    */
    public function getCountryByIP($ipaddress = null, $useFallback = true)
    {
        if (!$this->_locationCode) {

            require_once("GeoIP/geoip.inc");

            if (is_null($this->_gi)) {
                $this->_gi = geoip_open("/bigdisk/docs/INC/_default/GeoIP/GeoIP.dat", GEOIP_STANDARD);
            }

            if (is_null($ipaddress)) {
                $ipaddress = $this->getIpAddress();
            }

            $locationCode = geoip_country_code_by_addr($this->_gi, $ipaddress);

            if (!$locationCode) {
                if ($useFallback) {
                    $locationCode = geoip_country_code_by_addr($this->_gi, '77.95.99.162');
                } else {
                    Logger::error('getCountryByIP() called without fallback and failed for client ip: ' . $ipaddress);
                    $locationCode = null;
                }
            }

            $this->_locationCode = $locationCode;
        }

        Logger::debug('Get country by ip: ' . $this->_locationCode);

        return $this->_locationCode;
    }

    /**
    * Setter for the properties array. Sets one key,value pair
    *
    * @access public
    *
    * @param string $key The key we want to set in the array
    * @param string $val The value associated with that key
    *
    * @return void
    */
    public function setProperty($key, $val)
    {
        $this->_properties[$key] = $val;
    }

    /**
    * Function used to add a new message to be returned to the user
    *
    * @access public
    *
    * @param string $msg The message we want to be returned
    *
    * @return void
    */
    public function addFeedback($msg)
    {
        array_push($this->_feedback, $msg);
    }

    /**
    * Getter for the feedback array
    *
    * @access public
    *
    * @return array $feedback The feedback array
    */
    public function getFeedback()
    {
        return $this->_feedback;
    }

    /**
    * Function used to convert the feedback array into a string
    *
    * @access public
    *
    * @param string $separator The separator we want to use between the
    * different messages
    *
    * @return string $message The imploded message formed by all the messages
    *  inside the array
    */
    public function getFeedbackString($separator="\n")
    {
        return implode($separator, $this->_feedback);
    }

    /**
     * Return properties
     *
     * @access public
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    /**
     * Return test properties
     *
     * @access public
     *
     * @return array
     */
    public function getTestProperties()
    {
        return $this->_testProperties;
    }

    /**
     * find out what the client locale code is. Or just revert back to English,
     *  the common language of the universe.
     *
     * @access public
     * @param $default
     *
     * @return string
     */
    public function getLanguageCode($default = 'us')
    {
        if ($this->_localeCode === null) {
            $this->_localeCode = $default;
            $langCodes = $this->getAllLanguageCodes();
            if (count($langCodes) == 0) {
                return $default;
            }

            $allLangCodes = array();

            foreach ($langCodes as $langCode) {
                array_push($allLangCodes, $langCode);
                $langPart = explode('-', $langCode);
                array_push($allLangCodes, $langPart[0]);
            }

            foreach ($allLangCodes as $browserLangCode) {
                $browserLangCode = strtolower($browserLangCode);
                if (in_array($browserLangCode, $this->_acceptedLocales)) {
                    $this->_localeCode = $browserLangCode;
                    break;
                }
            }
        }
        return $this->_localeCode;
    }

    /**
     * @return array
     */
    public function getAllLanguageCodes()
    {
        $langCodes = array();
        if (!array_key_exists('HTTP_ACCEPT_LANGUAGE', $this->_server)) {
            return array();
        }
        $tuples = preg_split("/[;,]{1}/", $this->_server['HTTP_ACCEPT_LANGUAGE']);
        foreach ($tuples as $tuple) {
            if (preg_match("/^[a-zA-Z]{2}\-*[a-zA-Z]{0,2}$/", $tuple)) {
                array_push($langCodes, $tuple);
            }
        }
        return $langCodes;
    }

    /**
     * find out what the client locale code is.
     *
     * @access public
     *
     * @return string;
     */
    public function getLocaleCode()
    {
        return @$this->_server['HTTP_ACCEPT_LANGUAGE'];
    }

    public function getServer()
    {
        return $this->_server;
    }

    public function getBaseUrl(){

        return $this->_server['HTTP_SSL_HTTPS'] == 'true' ? 'https://'.$this->getHttpHostname() : 'http://'.$this->getHttpHostname();
    }
}
