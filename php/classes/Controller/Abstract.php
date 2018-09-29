<?php

abstract class Controller_Abstract implements Controller_Interface
{
    /**
     * @var Config
     */
    public $_config;

    /**
     * @var Request
     */
    public $_request;

    /**
     * @var HttpClient
     */
    public $_httpClient;

    /**
     * @var Template
     */
    public $template;

    /**
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->_config = $config;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->_request = $request;
    }

    /**
     * @param Template $template
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    public function getTemplate()
    {
        return $this->template;
    }
}
