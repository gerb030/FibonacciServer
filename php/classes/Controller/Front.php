<?php
/**
 * Controller class
 */
class Controller_Front
{
    /**
     * Variable holding the basic configuration data for the Controller service
     *
     * @var Config
     */
    private static $_config;

    /**
     * Main function for instantiating a new Controller
     * and calling the handling functions
     *
     * @access public
     *
     * @return void
     */
    static function run()
    {
        self::_handleRequest();
    }

    /**
     * Function responsible for handling the request inside the controller
     * based on the request that's being made
     *
     * @access private
     *
     * @return string
     */
    private static function _handleRequest()
    {
        self::$_config = Config::instance();
        self::$_config->init();
        //Logger::setLevel(self::$_config->getOptions()->logging->level);
        $request = new Request($_GET, $_POST, $_SERVER, $_REQUEST);
        $apiController = new Controller_Api(
            $request, self::$_config
        );
//            $apiController->setFactory(new Model_Factory());
        $apiController->handleRequest();
    }

    // not used
    private static function prettyPrintException(Exception $e)
    {        
        echo '<h1>'.$e->getMessage().'</h1><p>'.$e->getCode().'</p>';        
    }

    // Function to disable caching for CDN
    private static function noCacheHeaders()
    {
        header("Pragma: no-cache");
        header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', strtotime('-1 day')));
    }

    // // Function to enable caching for CDN
    // // $period is time in cache in seconds!
    // private static function addCacheHeaders($period)
    // {
    //     header("Cache-Control: public, max-age=" . $period);
    //     header('Date: ' . gmdate('D, d M Y H:i:s \G\M\T', time()));
    //     header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + $period));
    //     header('Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T', time()));
    // }
}
