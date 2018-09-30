<?php
/**
* Provides access to config values
*/
class Config
{
    /**
    * Variable holding the read xml config
    *
    * @var string
    */
    private $_config;

    /**
    * The singleton variable
    *
    * @var Config
    */
    private static $_instance;

    /**
    * Variable holding the settings read from the xml
    *
    * @var String
    */
    private $_options;

    /**
     * Is this initialised.
     *
     * @var boolean
     */
    private $_isInitialised = false;

    /**
    * Class constructor, is being called by
    * the init function
    *
    * @access private
    *
    * @return void
    */
    private function __construct()
    {
    }

    /**
    * The singleton function to instantiate the class
    *
    * @access public
    *
    * @return Config $instance An instance of the class
    */
    static function instance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @param null|string $config_filename
     * @return Config
     */
    public function init($config_filename=null)
    {
        if (defined('DB_APP_USER')) {
            $this->_options['db_app_user'] = DB_APP_USER;
        } else {
            $this->_options['db_app_user'] = 'default_db_user';
        }
        if (defined('DB_APP_PASSWORD')) {
            $this->_options['db_app_password'] = DB_APP_PASSWORD;
        } else {
            $this->_options['db_app_password'] = 'default_db_password';
        }
        if (defined('DB_APP_HOSTNAME')) {
            $this->_options['db_hostname'] = DB_APP_HOSTNAME;
        } else {
            $this->_options['db_hostname'] = 'localhost';
        }
        if (defined('DB_APP_DATABASE')) {
            $this->_options['db_database'] = DB_APP_DATABASE;
        } else {
            $this->_options['db_database'] = 'planningpoker';
        }
        if (defined('LOGGING')) {
            $this->_options['logging'] = LOGGING;
        } else {
            $this->_options['logging'] = false;
        }

        if (isset($_SERVER['HTTP_HOST'])) {
            if (strstr($_SERVER['HTTP_HOST'], 'poker.slumbrous.com'))    {
                $this->_options['environment'] = 'live';
            } else {
                $this->_options['environment'] = 'dev';
            }
        }
        return $this;
    }

    /**
     * Get config options
     *
     * @access public
     *
     * @return SimpleXMLElement
     */
    public function getOption($key)
    {
        if (!$this->_isInitialised) $this->init();
        if (!array_key_exists($key, $this->_options)) {
            throw new Exception("Unknown key ".$key);
        }
        return $this->_options[$key];
    }

    /**
     * Determine whether the server is running in production. Returns true if this is the case.
     *
     * @return bool
     */
    public function isProduction()
    {
        if (!isset($this->_options['environment'])) {
            return true;
        } else if ($this->_options['environment'] == 'dev') {
            return false;
        } else {
            return true;
        }
    }

}
